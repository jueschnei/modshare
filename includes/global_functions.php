<?php
// output an error page with the selected error
if (!$forums) {
	function error($error, $file = '', $line = '', $db_error = '') {
		header('Content-type: text/html; charset=utf-8');
		ob_end_clean();
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<title>Error - Mod Share</title>
		</head>
		<body>
		<p>An error was encountered: <?php echo $error; ?></p>
		<?php
		if (defined('MS_DEBUG')) {
			if ($file != '' && $line != '') {
				echo '<p>In file <b>' . substr($file, 16) . '</b> on line <b>' . $line . '</b></p>';
			}
			if ($db_error != '') {
				echo 'The database reported: <b>' . $db_error . '</b>';
			}
		}
		?>
		</body>
		</html>
		<?php
		addlog('Error ' . $error);
		die;
	}
}

// hash the passwords
function ms_hash($text) {
	return sha1('salt' . $text . 'salt');
}

/* return user info
	int		id			= unique user ID
	bool	valid		= is the user logged on
	string	username	= the username of the user
	
	int		permission	= the permission level of the user
	bool	is_mod		= if the user is a mod
	bool	is_admin	= if the user is an admin
	
	hex		style_col	= the colour of the theme
	string	style_logo	= the logo to display in the header
	
	bool	banned		= is the user banned? 
*/
function check_user(&$ms_user) {	
	global $db;
	$ms_user = array();
	user_is_guest($ms_user);
	$ms_user['banned'] = false;
	if ($_SESSION['uid']) {
		$result = $db->query('SELECT * FROM users
		WHERE id=' . $_SESSION['uid']) or error('Failed to check user info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result)) {
			$ms_user = $db->fetch_assoc($result);
			$ms_user['id'] = $_SESSION['uid'];
			$ms_user['valid'] = true;
			if ($ms_user['permission'] > 1) {
				$ms_user['is_mod'] = true;
			}
			if ($ms_user['permission'] > 2) {
				$ms_user['is_admin'] = true;
			}
		}
	}
}

// set the user info to default values (guest)
function user_is_guest(&$ms_user) {
	$ms_user['id'] = NULL;
	$ms_user['valid'] = false;
	$ms_user['username'] = '';
	$ms_user['permission'] = 0;
	$ms_user['is_mod'] = false;
	$ms_user['is_admin'] = false;
	$ms_user['style_col'] = '000';
	$ms_user['style_logo'] = 'default';
}

// replace html with other characters
function clearHTML($text, $linebreaks = false) {
	global $ms_user;
	$text = str_replace('&', '&amp;', $text);
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	if ($linebreaks) {
		$text = str_replace("\n", '<br />', $text);
	}
	$text = censor($text);
	return $text;
}

function censor($text) {
	global $ms_user;
	$pattern = array('damn*', '*fuck*', '*shit*', 'crap*', 'rape', 'rapist', 'raper', 'cunt*', 'ass', 'asshole', '*cialis*', '*viagra*');
	$text = ' ' . $text . ' ';
	foreach ($pattern as &$val) {
		$val = '%(?<=[^\p{L}\p{N}])('.str_replace('\*', '[\p{L}\p{N}]*?', preg_quote($val, '%')).')(?=[^\p{L}\p{N}])%iu';
	}
	$text = preg_replace($pattern, '[censored]', $text);
	$text = substr($text, 1, strlen($text) - 2);
	if (strstr($text, '[censored]')) {
		addlog('Bad word in comment "' . $text . '"');
	}
	return $text;
}

// add smilies to text
function add_smilies($text) {
	$text = str_replace(':)', '<img src="/img/smilies/happy.png" />', $text);
	$text = str_replace(':(', '<img src="/img/smilies/sad.png" />', $text);
	$text = str_replace(':P', '<img src="/img/smilies/tongue.png" />', $text);
	$text = str_replace(':D', '<img src="/img/smilies/laugh.png" />', $text);
	$text = str_replace(':/', '<img src="/img/smilies/confused.png" />', $text);
	return $text;
}

// return html for the user page
function parse_username($uinfo) {
	//give me an array with the elements username and permission
	$out = '<a href="/users/' . clearHTML(rawurlencode($uinfo['username'])) . '">' . clearHTML($uinfo['username']) . '</a>';
	if ($uinfo['permission'] == 2) {
		$out .= ' <sup style="color:#0F0; font-weight:bold; cursor:pointer" onclick="alert(\'This user is a moderator.\');">M</sup>';
	} elseif ($uinfo['permission'] == 3) {
		$out .= ' <sup style="color:#00F; font-weight:bold; cursor:pointer" onclick="alert(\'This user is an administrator.\');">A</sup>';
	}
	return $out;
}

// set a value in configuration
function set_config($key, $value) {
	global $db, $ms_config;
	$val = $value . '';
	$val = $db->escape($val);
	$db->query('UPDATE config SET c_value = \'' . $val . '\' WHERE c_name = \'' . $key . '\'') or error('Failed to update config', __FILE__, __LINE__, $db->error());
	$ms_config[$key] = $val;
}

// add a log entry
function addlog($text) {
	global $ms_user;
	file_put_contents(SRV_ROOT . '/data/log.txt', file_get_contents(SRV_ROOT . '/data/log.txt') . chr(13) . chr(10) .
	gmdate('ymd-Hi') . ', ' . $ms_user['username'] . ', ' . $_SERVER['REMOTE_ADDR'] . ': ' . $text);
}

//format a date
function format_date($time, $dateonly = false) {
	global $ms_user;
	$time += $ms_user['timezone'] * 60 * 60;
	if ($dateonly) {
		return gmdate('d M Y', $time);
	} else {
		return gmdate('d M Y H:i:s', $time);
	}
}

// get the name of the modification
function getMod($mod) {
	global $modlist;
	if (isset($modlist[$mod])) {
		return $modlist[$mod]['name'];
	} else {
		return 'A nonexistent mod';
	}
}

// turn an image into a data URI
function dataURI($data) {
	//$content_type = mime_content_type($data);
	if ($content_type == '') {
		$content_type = 'image/png';
	}
	return 'data:' . $content_type . ';base64,' . base64_encode($data);
}