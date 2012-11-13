<?php
$page_title = 'Banned - Mod Share';
$result = $db->query('SELECT message,id FROM bans
WHERE (user_id=' . ($ms_user['valid'] ? $ms_user['id'] : 0) . '
	OR ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'
	OR ip LIKE \'%,' . $_SERVER['REMOTE_ADDR'] . '\'
	OR ip LIKE \'%,' . $_SERVER['REMOTE_ADDR'] . ',%\'
	OR IP LIKE \'' . $_SERVER['REMOTE_ADDR'] . ',%\')
	AND expires>' . time()) or error('Failed to check bans', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	$db->query('DELETE FROM bans
	WHERE expires<' . time()) or error('Failed to delete old bans', __FILE__, __LINE__, $db->error());
	unset($_SESSION['banned']);
	header('Location: /'); die;
}
$_SESSION['banned'] = $cur_ban['id'];
$ban_info = $db->fetch_assoc($result);
?>
<h2>Banned</h2>
<p>The Mod Share Team decided to ban your account or IP address.</p>
<p>The person that banned you left you with the following message:<br /><b><?php echo $ban_info['message']; ?></b></p>
<p><a href="/help">Contact us</a> if you wish to be unbanned or have any other questions.</p>
<p>Please do <i>NOT</i> create another account to get around this ban. Instead, please contact us to talk about unbanning this one.</p>