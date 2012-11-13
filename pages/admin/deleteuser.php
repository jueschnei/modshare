<?php
$page_title = 'Delete user - Mod Share';
$user_id = intval($dirs[3]);
$result = $db->query('SELECT username,status FROM users
WHERE id=' . $user_id) or error('Failed to get user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	echo 'User does not exist';
	return;
}
$user_info = $db->fetch_assoc($result);
if (strstr($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI'])) {
	if ($user_info['status'] == 'disabledbyadmin') {
		$db->query('UPDATE users
		SET status=\'normal\'
		WHERE id=' . $user_id) or error('Failed to delete user', __FILE__, __LINE__, $db->error());
	} else {
		$db->query('UPDATE users
		SET status=\'disabledbyadmin\'
		WHERE id=' . $user_id) or error('Failed to delete user', __FILE__, __LINE__, $db->error());
	}
	
	addlog('User ' . $user_id . ' disabled');
	header('Location: /users/' . rawurlencode($user_info['username']));
	die;
}
?>
<h2><?php if ($user_info['status'] == 'normal') echo 'D'; else echo 'Und'; ?>elete user <?php echo clearHTML($user_info['username']); ?></h2>
<p>Are you sure?</p>
<p><a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Yes</a> &bull; <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">No</a></p>