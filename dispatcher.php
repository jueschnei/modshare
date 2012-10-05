<?php
/****************************************************************************
	Mod Share IV Coding Platform
	Copyright (c) 2012 - LS97 and jvvg
	
	MAIN DISPATCHER
	All requests (except ones overridden in .htaccess) go through this file
	To make a new page, go to config/pages.php
	To change database settings or the list of allowed mods, go to config/bootstrap.php
	This prevents unwanted access to system files
	
	This script generates a few variables for use in Mod Share's pages:
	 SRV_ROOT			Defines the root PHP directory of public_html
	 MS_DEBUG			Defines a boolean of whether the site is in debug mode
	 MS_EMERGENCY		Defines a boolean of whether the site is in emergency mode
	 $db_info			IDs and passwords for the database login
	 $ms_config			Contains basic key/value configuration
	 $ms_user			Contains authentication info and preferences of the logged in user
	 $modlist			An array containing info about the allowed mods
****************************************************************************/

// define the server root to be used in all scripts
define('SRV_ROOT', dirname(__FILE__));

ini_set('magic_quotes_runtime', 0);
ini_set('session.save_path', SRV_ROOT . '/sessions');
ini_set('session.name', 'MODSHARESESSIONID');
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
// start the user session
session_start();

// include core files
include SRV_ROOT . '/config/bootstrap.php';
include SRV_ROOT . '/config/pages.php';
include SRV_ROOT . '/drivers/mysqli.php';
include SRV_ROOT . '/includes/global_functions.php';

if (MS_EMERGENCY) {
	error('The site is currently down due to a system emergency.');
	die;
}

//initialise the database using $db_info
$db = new databasetool($db_info);
if (!$db) {
	error('Failed to start database', __FILE__, __LINE__, $db->connect_error());
}

//get site config from database (or cache)
if (!file_exists(SRV_ROOT . '/cache/cache_config.php')) {
	$ms_config = array();
	$result = $db->query('SELECT c_name,c_value FROM config');
	while ($c_field = $db->fetch_assoc($result)) {
		$ms_config[$c_field['c_name']] = $c_field['c_value'];
	}
	$data = '<?php' . "\n" . '$ms_config = ';
	$data .= var_export($ms_config, true);
	$data .= ';';
	file_put_contents(SRV_ROOT . '/cache/cache_config.php', $data);
}
include SRV_ROOT . '/cache/cache_config.php';

//fix $_POST if necessary (remove the backslashes)
function stripslashes_array($array) {
	foreach ($array as &$val) {
		if (is_array($val)) {
			$val = stripslashes_array($val);
		} else {
			$val = stripslashes($val);
		}
	}
	return $array;
}
$_POST = stripslashes_array($_POST);

// get user info from database
check_user($ms_user);

//maintenance?
if ($ms_config['status'] == 'maint' && $_SERVER['REQUEST_URI'] != '/styles/default.css' && !$ms_user['is_admin']) {
	include SRV_ROOT . '/pages/maintenance.php';
	die;
}

//look for the page and set the page info
$url = strtok($_SERVER['REQUEST_URI'], '?');
$dirs = explode('/', $url);
$base = dirname($url);

//check for bans
$result = $db->query('SELECT 1 FROM bans
WHERE (user_id=' . ($ms_user['valid'] ? $ms_user['id'] : -1) . '
OR ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'
OR ip LIKE \'%,' . $_SERVER['REMOTE_ADDR'] . '\'
OR ip LIKE \'%,' . $_SERVER['REMOTE_ADDR'] . ',%\'
OR IP LIKE \'' . $_SERVER['REMOTE_ADDR'] . ',%\')
AND expires>' . time()) or error('Failed to check bans', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result) && !$ms_user['is_admin']) {
	$ms_user['banned'] = true;
	$_SESSION['banned'] = true;
	if ($url != '/banned' && $url != '/styles/default.css' && $url != '/help') {
		header('Location: /banned'); die;
	}
}

// check whether to display mandatory voting
if ($ms_config['election_mandatory'] == 'yes' && $ms_config['election']) {
	if($ms_user['valid']) {
		$result = $db->query('SELECT voter FROM election_voted WHERE voter=' . $ms_user['id']) or error('Failed to see if you have already voted', __FILE__, __LINE__, $db->error());
		if(!$db->num_rows($result)) {
			// show mandatory election page!
			if ($url != '/vote' && $url != '/styles/default.css' && $url != '/styles/forums.css' && $url != '/logout') {
				header('Location: /vote'); die;
			}
		}
	}
}

//check if the page exists
$ok = false;
if (array_key_exists($url, $pages)) {
	$ok = true;
	$page_info = $pages[$url];
	if ($page_info['permission'] > $ms_user['permission']) {
		$ok = false;
	}
} else {
	foreach ($pageswithsubdirs as $key => $val) {
		if (strpos($url, $key) === 0) {
			$ok = true;
			$page_info = $val;
			break;
		}
	}
}

// if page found, render the page
if ($ok) {
	// start the output buffer
	ob_start();
	
	// output the header
	if ($page_info['header'])
		include SRV_ROOT . '/includes/header.php';
		
	//include a prepend
	$continue = true;
	if ($page_info['prepend']) {
		include SRV_ROOT . $page_info['prepend'];
	}
	
	if ($continue) {
		// output page contents
		if(file_exists(SRV_ROOT . '/pages/' . $page_info['file'])) {
			include SRV_ROOT . '/pages/' . $page_info['file'];
		} else {
			echo '<p>Page not found.</p>';
		}
	}
	
	// output the footer
	if ($page_info['header'])
		include SRV_ROOT . '/includes/footer.php';
	
	// dump the buffer into a string for manipulation
	$contents = ob_get_contents();
	ob_end_clean();
	
	// set the appropriate content type
	if ($content_type) {
		header('Content-type: ' . $content_type);
	} else {
		header('Content-type: text/html; charset=utf-8');
	}
	
	// modify the page title according to the variable, if present
	if ($page_info['header']) {
		if(!isset($page_title))
			$page_title = 'Mod Share';
		$contents = str_replace('<$page_title/>', $page_title, $contents);
	}
	
	// output the page
	echo $contents;
} else {
	// if not found, echo a 404 page
	header('HTTP/1.1 404 Not found');
	include SRV_ROOT . '/errorpages/404.php';
}

$_SESSION['lastvisit'] = time();

$db->close();