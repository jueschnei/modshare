<?php
//$content_type = 'image/png';
$result = $db->query('SELECT thumbnail FROM projects
WHERE id=' . intval($dirs[2])) or error('Failed to get avatar', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
	$info = $db->fetch_assoc($result);
	if ($info['thumbnail'] == '') {
		echo file_get_contents(SRV_ROOT . '/includes/static/defaultavatar.png');
	} else {
		echo $info['thumbnail'];
	}
}