<?php
$page_title = 'Projects - Mod Share';
function defaultPage($ajax = false) {
	global $dirs, $db;
	$out = '';
	// deafult browse page
	$dirs[3] = $dirs[2];
	if(!is_numeric($dirs[3]) || $dirs[3] == '') $dirs[3] = 1;
	
	// output projects as list ordered by date
	$q = 'SELECT p.id,p.title,p.time,p.downloads,p.modification,u.username,u.permission FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by';
	if ($ajax) {
		switch($_POST['order']) {
		case 'date':
			$order = 'p.time'; break;
		case 'creator':
			$order = 'u.username'; break;
		case 'title':
			$order = 'p.title'; break;
		case 'mod':
			$order = 'p.modification'; break;
		default:
			$order = 'p.time';
		}
		switch($_POST['orderway']) {
		case 'asc':
			$order .= ' ASC'; break;
		case 'desc':
			$order .= ' DESC'; break;
		default:
			$order .= ' DESC';
		}
		$q .= '
		WHERE p.status=\'normal\'';
		if (isset($_POST['mod']) && $_POST['mod'] != '') {
			$q .= ' AND p.modification=\'' . $db->escape($_POST['mod']) . '\'';
		}
		if (isset($_POST['date']) && $_POST['date'] != '') {
			$q .= ' AND p.time>' . (time() - (intval($_POST['date']) * 60 * 60 * 24));
		}
		if (isset($_POST['title']) && $_POST['title'] != '') {
			$q .= ' AND p.title LIKE \'%' . $db->escape($_POST['title']) . '%\'';
		}
		$q .= ' ORDER BY ' . $order;
	} else {
		$q .= '
		WHERE p.status=\'normal\'
		ORDER BY p.time DESC LIMIT ' . (intval($dirs[3] - 1) * 20) . ',20';
	}
	$result = $db->query($q) or error('Failed to get projects', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$out .= '<table border="0" style="width: 100%;"><tr><th>&nbsp;</th>
						<th>Project</th>
						<th>Mod</th>
						<th>Date</th>
						<th style="width: 30px;" align="center"><img src="/img/download.png" alt="Downloads" width="24" /></th></tr>';
		while ($cur_project = $db->fetch_assoc($result)) {
			$out .= '<tr><td><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="80px" height="60px" alt="Project icon" /></td>';
			$out .= '<td><a href="/projects/' . clearHTML(rawurlencode($cur_project['username'])) . '/' . $cur_project['id'] . '" style="font-weight: bold;">'
					. clearHTML($cur_project['title']) . '</a><br />by ' . parse_username($cur_project) . '</td>'
					. '<td>' . getMod($cur_project['modification']). '</td>'
					. '<td>' . date('d/m/y', $cur_project['time']) . '<br />' . date('H:i', $cur_project['time']) . '</td>'
					. '<td style="text-align: center;">' . $cur_project['downloads'] . '</td></tr>';
		}
		$out .= '</table>';
	}
	return $out;
}
if (isset($_POST['ajax'])) {
	ob_end_clean();
	echo defaultPage(true); 
	die;
}

if(!is_numeric($dirs[3]) || $dirs[3] == '') $dirs[3] = 1;

if($dirs[2] == 'latest') {
	$titleappend = ' - Latest';
	$result = $db->query('SELECT p.id,p.title,p.description,p.downloads,p.modification,u.username,u.permission FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by
	WHERE p.status=\'normal\'
	ORDER BY p.time DESC LIMIT ' . (intval($dirs[3] - 1) * 15) . ',15') or error('Failed to get projects', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$out = '<table border="0">';
		while ($cur_project = $db->fetch_assoc($result)) {
			$out .= '<tr><td><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /></td>';
			$out .= '<td style="vertical-align: top;"><a href="/projects/' . clearHTML(rawurlencode($cur_project['username'])) . '/' . $cur_project['id'] . '" style="font-weight: bold;">'
					. clearHTML($cur_project['title']) . '</a> by ' . parse_username($cur_project) . '<br />'
					. '<em>' . clearHTML(substr($cur_project['description'], 0, 120)) . '...</em>' . '<br />'
					. 'Downloads: ' . $cur_project['downloads'] . ' &bull; <strong>' . getMod($cur_project['modification']). '</strong></td></tr>';
		}
		$out .= '</table>';
		if($dirs[3] > 1) $out .= '<a href="/browse/latest/' . intval($dirs[3] - 1) . '">Previous</a>&nbsp;&bull;&nbsp;';
		$out .= '<a href="/browse/latest/' . intval($dirs[3] + 1) . '">Next</a>';
	} else {
		$out = 'No projects found in range ' . (intval($dirs[3] - 1) * 15) . '-' . (intval($dirs[3]) * 15);
	}
	
} elseif($dirs[2] == 'featured') {
	$titleappend = ' - Featured';
	$result = $db->query('SELECT p.id,p.title,p.description,p.downloads,p.modification,u.username,u.permission FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by
	WHERE p.status=\'normal\'
	AND p.featured IS NOT NULL
	ORDER BY p.featured DESC LIMIT ' . (intval($dirs[3] - 1) * 10) . ',10') or error('Failed to get projects', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$out = '<table border="0">';
		while ($cur_project = $db->fetch_assoc($result)) {
			$out .= '<tr><td><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /></td>';
			$out .= '<td style="vertical-align: top;"><a href="/projects/' . clearHTML(rawurlencode($cur_project['username'])) . '/' . $cur_project['id'] . '" style="font-weight: bold;">'
					. clearHTML($cur_project['title']) . '</a> by ' . parse_username($cur_project) . '<br />'
					. '<em>' . clearHTML(substr($cur_project['description'], 0, 120)) . '...</em>' . '<br />'
					. 'Downloads: ' . $cur_project['downloads'] . ' &bull; <strong>' . getMod($cur_project['modification']). '</strong></td></tr>';
		}
		$out .= '</table>';
		if($dirs[3] > 1) $out .= '<a href="/browse/featured/' . intval($dirs[3] - 1) . '">Previous</a>&nbsp;&bull;&nbsp;';
		$out .= '<a href="/browse/featured/' . intval($dirs[3] + 1) . '">Next</a>';
	} else {
		$out = 'No featured projects found in range ' . (intval($dirs[3] - 1) * 10) . '-' . (intval($dirs[3]) * 10);
	}	
	
	
} elseif($dirs[2] == 'top-downloaded') {
	$titleappend = ' - Top downloads';
	$result = $db->query('SELECT p.id,p.title,p.description,p.downloads,p.modification,u.username,u.permission FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by
	WHERE p.status=\'normal\'
	ORDER BY p.downloads DESC LIMIT ' . (intval($dirs[3] - 1) * 15) . ',15') or error('Failed to get projects', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$out = '<table border="0">';
		while ($cur_project = $db->fetch_assoc($result)) {
			$out .= '<tr><td><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /></td>';
			$out .= '<td style="vertical-align: top;"><a href="/projects/' . clearHTML(rawurlencode($cur_project['username'])) . '/' . $cur_project['id'] . '" style="font-weight: bold;">'
					. clearHTML($cur_project['title']) . '</a> by ' . parse_username($cur_project) . '<br />'
					. '<em>' . clearHTML(substr($cur_project['description'], 0, 120)) . '...</em>' . '<br />'
					. 'Downloads: ' . $cur_project['downloads'] . ' &bull; <strong>' . getMod($cur_project['modification']). '</strong></td></tr>';
		}
		$out .= '</table>';
		if($dirs[3] > 1) $out .= '<a href="/browse/featured/' . intval($dirs[3] - 1) . '">Previous</a>&nbsp;&bull;&nbsp;';
		$out .= '<a href="/browse/featured/' . intval($dirs[3] + 1) . '">Next</a>';
	} else {
		$out = 'No projects found in range ' . (intval($dirs[3] - 1) * 15) . '-' . (intval($dirs[3]) * 15);
	}	
	
} elseif($dirs[2] == 'all') {
	$titleappend = ' - Browse all';
	$result = $db->query('SELECT p.id,p.title,u.username,u.permission FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by
	WHERE p.status=\'normal\'
	ORDER BY p.time DESC') or error('Failed to get projects', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$out = '<table border="0">
		<tr>';
		$count = 0;
		while ($cur_project = $db->fetch_assoc($result)) {
			if ($count % 5 == 0 && $count > 0) {
				$out .= '</tr><tr>';
			}
			$count++;
			$out .= '
						<td style="text-align:center; width: 150px; vertical-align:top">
							<a href="/projects/' . clearHTML(rawurlencode($cur_project['username'])) . '/' . $cur_project['id'] . '">
								<img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /><br />
								' . clearHTML($cur_project['title']) . '</a><br />
								By ' . parse_username($cur_project) . '
						</td>
			';
		}
		$out .= '</tr>
	</table>';
	} else {
		$out = 'No projects found';
	}
} else {
	?>
	<script type="text/javascript">
	//<![CDATA[
	function search() {
		var dateSearch = encodeURIComponent(document.getElementById('dateSelect').value);
		var mod = encodeURIComponent(document.getElementById('mod').value);
		var title = encodeURIComponent(document.getElementById('title').value);
		var order = encodeURIComponent(document.getElementById('order').value);
		var orderway = encodeURIComponent(document.getElementById('orderway').value);
		
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		} else {
			req = new ActiveXObject("Microsoft.XMLHTTP");
		}
		document.getElementById('output').innerHTML = 'Working...';
		req.open("POST", "/browse", true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		req.send('date=' + dateSearch + '&mod=' + mod + '&title=' + title + '&order=' + order + '&orderway=' + orderway + '&ajax=true');
		
		req.onreadystatechange = function() {
			if (req.readyState==4 && req.status==200) {
				document.getElementById('output').innerHTML = req.responseText;
			} else {
				document.getElementById('output').innerHTML = 'Error: ' + req.status;
			}
		}
	}
	//]]>
	</script>
	<?php
	echo '<div id="searchtools"><p><strong>Filter projects:</strong>
			<select name="mod" id="mod"><option label="Mod" disabled="disabled" selected="selected" />';
			echo '<option value="">Any mod</a>';
			foreach ($modlist as $key => $val) {
				echo '<option value="' . $key . '">' . strip_tags($val['name']) . '</option>';
			}
			echo '</select>
			
			<select name="date" id="dateSelect">
				<option label="Recently uploaded" value="2" disabled="disabled" selected="selected" />
				<option label="Today" value="1" />
				<option label="Yesterday" value="2" />
				<option label="Up to 1 week" value="7" />
				<option label="Up to 2 weeks" value="14" />
				<option label="This month" value="30" />
				<option label="Any time" value="" />
			</select>
			
			Title contains <input name="title" id="title" type="text" size="10" maxlength="20" />
			
			<br />
			
			<strong>Order by:</strong>
			<select name="order" id="order">
				<option label="Date" value="date" selected="selected" />
				<option label="Creator" value="creator" />
				<option label="Title" value="title" />
				<option label="Mod" value="mod" />
				</select>
			<select name="orderway" id="orderway">
				<option label="ascending" value="asc" />
				<option label="descending" value="desc" selected="selected" />
				</select>
			<input type="submit" value="Go" onclick="search()" />
			</p></div>';
	$out = defaultPage();
}

echo '<h2>Projects on Mod Share' . $titleappend . '</h2>';
echo '<p><a href="/browse/latest">Latest</a> &bull; <a href="/browse/featured">Featured</a> &bull; <a href="/browse/top-downloaded">Top downloaded</a> &bull; <a href="/browse/all">All projects</a></p>
		<hr />';
echo '<div id="output">';
echo $out;
echo '</div>';