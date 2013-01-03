<?php
$result = $db->query('SELECT name,creator,id,contributors FROM ' . $db->prefix . 'galleries WHERE url=\'' . $db->escape($dirs[2]) . '\'') or error('Failed to locate gallery', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	ob_end_clean();
	header('HTTP/1.1 404 Not found');
	include SRV_ROOT . '/errorpages/404.php';
	die;
}
$gallery = $db->fetch_assoc($result);
$page_title = clearHTML($gallery['name']) . ' - Galleries - Mod Share';
if (strstr($gallery['contributors'], '|' . $ms_user['id'])) {
	$add = true;
}
if ($ms_user['id'] == $gallery['creator']) {
	$add = true;
	$owner = true;
}
if ($dirs[3] == 'delete' && $owner) {
	$db->query('DELETE FROM galleries WHERE id=' . $gallery['id']) or error('Failed to delete gallery', __FILE__, __LINE__, $db->error());
	$db->query('DELETE FROM gallery_projects WHERE gallery_id=' . $gallery['id']) or error('Failed to delete gallery projects', __FILE__, __LINE__, $db->error());
	header('Location: /users/' . $ms_user['username']); die;
}
if (isset($_GET['removeproject']) && $owner) {
	$db->query('DELETE FROM gallery_projects WHERE project_id=' . intval($_GET['removeproject']) . ' AND gallery_id=' . $gallery['id']) or error('Failed to remove project', __FILE__, __LINE__, $db->error());
}
if (isset($_POST['updatecontribs']) && $owner) {
	if (!isset($_POST['updatecontribs'])) {
		$_POST['updatecontribs'] = array();
	}
	$db->query('UPDATE galleries SET contributors=\'' . $db->escape('|' . implode('|', $_POST['contribs']) . '|') . '\' WHERE id=' . $gallery['id']) or error('Failed to update contributors', __FILE__, __LINE__, $db->error());
	header('Refresh: 0'); return;
}
echo '<h2>Gallery: ' . clearHTML($gallery['name']) . '</h2>';
if ($owner) {
	echo '<p><a href="/galleries/' . $dirs[2] . '/delete">Delete gallery</a></p>';
	$result = $db->query('SELECT u.id,u.username FROM friends AS f LEFT JOIN users AS u ON u.id=f.friendee WHERE f.friender=' . $ms_user['id']) or error('Failed to get friends', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		echo '<h3>Edit contributors</h3>';
		echo '<form action="/galleries/' . clearHTML($dirs[2]) . '" method="post" enctype="multipart/form-data">';
		echo '<table border="0">';
		while (list($id,$username) = $db->fetch_row($result)) {
			echo '<tr><td><input type="checkbox" name="contribs[' . $id . ']" value="' . $id . '"';
			if (strstr($gallery['contributors'], '|' . $id . '|')) {
				echo ' checked="checked"';
			}
			echo '/></td><td>' . clearHTML($username) . '</td></tr>';
		}
		echo '</table>';
		echo '<p><input type="submit" name="updatecontribs" value="Update" /></p>';
		echo '</form>';
	}
}
$result = $db->query('SELECT p.id,p.title,p.modification,p.time,p.downloads,u.username,u.permission FROM gallery_projects AS gp LEFT JOIN projects AS p ON p.id=gp.project_id LEFT JOIN users AS u ON u.id=p.uploaded_by WHERE gp.gallery_id=' . $gallery['id'] . ' ORDER BY gp.added DESC') or error('Failed to get gallery projects', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
	echo '<table border="0" style="width: 100%;">
		<tr style="text-align:center">
			<th>&nbsp;</th>
			<th>Project</th>
			<th>Mod</th>
			<th>Date</th>
			<th style="width: 30px;" align="center"><img src="/img/download.png" alt="Downloads" width="24" /></th>';
		if ($owner) {
			echo '<th>Remove</th>';
		}
		echo '</tr>';
	while ($cur_project = $db->fetch_assoc($result)) {
		echo '<tr>
			<td><img src="/data/icons/project/' . $cur_project['id'] . '.png" alt="project icon" width="80px" height="60px" /></td>
			<td><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '" style="font-weight:bold">' . clearHTML($cur_project['title']) . '</a><br />' . parse_username($cur_project) . '</td>
			<td>' . getMod($cur_project['modification']) . '</td>
			<td>' . format_date($cur_project['time']) . '</td>
			<td style="text-align:center">' . $cur_project['downloads'] . '</td>
			<td style="text-align:center"><a href="/galleries/' . clearHTML($dirs[2]) . '?removeproject=' . $cur_project['id'] . '">[X]</a></td>
		</tr>';
	}
	echo '</table>';
}
