<?php

$page_title = 'Send admin notification';

$uid = $dirs[3];

$result = $db->query('SELECT username FROM users

WHERE id=' . intval($uid)) or error('Failed to get user info', __FILE__, __LINE__, $db->error());

if (!$db->num_rows($result)) {

	echo 'User does not exist.';

	return;

}
if (isset($_POST['form_sent'])) {
	$db->query('INSERT INTO notifications(user,type,message)
	VALUES(' . intval($uid) . ',1,\'' . $db->escape($_POST['message']) . '\')') or error('Failed to send notification', __FILE__, __LINE__, $db->error());
	
	addlog('Notified user ' . $uid);
	$db->query('INSERT INTO adminhistory(to_user,from_user,time,action)

	VALUES(' . intval($uid) . ',' . $ms_user['id'] . ',' . time() . ',\'' . $db->escape('Admin notification: \'' . $_POST['message'] . '\'') . '\')') or error('Failed to log admin action', __FILE__, __LINE__, $db->error());

}

$user_info = $db->fetch_assoc($result);

?>

<h2>Admin notifications</h2>
<p>Sending to <?php echo clearHTML($user_info['username']); ?></p>
<form action="/admin/notify/<?php echo $uid; ?>" method="post" enctype="multipart/form-data">
	Message<br />
	<input type="text" name="message" size="50" /><br />
	<input type="submit" name="form_sent" value="Send" />
</form>