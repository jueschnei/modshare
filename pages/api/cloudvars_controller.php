<?php
$content_type = 'text/plain';
if ($dirs[3] == 'get') {
	$result = $db->query('SELECT value FROM cloudvars
	WHERE name=\'' . $db->escape($dirs[4]) . '\'') or error('Failed to get variable', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$var_info = $db->fetch_assoc($result);
		echo $var_info['value'];
	} else {
		echo 'Variable not found';
	}
} else if ($dirs[3] == 'set') {
	$result = $db->query('SELECT value FROM cloudvars
	WHERE name=\'' . $db->escape($_POST['name']) . '\'') or error('Failed to get variable', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$db->query('UPDATE cloudvars
		SET value=\'' . $db->escape($_POST['val']) . '\'
		WHERE name=\'' . $db->escape($_POST['name']) . '\'') or error('Failed to update variable', __FILE__, __LINE__, $db->error());
	} else {
		$db->query('INSERT INTO cloudvars(name,value)
		VALUES(\'' . $db->escape($_POST['name']) . '\',\'' . $db->escape($_POST['val']) . '\')') or error('Failed to insert variable', __FILE__, __LINE__, $db->error());
	}
} else if ($dirs[3] == 'getnewid') {
	echo $ms_config['lastprojectid'] + 1;
	set_config('lastprojectid', $ms_config['lastprojectid'] + 1);
} else {
	header('HTTP/1.1 400 Bad request');
	echo 'Bad request';
}