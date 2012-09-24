<?php
$page_title = 'Ban user - Mod Share';
$result = $db->query('SELECT id FROM bans
WHERE user_id=' . intval($dirs[3]) . ' AND expires>' . time()) or error('Could not check for existing bans', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result) && $dirs[3] != 0) {
	$info = $db->fetch_assoc($result);
	header('Location: /admin/edit_ban/' . $info['id']); die;
}
if (isset($_POST['form_sent'])) {
	$expire = strtotime($_POST['expire']);
	$daysdiff = floor(($expire - time()) / 60 / 60 / 24);
	//create ban
	$db->query('INSERT INTO bans(user_id,ip,expires,message)
	VALUES(' . intval($dirs[3]) . ',\'' . $db->escape($_POST['ip']) . '\',' . $expire . ',\'' . $db->escape($_POST['message']) . '\')') or error('Failed to add ban', __FILE__, __LINE__, $db->error());
	$db->query('INSERT INTO adminhistory(to_user,from_user,time,action)
	VALUES(' . intval($dirs[3]) . ',' . $ms_user['id'] . ',' . time() . ',\'' . $db->escape('Banned for ' . $daysdiff . ' days with the message "' . $_POST['message'] . '"') . '\')') or error('Failed to log admin action', __FILE__, __LINE__, $db->error());
	
	addlog('User ban updated for ' . intval($dirs[3]));
	
	
	header('Location: /admin/bans'); die;
}
$result = $db->query('SELECT username FROM users
WHERE id=' . intval($dirs[3])) or error('Failed to get user info', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
	$user_info = $db->fetch_assoc($result);
} else if ($dirs[3] == 0) {
	$user_info['username'] = 'No username';
} else {
	echo 'User does not exist.';
	return;
}
?>
<h2>Add a ban</h2>
<form action="/admin/ban_user/<?php echo $dirs[3]; ?>" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" value="<?php echo clearHTML($user_info['username']); ?>" readonly="readonly" /></td>
		</tr>
		<tr>
			<td>IP</td>
			<td><input type="text" name="ip" /><!--add last seen IP here at some point--></td>
		</tr>
		<tr>
			<td>Message</td>
			<td><input type="text" name="message" /></td>
		</tr>
		<tr>
			<td>Expires (e.g. <?php echo date('d M Y'); ?>)</td>
			<td><input type="text" name="expire" value="<?php echo date('d M Y', time() + 60 * 60 * 24 * 4); ?>" /></td>
		</tr>
	</table>
	<input type="submit" name="form_sent" value="Add ban" />
</form>