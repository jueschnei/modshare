<?php
$page_title = 'Send admin notification';
$userid = $dirs[3];
$result = $db->query('SELECT username FROM users
WHERE id=' . intval($userid)) or error('Failed to get user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	echo 'User does not exist.';
	return;
}
$user_info = $db->fetch_assoc($result);
if (isset($_POST['form_sent'])) {
	$db->query('INSERT INTO notifications(user,type,message)
	VALUES(' . intval($userid) . ',1,\'' . $db->escape($_POST['message']) . '\')') or error('Failed to send notification', __FILE__, __LINE__, $db->error());
	
	addlog('Notified user ' . $userid);
	$db->query('INSERT INTO adminhistory(to_user,from_user,time,action)
	VALUES(' . intval($userid) . ',' . $ms_user['id'] . ',' . time() . ',\'' . $db->escape('Admin notification: \'' . $_POST['message'] . '\'') . '\')') or error('Failed to log admin action', __FILE__, __LINE__, $db->error());
	echo '<p>Notification "' . $_POST['message'] . '" sent to ' . $user_info['username'] . ' successfully!<br /><a href="/users/' . $user_info['username'] . '">Return to user page</a></p>';
	return;
}
?>
<h2>Admin notifications</h2>
<?php
$result = $db->query('SELECT 1 FROM adminhistory
WHERE to_user=' . intval($userid) . '
AND (action LIKE \'Admin notification:%\' OR action=\'Flagged an unnecessary project.\')
AND time>' . (time() - 60 * 60 * 24 * 30)) or error('Failed to check for recent admin history', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result) >= 2) {
	echo '<p>Hmm. This user has already received a couple admin notifications lately. <a href="/admin/history/' . intval($userid) . '">Want to check them out?</a></p>';
} else if ($db->num_rows($result) >= 1) {
	echo '<p>Hmm. This user has already received an admin notification in the past month. <a href="/admin/history/' . intval($userid) . '">Want to check it out?</a></p>';
}
?>
<p>Sending to <?php echo clearHTML($user_info['username']); ?></p>
<form action="/admin/notify/<?php echo $userid; ?>" method="post" enctype="multipart/form-data">
	Message<br />
	<p><textarea type="text" name="message" rows="6" cols="80"></textarea></p>
	<p><input type="submit" name="form_sent" value="Send" /></p>
</form>