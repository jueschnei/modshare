<?php
session_regenerate_id();
$page_title = 'Banned - Mod Share';
$result = $db->query('SELECT message FROM bans
WHERE (user_id=' . ($ms_user['valid'] ? $ms_user['id'] : 0) . ' OR ip=\'' . $_SERVER['REMOTE_ADDR'] . '\')
AND expires>' . time()) or error('Failed to check bans', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	header('Location: /'); die;
}
$ban_info = $db->fetch_assoc($result);
?>
<h2>Banned</h2>
<p>The Mod Share Team decided to ban your account or IP address.</p>
<p>The person that banned you left you with the following message:<br /><b><?php echo $ban_info['message']; ?></b></p>
<p>Please <a href="/help">contact us</a> if you have any questions.</p>
<p>Here is a short description of what we do when receiving a message asking to be unbanned: <br />
<img src="/img/banprocess.gif" width="619" height="215" alt="Process of unbanning a user" /></p>