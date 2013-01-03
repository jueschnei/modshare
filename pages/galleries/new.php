<?php
$page_title = 'New Gallery - Mod Share';
if (isset($_POST['form_sent'])) {
	function sef_friendly($str) {
		//borrowed from the "Friendly URL" FluxBB mod. See https://fluxbb.org/resources/mods/friendly-url/ for more information.
		include SRV_ROOT . '/includes/lang_url_replace.php';
	
		$forum_reserved_strings = array();
	
		$str = strtr($str, $lang_url_replace);
		$str = strtolower(utf8_decode($str));
		$str = trim(preg_replace(array('/[^a-z0-9\s]/', '/\s+/'), array('', '-'), $str), '-');
	
		foreach ($forum_reserved_strings as $match => $replace)
			if ($str == $match)
				return $replace;
	
		return $str;
	}
	$contribs = '|' . implode('|', $_POST['allow']) . '|';
	$name = $_POST['name'];
	$url = sef_friendly($name);
	$result = $db->query('SELECT 1 FROM galleries WHERE url=\'' . $db->escape($url) . '\'') or error('Failed to check for duplicates', __FILE__, __LINE__, $db->error());
	$i = 0;
	while ($db->num_rows($result)) {
		$i++;
		$result = $db->query('SELECT 1 FROM galleries WHERE url=\'' . $db->escape($url . '-' . $i) . '\'') or error('Failed to check for duplicates', __FILE__, __LINE__, $db->error());
	}
	if ($i > 0) {
		$url .= '-' . $i;
	}
	
	$db->query('INSERT INTO galleries(name,url,contributors,creator) VALUES(\'' . $db->escape($name) . '\',\'' . $db->escape($url) . '\',\'' . $db->escape($contribs) . '\',' . $ms_user['id'] . ')') or error('Failed to create gallery', __FILE__, __LINE__, $db->error());
	header('Location: /galleries/' . $url); die;
}
?>
<form action="/newgallery" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Gallery name</td>
			<td><input type="text" name="name" maxlength="200" /></td>
		</tr>
		<tr>
			<td>Contributors</td>
			<td>
			<?php
			$result = $db->query('SELECT f.friendee AS id,u.username AS username FROM friends AS f LEFT JOIN users AS u ON u.id=f.friendee WHERE friender=' . $ms_user['id']) or error('Failed to get friend list', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				echo '<ul style="list-style:none; margin-left:-40px">';
				while (list($id,$username) = $db->fetch_row($result)) {
					echo '<li><input type="checkbox" name="allow[' . $id . ']" value="' . $id . '" />' . clearHTML($username) . '</li>';
				}
				echo '</ul>';
			}
			?>
			</td>
		</tr>
		<tr>
			<td>Ready?</td>
			<td><input type="submit" name="form_sent" value="Create" /></td>
		</tr>
	</table>
</form>
