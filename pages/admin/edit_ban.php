<?php
$page_title = 'Edit ban - Mod Share';
if (isset($_POST['form_sent'])) {
	$db->query('UPDATE bans
	SET ip=\'' . $db->escape($_POST['ip']) . '\',message=\'' . $db->escape($_POST['message']) . '\',expires=' . strtotime($_POST['expire'] . ' GMT' . ($ms_user['timezone'] >= 0 ? '+' : '') . $ms_user['timezone']) . ',type=\'' . $db->escape($_POST['type']) . '\'
	WHERE id=' . intval($dirs[3])) or error('Failed to update ban', __FILE__, __LINE__, $db->error());
}
if (isset($_POST['remove_ban'])) {
	$result = $db->query('SELECT user_id FROM bans
	WHERE id=' . intval($dirs[3])) or error('Failed to get ban info', __FILE__, __LINE__, $db->error());
	$ban_info = $db->fetch_assoc($result);
	$db->query('DELETE FROM bans
	WHERE id=' . intval($dirs[3])) or error('Failed to delete ban', __FILE__, __LINE__, $db->error());
	if ($ban_info['user_id'] > 0) {
		$db->query('INSERT INTO adminhistory(to_user,from_user,time,action)
		VALUES(' . $ban_info['user_id'] . ',' . $ms_user['id'] . ',' . time() . ',\'' . $db->escape('Ban manaully removed') . '\')') or error('Failed to log admin action', __FILE__, __LINE__, $db->error());
	}
	
	addlog('User ban updated for ' . $ban_info['user_id']);
	header('Location: /admin/bans'); die;
}
$result = $db->query('SELECT b.user_id,b.ip,b.message,b.expires,b.type,u.username FROM bans AS b
LEFT JOIN users AS u
ON u.id=b.user_id
WHERE b.id=' . intval($dirs[3])) or error('Failed to get ban info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	echo 'Ban does not exist';
	return;
}
$cur_ban = $db->fetch_assoc($result);
?>
<h2>Edit a ban</h2>
<form action="/admin/edit_ban/<?php echo intval($dirs[3]); ?>" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Username</td>
			<td><?php echo clearHTML($cur_ban['username']); ?></td>
		</tr>
		<tr>
			<td>IP addresses<br />Separate with commas</td>
			<td><input type="text" name="ip" value="<?php echo clearHTML($cur_ban['ip']); ?>" size="50" /></td>
		</tr>
		<tr>
			<td>Message</td>
			<td><textarea name="message" rows="5" cols="100"><?php echo clearHTML($cur_ban['message']); ?></textarea></td>
		</tr>
		<tr>
			<td>Expires</td>
			<td><input type="text" name="expire" value="<?php echo format_date($cur_ban['expires']); ?>" /></td>
		</tr>
		<tr>
			<td>Type</td>
			<td><select name="type"><option value="full" <?php if ($cur_ban['type'] == 'full') echo ' selected="selected"'; ?>>Everything</option><option value="login" <?php if ($cur_ban['type'] == 'login') echo ' selected="selected"'; ?>>Login/registration</option></select></td>
		</tr>
	</table>
	<input type="submit" value="Update ban" name="form_sent" />
	<input type="submit" value="Remove ban" name="remove_ban" />
</form>