<?php
$page_title = 'Messages and Notifications - Mod Share';
//get notifications
$result = $db->query('SELECT type,message,id FROM notifications
WHERE user=' . $ms_user['id'] . '
ORDER BY type DESC, id DESC') or error('Failed to get notifcations', __FILE__, __LINE__, $db->error());
echo '<div id="notifications">';
if ($db->num_rows($result)) {
	$last_type = -1;
	while ($cur_notification = $db->fetch_assoc($result)) {
		if ($cur_notification['type'] != $last_type) {
			switch($cur_notification['type']) {
				case 0:
					echo '<h2>Messages</h2>';
					break;
				case 1:
					echo '<h2>Admin notifications</h2>';
					break;
			}
			$last_type = $cur_notification['type'];
		}
		echo "\n" . '<p id="notification' . $cur_notification['id'] . '"><a onclick="removeNotification(' . $cur_notification['id'] . ')" style="cursor: pointer;">(x)</a> ' . $cur_notification['message'] . '</p>';
	}
}
echo '</div>';
?>
<script type="text/javascript">
//<![CDATA[
function removeNotification(id) {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("POST", "/ajax/delnotification", true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send("delid=" + id);
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			//success
		} else {
			//failure
		}
	 }
	document.getElementById('notifications').removeChild(document.getElementById('notification' + id));
}
//]]>
</script>