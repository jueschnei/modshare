<?php

$pages = array(
	//******************** INDEX *********************//
	
	'/index.php' => array(
		'file'		=> 'default.php', //the file containing the page in /pages
		'header'	=> true, //does this page need a header and footer?
		'permission'=> 0, //what permissions does this page require? (0 for guest, 1 for logged in, 2 for moderator, and 3 for admin
		'prepend'	=> null //what files need to be put before it? (currently, this means nothing)
	),

	'/' => array(
		'file'		=> 'default.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	//******************** LOGIN *********************//
	
	'/login' => array(
		'file'		=> 'login.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/logout' => array(
		'file'		=> 'login.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/register' => array(
		'file'		=> 'register.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/forgot' => array(
		'file'		=> 'forgot.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	//******************** USERS *********************//
	
	'/notifications' => array(
		'file'		=> 'notifications.php',
		'header'	=> true,
		'permission'=> 1,
		'prepend'	=> null
	),
	
	//******************** PROJECTS *********************//
	
	'/upload' => array(
		'file'		=> 'upload.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/browse' => array(
		'file'		=> 'browseprojects.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/player' => array(
		'file'		=> 'swfdispatcher.php',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	//******************** BANNED *********************//
	
	'/banned' => array(
		'file'		=> 'banned.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	//******************** ELECTION *********************//
	
	'/vote' => array(
		'file'		=> 'election.php',
		'header'	=> true,
		'permission'=> 1,
		'prepend'	=> null
	),
	
	//******************** ADMIN *********************//
	
	'/admin/mod_menu' => array(
		'file'		=> 'admin/mod_menu.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null
	),
	
	'/admin/admin_menu' => array(
		'file'		=> 'admin/admin_menu.php',
		'header'	=> true,
		'permission'=> 3,
		'prepend'	=> null
	),
	
	'/admin/bans' => array(
		'file'		=> 'admin/bans.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null
	),
	
	'/admin/flags' => array(
		'file'		=> 'admin/flags.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null
	),
	
	'/admin/election_setup' => array(
		'file'		=> 'admin/election.php',
		'header'	=> true,
		'permission'=> 3,
		'prepend'	=> null
	),
	
	'/admin/archive_log' => array(
		'file'		=> 'admin/archivelog.php',
		'header'	=> false,
		'permission'=> 3,
		'prepend'	=> null
	),
	
	'/admin/maintenance' => array(
		'file'		=> 'admin/maintenance.php',
		'header'	=> true,
		'permission'=> 3,
		'prepend'	=> null
	),
	
	'/admin/return' => array(
		'file'		=> 'admin/return.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	//******************** AJAX *********************//
	'/ajax/delnotification' => array(
		'file'		=> 'ajax/delnotification.php',
		'header'	=> false,
		'permission'=> 1,
		'prepend'	=> null
	),
	
	//******************** STYLES *********************//

	'/styles/default.css' => array(
		'file'		=> 'css/default.css',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/styles/forums.css' => array(
		'file'		=> 'css/forums.css',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	//******************** IMAGE SERVICE *********************//
	
	'/imgsrv' => array(
		'file'		=> 'imgsrv/home.php',
		'header'	=> true,
		'permission'=> 1,
		'prepend'	=> '/pages/imgsrv/prepend.php'
	),

	//******************** MISC. *********************//

	'/help' => array(
		'file'		=> 'help.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/terms' => array(
		'file'		=> 'terms.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null
	),
);

$pageswithsubdirs = array(

	//******************** USERS *********************//
	'/users' => array(
		'file'		=> 'userviewer.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null

	),
	//******************** PROJECTS *********************//	
	'/projects' => array(
		'file'		=> 'projectviewer.php',
		'header'	=> true,
		'permission'=> 0,
		'prepend'	=> null

	),
	//******************** ADMIN *********************//
	'/admin/ban_user' => array(
		'file'		=> 'admin/ban_user.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null

	),
	
	'/admin/search_ip' => array(
		'file'		=> 'admin/search_ip.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null

	),
	
	'/admin/block_project/' => array(
		'file'		=> 'admin/block_project.php',
		'header'	=> false,
		'permission'=> 2,
		'prepend'	=> null

	),
	
	'/admin/edit_ban' => array(
		'file'		=> 'admin/edit_ban.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null
	),
	
	'/admin/filesandnews' => array(
		'file'		=> 'admin/updateadminnews.php',
		'header'	=> false,
		'permission'=> 2,
		'prepend'	=> null
	),
	
	'/admin/notify' => array(
		'file'		=> 'admin/notify.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null
	),
	
	'/admin/history' => array(
		'file'		=> 'admin/userhistory.php',
		'header'	=> true,
		'permission'=> 2,
		'prepend'	=> null

	),
	
	'/admin/delete_user' => array(
		'file'		=> 'admin/deleteuser.php',
		'header'	=> true,
		'permission'=> 3,
		'prepend'	=> null

	),
	
	//******************** REFERENCES (e.g. projects, avatars) *********************//
	'/getavatar' => array(
		'file'		=> 'refs/avatar.php',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/projthumbnail' => array(
		'file'		=> 'refs/projthumbnail.php',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/downloadproject' => array(
		'file'		=> 'refs/downloadproject.php',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	//******************** APIs *********************//
	
	'/api/cloudvars' => array(
		'file'		=> 'api/cloudvars_controller.php',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/api' => array(
		'file'		=> 'api/main.php',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
	
	'/imgsrv/view' => array(
		'file'		=> 'imgsrv/view.php',
		'header'	=> false,
		'permission'=> 0,
		'prepend'	=> null
	),
);

foreach ($pages as $key => $val) {
	$pages[$key . '/'] = $val;
}