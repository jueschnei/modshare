<?php
$page_title = 'Search results - Mod Share';
if (!isset($_GET['q']) || $_GET['q'] == '') {
	echo '<form action="/search" method="get"><p><strong>Filter projects:</strong>
	<select name="mod"><option label="Mod" disabled="disabled" selected="selected" />';
	foreach ($modlist as $key => $val) {
		echo '<option value="' . $key . '">' . strip_tags($val['name']) . '</option>';
	}
	echo '</select>
	
	<select name="date"><option label="Recently uploaded" disabled="disabled" selected="selected" />
		<option label="Today" value="1" />
		<option label="Yesterday" value="2" />
		<option label="Up to 1 week" value="7" />
		<option label="Up to 2 weeks" value="14" />
		<option label="This month" value="30" /></select>
		
	<br />
	
	<!--<strong>Order by:</strong>
	<select name="order">
		<option label="Date" value="date" selected="selected" />
		<option label="Creator" value="creator" />
		<option label="Title" value="title" />
		<option label="Mod" value="mod" />
		</select>
	<select name="orderway">
		<option label="ascending" value="asc" />
		<option label="descending" value="desc" selected="selected" />
		</select>-->
	</p>
	<p>Query: <input type="text" name="q" /></p>
	</form>';
	return;
}
//add any additional query parameters
$appendwhere = '';
if (isset($_GET['mod'])) {
	$appendwhere .= ' AND p.modification LIKE \'' . $db->escape(str_replace('-', '%', $_GET['mod'])) . '\'';
}
if (isset($_GET['date'])) {
	$appendwhere .= ' AND p.time>' . (time() - 60 * 60 * 24 * intval($_GET['date']));
}

//prepare search
$words = split_into_words($_GET['q']);
foreach ($words as &$val) {
	$val = '\'' . $db->escape($val) . '\'';
}

if ($_GET['format'] == 'squeak') {
	ob_end_clean();
	ob_start();
	$page_info['header'] = false;
}

$result = $db->query('SELECT u.username,u.permission,p.title,p.description,p.id,si.word,p.status,p.modification,p.downloads FROM search_index AS si
LEFT JOIN projects AS p
ON p.id=si.project
LEFT JOIN users AS u
ON u.id=p.uploaded_by
WHERE si.word IN (' . implode(',', $words) . ')' . ' AND p.status=\'normal\'
' . $appendwhere) or error('Failed to get results', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	if ($_GET['format'] == 'squeak') {
		echo 'no results';
	} else {
		//no results, check for other possible words
		echo '<p>No results</p>';
		$escapedwords = array();
		foreach ($words as $val) {
			$escapedwords[] = $val;
		}
		$result = $db->query('SELECT replacements FROM search_index_mistakes WHERE word IN(' . implode(',', $escapedwords) . ')') or error('Failed to get possible replacements', __FILE__, __LINE__, $db->error());
		$similar = array();
		if ($db->num_rows($result)) {
			while (list($replacements) = $db->fetch_row($result)) {
				$replacements = explode(',', $replacements);
				foreach ($replacements as $val) {
					$similar[] = '<a href="/search?q=' . clearHTML(rawurlencode($val)) . '">' . clearHTML($val) . '</a>';
				}
			}
		} else {
			$newwords = array();
			$result = $db->query('SELECT distinct(word) FROM search_index') or error('Failed to get old search terms', __FILE__, __LINE__, $db->error());
			while (list($word) = $db->fetch_row($result)) {
				foreach ($words as $val) {
					similar_text($word, $val, $percent);
					if ($percent > 50) {
						$similar[] = '<a href="/search?q=' . clearHTML(rawurlencode($word)) . '">' . clearHTML($word) . '</a>';
						$newwords[substr($val, 1, strlen($val) - 2)] .= ',' . $word;
					}
				}
			}
			foreach ($newwords as &$val) {
				$val = substr($val, 1);
			}
			$items = array();
			foreach ($newwords as $key => $val) {
				$items[] = '(\'' . $db->escape($key) . '\',\'' . $db->escape($val) . '\')';
			}
			if (sizeof($items)) {
				$db->query('INSERT INTO search_index_mistakes(word,replacements) VALUES' . implode(',', $items) . '') or error('Failed to insert cache correction', __FILE__, __LINE__, $db->error());
			}
		}
		
		if (sizeof($similar)) {
			echo '<h3>Did you mean any of these?</h3>';
			echo '<ul><li>' . implode('</li><li>', $similar) . '</li></ul>';
		}
	}
} else {
	$out = array();

	while ($cur_project = $db->fetch_assoc($result)) {
		if (!array_key_exists($cur_project['id'], $out)) {
			if ($_GET['format'] == 'squeak') {
				$out[$cur_project['id']] = array('info' => $cur_project['title'], 'matches' => 1);
			} else {
				$out[$cur_project['id']] = array(
					'info' => '<tr><td><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /></td>' .
					'<td style="vertical-align: top;"><a href="/projects/' . clearHTML(rawurlencode($cur_project['username'])) . '/' . $cur_project['id'] . '" style="font-weight: bold;">'
					. clearHTML($cur_project['title']) . '</a> by ' . parse_username($cur_project) . '<br />'
					. '<em>' . clearHTML(substr($cur_project['description'], 0, 120)) . '...</em>' . '<br />'
					. 'Downloads: ' . $cur_project['downloads'] . ' &bull; <strong>' . getMod($cur_project['modification']). '</strong></td></tr>',
					'matches' => 1
				);
			}
		} else {
			$out[$cur_project['id']]['matches']++;
		}
	}

	function rel_cmp($a, $b) {
		if ($a['matches'] == $b['matches']) {
			return 0;
		} else if ($a['matches'] < $b['matches']) {
			return 1;
		} else if ($a['matches'] > $b['matches']) {
			return -1;
		}
	}
	if (!uasort($out, 'rel_cmp')) {
		error('Failed to sort array', __FILE__, __LINE__, $db->error());
	}
	/*foreach ($out as $key => &$val) {
		if ($val['matches'] < sizeof($words) - 1) {
			unset($out[$key]);
		}
	}*/
		
	$stuff = array();
	if ($_GET['format'] == 'squeak') {
		ob_end_clean();
		$outstr = '';
		foreach ($out as $key => $val) {
			$outstr .= ':' . $key . '|' . $val['info'];
		}
		echo substr($outstr,1);
		$db->close();
		die;
	} else {
		echo '<table border="0">';
		foreach ($out as $key => $val) {
			echo $val['info'] . "\n";
		}
		echo '</table>';
	}
}