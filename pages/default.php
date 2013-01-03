<?php
$page_title = 'Mod Share IV';
?>
<p>&nbsp;</p>
<table width="100%" border="0">
  <tr>
    <td><img src="/img/logo.png" alt="Mod Share Logo" width="140" height="140" /></td>
    <td><p>Welcome to Mod Share, where impossibility becomes reality!</p>
    <p>The Scratch license doesn't allow uploading projects made in <a href="/mods">mods</a> onto the Scratch Website. That is why Mod Share was created to provide you with a place to <em>upload your creativity</em> !</p>
    <p>Check out the hundreds of <a href="/browse">uploaded projects</a> or <a href="/upload">share your own</a> creation!</p></td>
  </tr>
</table>
<?php
$result = $db->query('SELECT p.id,p.title,u.username,u.permission FROM projects AS p
LEFT JOIN users AS u
ON u.id=p.uploaded_by
WHERE p.status=\'normal\'
ORDER BY p.time DESC
LIMIT 3') or error('Failed to get latest projects', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
?>
<h2>Latest projects</h2>
<table border="0">
	<tr>
<?php
while ($cur_project = $db->fetch_assoc($result)) {
	echo '<td style="text-align:center; max-width: 130px; vertical-align:top"><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '"><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /><br />' . clearHTML($cur_project['title']) . '</a><br />By ' . parse_username($cur_project) . '</td>';
}
?>
	</tr>
</table>
<?php } ?>
<?php
$result = $db->query('SELECT p.id,p.title,u.username,u.permission FROM projects AS p
LEFT JOIN users AS u
ON u.id=p.uploaded_by
WHERE p.status<>\'deleted\'
AND p.featured IS NOT NULL
ORDER BY p.featured DESC
LIMIT 3') or error('Failed to get featured projects', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
?>
<h2>Featured projects</h2>
<table border="0">
	<tr>
<?php
while ($cur_project = $db->fetch_assoc($result)) {
	echo '<td style="text-align:center; max-width: 130px;"><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '"><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /><br />' . clearHTML($cur_project['title']) . '</a><br />By ' . parse_username($cur_project) . '</td>';
}
?>
	</tr>
</table>
<?php } ?>
<?php if ($ms_user['valid']) {
	$result = $db->query('SELECT friendee FROM friends
	WHERE friender=' . $ms_user['id']) or error('Failed to get friends list', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$friends = array();
		while ($friend_info = $db->fetch_assoc($result)) {
			$friends[] = $friend_info['friendee'];
		}
		$result = $db->query('SELECT p.id,p.title,username,u.permission FROM projects AS p
		LEFT JOIN users AS u
		ON u.id=p.uploaded_by
		WHERE p.uploaded_by IN(' . implode(',', $friends) . ')
		LIMIT 3') or error('Failed to get friends&apos; latest projects', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result)) { ?>
		<h2>My friends&apos; latest projects</h2>
		<table border="0">
			<tr>
		<?php
		while ($cur_project = $db->fetch_assoc($result)) {
			echo '<td style="text-align:center; max-width: 130px;"><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '"><img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /><br />' . clearHTML($cur_project['title']) . '</a><br />By ' . parse_username($cur_project) . '</td>';
		}
		?>
			</tr>
		</table>
		<?php
		}
	}
}
?>