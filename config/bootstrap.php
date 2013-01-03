<?php
//Mod Share IV - Bootstrap
//This file contains settings such as the database.

$db_info = array( //database info
	'host'	=> '',
	'user' 	=> '',
	'pass' 	=> '',
	'name'	=> ''
);

$modlist = array( //list of allowed mods
	'scratch' => array(
		'extension'	=> 'sb',
		'name'		=> '<a href="http://scratch.mit.edu">Scratch</a>'
	),
	'bingo' => array(
		'extension'	=> 'bingo',
		'name'		=> '<a href="http://bingoprogramming.weebly.com">Bingo 1.X</a>'
	),
	'bingo2' => array(
		'extension'	=> 'bingo',
		'name'		=> '<a href="http://bingoprogramming.weebly.com">Bingo 2.0</a>'
	),
	'byob' => array(
		'extension'	=> 'ypr',
		'name'		=> '<a href="http://byob.berkely.edu">BYOB</a>'
	),
	'insanity10' => array(
		'extension'	=> 'ins',
		'name'		=> '<a href="http://insanity.jvvgindustries.com">Insanity 1.0</a>'
	),
	'insanity11' => array(
		'extension'	=> 'ins',
		'name'		=> '<a href="http://insanity.jvvgindustries.com">Insanity 1.1</a>'
	),
	'insanity12' => array(
		'extension'	=> 'ins',
		'name'		=> '<a href="http://insanity.jvvgindustries.com">Insanity 1.2</a>'
	),
	'panther' => array(
		'extension'	=> 'pt',
		'name'		=> '<a href="http://pantherprogramming.weebly.com">Panther</a>'
	),
	'stack' => array(
		'extension'	=> 'sb',
		'name'		=> 'Stack'
	),
	'kitcat' => array(
		'extension'	=> 'kct',
		'name'		=> 'Kitcat'
	),
	'bones' => array(
		'extension'	=> 'bons',
		'name'		=> '<a href="http://bonesprogramming.weebly.com">Bones</a>'
	),
	'blook' => array(
		'extension'	=> 'sb',
		'name'		=> '<a href="http://scratch.mit.edu/forums/viewtopic.php?id=107285">Blook</a>'
	),
	'other' => array(
		'extension'	=> 'sb',
		'name'		=> 'An unrecognised mod'
	)
);

$disallowed_dirs = array('cache', 'drivers', 'pages', 'errorpages', 'includes', 'sessions.sqlite');

$pun_admin_code = 'fstech';

$hash_salt = 'ms'; //a two-letter string representing the salt that will be used to hash passwords

define('MS_DEBUG', false); //enable debug mode (shows number of queries on the bottom of the page for admins and shows more detailed error information)
define('MS_EMERGENCY', false); //enables emergency mode
define('TOO_MANY_CONNECTIONS', 700); //more than this many connections in 10 minutes from one IP will cause a DDoS warning