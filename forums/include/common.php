<?php

/**
 * Copyright (C) 2008-2012 FluxBB
 * based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */


if (!defined('PUN_ROOT'))
	exit('The constant PUN_ROOT must be defined and point to a valid FluxBB installation root directory.');

// Define the version and database revision that this code was written for
define('FORUM_VERSION', '1.4.9');

define('FORUM_DB_REVISION', 15);
define('FORUM_SI_REVISION', 2);
define('FORUM_PARSER_REVISION', 2);

//define some constants
define('TOO_MANY_REPORTS', 3); //after this many reports, the post will be hidden and the poster banned for 24 hours

// Block prefetch requests
if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
{
	header('HTTP/1.1 403 Prefetching Forbidden');

	// Send no-cache headers
	header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache'); // For HTTP/1.0 compatibility

	exit;
}

// Attempt to load the configuration file config.php
if (file_exists(PUN_ROOT.'config.php'))
	require PUN_ROOT.'config.php';

// If we have the 1.3-legacy constant defined, define the proper 1.4 constant so we don't get an incorrect "need to install" message
if (defined('FORUM'))
	define('PUN', FORUM);

// Load the functions script
require PUN_ROOT.'include/functions.php';

// Load UTF-8 functions
require PUN_ROOT.'include/utf8/utf8.php';

// Strip out "bad" UTF-8 characters
forum_remove_bad_characters();

// Reverse the effect of register_globals
forum_unregister_globals();

// If PUN isn't defined, config.php is missing or corrupt
if (!defined('PUN'))
{
	header('Location: install.php');
	exit;
}

// Record the start time (will be used to calculate the generation time for the page)
$pun_start = get_microtime();

// Make sure PHP reports all errors except E_NOTICE. FluxBB supports E_ALL, but a lot of scripts it may interact with, do not
error_reporting(E_ALL ^ E_NOTICE);

// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale(LC_CTYPE, 'C');

// Turn off magic_quotes_runtime
if (get_magic_quotes_runtime())
	set_magic_quotes_runtime(0);

// Strip slashes from GET/POST/COOKIE/REQUEST/FILES (if magic_quotes_gpc is enabled)
if (!defined('FORUM_DISABLE_STRIPSLASHES') && get_magic_quotes_gpc())
{
	function stripslashes_array($array)
	{
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
	$_REQUEST = stripslashes_array($_REQUEST);
	if (is_array($_FILES))
	{
		// Don't strip valid slashes from tmp_name path on Windows
		foreach ($_FILES AS $key => $value)
			$_FILES[$key]['tmp_name'] = str_replace('\\', '\\\\', $value['tmp_name']);
		$_FILES = stripslashes_array($_FILES);
	}
}

// If a cookie name is not specified in config.php, we use the default (pun_cookie)
if (empty($cookie_name))
	$cookie_name = 'pun_cookie';

// If the cache directory is not specified, we use the default setting
if (!defined('FORUM_CACHE_DIR'))
	define('FORUM_CACHE_DIR', PUN_ROOT.'cache/');

// Define a few commonly used constants
define('PUN_UNVERIFIED', 0);
define('PUN_ADMIN', 1);
define('PUN_MOD', 2);
define('PUN_GUEST', 3);
define('PUN_MEMBER', 4);

// Load DB abstraction layer and connect
require PUN_ROOT.'include/dblayer/common_db.php';

// Start a transaction
$db->start_transaction();

// Load cached config
if (file_exists(FORUM_CACHE_DIR.'cache_config.php'))
	include FORUM_CACHE_DIR.'cache_config.php';

if (!defined('PUN_CONFIG_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';

	generate_config_cache();
	require FORUM_CACHE_DIR.'cache_config.php';
}

// Verify that we are running the proper database schema revision
if (!isset($pun_config['o_database_revision']) || $pun_config['o_database_revision'] < FORUM_DB_REVISION ||
	!isset($pun_config['o_searchindex_revision']) || $pun_config['o_searchindex_revision'] < FORUM_SI_REVISION ||
	!isset($pun_config['o_parser_revision']) || $pun_config['o_parser_revision'] < FORUM_PARSER_REVISION ||
	version_compare($pun_config['o_cur_version'], FORUM_VERSION, '<'))
{
	header('Location: db_update.php');
	exit;
}

// Enable output buffering
if (!defined('PUN_DISABLE_BUFFERING'))
{
	// Should we use gzip output compression?
	if ($pun_config['o_gzip'] && extension_loaded('zlib'))
		ob_start('ob_gzhandler');
	else
		ob_start();
}

// Define standard date/time formats
$forum_time_formats = array($pun_config['o_time_format'], 'H:i:s', 'H:i', 'g:i:s a', 'g:i a');
$forum_date_formats = array($pun_config['o_date_format'], 'Y-m-d', 'Y-d-m', 'd-m-Y', 'm-d-Y', 'M j Y', 'jS M Y');

// Check/update/set cookie and fetch user info
$pun_user = array();
check_cookie($pun_user);

// Attempt to load the common language file
if (file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/common.php'))
	include PUN_ROOT.'lang/'.$pun_user['language'].'/common.php';
else
	error('There is no valid language pack \''.pun_htmlspecialchars($pun_user['language']).'\' installed. Please reinstall a language of that name');

// Check if we are to display a maintenance message
if ($pun_config['o_maintenance'] && $pun_user['g_id'] > PUN_ADMIN && !defined('PUN_TURN_OFF_MAINT'))
	maintenance_message();

// Load cached bans
if (file_exists(FORUM_CACHE_DIR.'cache_bans.php'))
	include FORUM_CACHE_DIR.'cache_bans.php';

if (!defined('PUN_BANS_LOADED'))
{
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require PUN_ROOT.'include/cache.php';

	generate_bans_cache();
	require FORUM_CACHE_DIR.'cache_bans.php';
}

// Check if current user is banned
check_bans();

// Update online list
update_users_online();

// Check to see if we logged in without a cookie being set
if ($pun_user['is_guest'] && isset($_GET['login']))
	message($lang_common['No cookie']);

// The maximum size of a post, in bytes, since the field is now MEDIUMTEXT this allows ~16MB but lets cap at 1MB...
if (!defined('PUN_MAX_POSTSIZE'))
	define('PUN_MAX_POSTSIZE', 1048576);

if (!defined('PUN_SEARCH_MIN_WORD'))
	define('PUN_SEARCH_MIN_WORD', 3);
if (!defined('PUN_SEARCH_MAX_WORD'))
	define('PUN_SEARCH_MAX_WORD', 20);

if (!defined('FORUM_MAX_COOKIE_SIZE'))
	define('FORUM_MAX_COOKIE_SIZE', 4048);

ini_set('session.save_path', SRV_ROOT . '/sessions');
ini_set('session.name', 'MODSHARESESSIONID');
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
session_start();
$forums = true;
include SRV_ROOT . '/includes/global_functions.php';
include SRV_ROOT . '/includes/filter.php';
check_user($ms_user);
if ($ms_user['valid'] && $ms_user['username'] != $pun_user['username']) {
	//do something!
	$result = $db->query('SELECT 1 FROM ' . $db->prefix . 'users
	WHERE username=\'' . $db->escape($ms_user['username']) . '\'') or error('Failed to check if user exists', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		//user exists, log them in
		$form_username = pun_trim($ms_user['username']);
		$form_password = pun_trim('modshare');
		$save_pass = false;

		$username_sql = ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') ? 'username=\''.$db->escape($form_username).'\'' : 'LOWER(username)=LOWER(\''.$db->escape($form_username).'\')';

		$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE '.$username_sql) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		$cur_user = $db->fetch_assoc($result);

		$authorized = false;

		if (!empty($cur_user['password']))
		{
			$form_password_hash = pun_hash($form_password); // Will result in a SHA-1 hash

			// If there is a salt in the database we have upgraded from 1.3-legacy though havent yet logged in
			if (!empty($cur_user['salt']))
			{
				if (sha1($cur_user['salt'].sha1($form_password)) == $cur_user['password']) // 1.3 used sha1(salt.sha1(pass))
				{
					$authorized = true;

					$db->query('UPDATE '.$db->prefix.'users SET password=\''.$form_password_hash.'\', salt=NULL WHERE id='.$cur_user['id']) or error('Unable to update user password', __FILE__, __LINE__, $db->error());
				}
			}
			// If the length isn't 40 then the password isn't using sha1, so it must be md5 from 1.2
			else if (strlen($cur_user['password']) != 40)
			{
				if (md5($form_password) == $cur_user['password'])
				{
					$authorized = true;

					$db->query('UPDATE '.$db->prefix.'users SET password=\''.$form_password_hash.'\' WHERE id='.$cur_user['id']) or error('Unable to update user password', __FILE__, __LINE__, $db->error());
				}
			}
			// Otherwise we should have a normal sha1 password
			else
				$authorized = ($cur_user['password'] == $form_password_hash);
		}

		if (!$authorized)
			message($lang_login['Wrong user/pass'].' <a href="login.php?action=forget">'.$lang_login['Forgotten pass'].'</a>');

		// Update the status if this is the first time the user logged in
		if ($cur_user['group_id'] == PUN_UNVERIFIED)
		{
			$db->query('UPDATE '.$db->prefix.'users SET group_id='.$pun_config['o_default_user_group'].' WHERE id='.$cur_user['id']) or error('Unable to update user status', __FILE__, __LINE__, $db->error());

			// Regenerate the users info cache
			if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
				require PUN_ROOT.'include/cache.php';

			generate_users_info_cache();
		}

		// Remove this users guest entry from the online list
		$db->query('DELETE FROM '.$db->prefix.'online WHERE ident=\''.$db->escape(get_remote_address()).'\'') or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

		$expire = ($save_pass == '1') ? time() + 1209600 : time() + $pun_config['o_timeout_visit'];
		pun_setcookie($cur_user['id'], $form_password_hash, $expire);

		// Reset tracked topics
		set_tracked_topics(null);
		
		header('Refresh: 0'); die;
	} else {
		$password_hash = pun_hash('modshare');
		$email1 = 'none@given.com';
		$group_id = 5;
		$now = $ms_user['registered'];
		$db->query('INSERT INTO '.$db->prefix.'users (username, group_id, password, email, email_setting, timezone, dst, language, style, registered, registration_ip, last_visit) VALUES(\''.$db->escape($ms_user['username']).'\', '.$group_id.', \''.$password_hash.'\', \''.$db->escape($email1).'\', 2, 0, 0, \'English\', \''.$pun_config['o_default_style'].'\', '.$ms_user['registered'].', \''.get_remote_address().'\', '.$now.')') or error('Unable to create user', __FILE__, __LINE__, $db->error());
		unlink(FORUM_CACHE_DIR . 'cache_users_info.php');
		header('Refresh: 0'); die;
	}
}

//check group id
if ($ms_user['valid'] && !$pun_user['is_guest']) {
	if ($ms_user['permission'] == 3 && $pun_user['group_id'] != 1) {
		$db->query('UPDATE ' . $db->prefix . 'users
		SET group_id=1
		WHERE id=' . $pun_user['id']) or error('Failed to update group ID', __FILE__, __LINE__, $db->error());
		header('Refresh: 0'); die;
	}
	if ($ms_user['permission'] == 2 && $pun_user['group_id'] != 2) {
		$db->query('UPDATE ' . $db->prefix . 'users
		SET group_id=2
		WHERE id=' . $pun_user['id']) or error('Failed to update group ID', __FILE__, __LINE__, $db->error());
		header('Refresh: 0'); die;
	}
	if ($pun_user['group_id'] < 4 && $ms_user['permission'] == 1) {
		$db->query('UPDATE ' . $db->prefix . 'users
		SET group_id=5
		WHERE id=' . $pun_user['id']) or error('Failed to update group ID', __FILE__, __LINE__, $db->error());
		header('Refresh: 0'); die;
	}
	if ($pun_user['group_id'] == 5) {
		//promote new Mod Share-ers to Mod Share-ers
		$result = $db->query('SELECT 1 FROM projects
		WHERE id=' . $ms_user['id']) or error('Failed to get project count', __FILE__, __LINE__, $db->error());
		$num_projects = $db->num_rows($result);
		$days_registered = floor((time() - $pun_user['registered']) / 60 / 60 / 24);
		$score = floor((4 * $num_projects) + (.4 * $pun_user['num_posts']) + $days_registered);
		if ($score > 30 && $num_projects > 0 && $pun_user['num_posts'] > 4 && $days_registered > 13) {
			$db->query('UPDATE ' . $db->prefix . 'users
			SET group_id=4
			WHERE id=' . $pun_user['id']) or error('Failed to promote user', __FILE__, __LINE__, $db->error());
			header('Refresh: 0'); die;
		}
	}
}

if (!$ms_user['valid'] && !$pun_user['is_guest']) {
	// Remove user from "users online" list
	$db->query('DELETE FROM '.$db->prefix.'online WHERE user_id='.$pun_user['id']) or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());

	pun_setcookie(1, pun_hash(uniqid(rand(), true)), time() + 31536000);
}

//check if timezone is correct
if ($ms_user['valid'] && $pun_user['timezone'] != $ms_user['timezone']) {
	$db->query('UPDATE ' . $db->prefix . 'users
	SET timezone=' . $ms_user['timezone'] . '
	WHERE id=' . $pun_user['id']) or error('Failed to update time zone', __FILE__, __LINE__, $db->error());
	header('Refresh: 0'); die;
}

if ($_SESSION['banned']) {
	header('Location: /banned'); die;
}