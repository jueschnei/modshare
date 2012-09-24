<?php
$content_type = 'application/x-scratch-project';
$result = $db->query('SELECT project_file,filename FROM projects
WHERE id=' . intval($dirs[2])) or error('Failed to get project', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
	$pinfo = $db->fetch_assoc($result);
	header('Content-disposition: attachment;filename=' . $pinfo['filename']);
	echo $pinfo['project_file'];
} else {
	echo file_get_contents(SRV_ROOT . '/includes/sattic/notfound.sb');
}