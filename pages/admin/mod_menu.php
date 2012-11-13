<?php
$page_title = 'Moderator Menu - Mod Share';
if (isset($_POST['zap'])) {
	$zapid = intval(key($_POST['zap']));
	$db->query('UPDATE notificationstoadmin
	SET zapped=' . time() . '
	WHERE id=' . $zapid) or error('Failed to zap message', __FILE__, __LINE__, $db->error());
}
?>
<h2>Moderator Tools</h2>
<h3>User management</h3>
<p><a href="/admin/bans">Bans</a></p>
<h3>Project management</h3>
<p><a href="/admin/flags">Reports</a></p>
<h3>Alerts</h3>
<form action="/admin/mod_menu" method="post" enctype="multipart/form-data">
<?php
$result = $db->query('SELECT id,text FROM notificationstoadmin
WHERE zapped=0 OR zapped IS NULL') or error('Failed to get alerts', __FILE__, __LINE__, $db->error());
while (list($id, $text) = $db->fetch_row($result)) {
	echo '<p>' . clearHTML($text, true, false) . '<br />
	<input type="submit" name="zap[' . $id . ']" value="Mark as read" /></p>';
}
?>
</form>