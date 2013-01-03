<?php
$page_title = 'Appeal notification - Mod Share';
$result = $db->query('SELECT message FROM notifications WHERE id=' . intval($dirs[2]) . ' AND type=1 AND user=' . $ms_user['id']) or error('Failed to check notification', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	echo '<p>Notification does not exist or is automated (and therefore can not be appealed)</p>';
	return;
}
list($message) = $db->fetch_row($result);
if (isset($_POST['form_sent'])) {
	$db->query('INSERT INTO notificationstoadmin(text)
	VALUES(\'' . $db->escape($ms_user['username'] . ' appealed the following notification:' . "\n" . $message . "\n\n" . 'The appeal message was:' . "\n" . $_POST['resp']) . '\')') or error('Failed to submit appeal', __FILE__, __LINE__, $db->error());
	$db->query('INSERT INTO adminhistory(to_user,from_user,time,action)
	VALUES(' . $ms_user['id'] . ',' . $ms_user['id'] . ',' . $_SERVER['REQUEST_TIME'] . ',\'' . $db->escape('Appealed the following notification:' . "\n" . $message . "\n\n" . 'The appeal message was:' . "\n" . $_POST['resp']) . '\')') or error('Failed to submit appeal', __FILE__, __LINE__, $db->error());
	echo '<p>Appeal submitted. You may now delete the notification. However, while we retain a copy, it is not accessible to you, so you might want to record it.</p>';
	return;
}

echo '<h2>Appeal notification</h2>';
echo '<h3>Original message</h3>
<p>' . clearHTML($message) . '</p>';
echo '<h3>Your response</h3>
<form action="' . clearHTML($_SERVER['REQUEST_URI']) . '" method="post" enctype="multipart/form-data">
	<p><textarea name="resp" rows="5" cols="50"></textarea></p>
	<p><input type="submit" name="form_sent" value="Submit" /></p>
</form>';