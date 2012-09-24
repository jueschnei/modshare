<?php
$page_title = 'Mod Share IV';
?>
<p>Welcome to Mod Share, where impossibility becomes reality!</p>
<p>According to the Scratch Source Code license, you can't upload projects made in mods onto the Scratch Website. That is why Mod Share is here, so you do have a place to upload them!</p>
<p><img src="/img/logo.png" alt="Mod Share Logo" /></p>
<?php
$result = $db->query('SELECT p.id,p.title,p.thumbnail,u.username FROM projects AS p
LEFT JOIN users AS u
ON u.id=p.uploaded_by
WHERE p.status<>\'deleted\'
ORDER BY p.time DESC
LIMIT 0,3') or error('Failed to get latest projects', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
?>
<h2>Latest projects</h2>
<table border="0">
	<tr>
<?php
while ($cur_project = $db->fetch_assoc($result)) {
	echo '<td style="text-align:center; max-width: 100px;"><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '"><img src="' . dataURI($cur_project['thumbnail']) . '" width="100px" height="100px" alt="Project icon" /><br />' . clearHTML($cur_project['title']) . '</a></td>';
}
?>
	</tr>
</table>
<?php } ?>
<?php
$result = $db->query('SELECT p.id,p.title,p.thumbnail,u.username FROM projects AS p
LEFT JOIN users AS u
ON u.id=p.uploaded_by
WHERE p.status<>\'deleted\'
AND p.featured IS NOT NULL
ORDER BY p.featured DESC
LIMIT 0,3') or error('Failed to get featured projects', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
?>
<h2>Featured projects</h2>
<table border="0">
	<tr>
<?php
while ($cur_project = $db->fetch_assoc($result)) {
	echo '<td style="text-align:center; max-width: 100px;"><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '"><img src="' . dataURI($cur_project['thumbnail']) . '" width="100px" height="100px" alt="Project icon" /><br />' . clearHTML($cur_project['title']) . '</a></td>';
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
		$result = $db->query('SELECT p.id,p.title,p.thumbnail,username FROM projects AS p
		LEFT JOIN users AS u
		ON u.id=p.uploaded_by
		WHERE p.uploaded_by IN(' . implode(',', $friends) . ')
		LIMIT 0,2') or error('Failed to get friends&apos; latest projects', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result)) { ?>
		<h2>My friends&apos; latest projects</h2>
		<table border="0">
			<tr>
		<?php
		while ($cur_project = $db->fetch_assoc($result)) {
			echo '<td style="text-align:center; max-width: 100px;"><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '"><img src="' . dataURI($cur_project['thumbnail']) . '" width="100px" height="100px" alt="Project icon" /><br />' . clearHTML($cur_project['title']) . '</a></td>';
		}
		?>
			</tr>
		</table>
		<?php
		}
	}
}
?>