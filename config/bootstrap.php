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
	'insanity10' => array(
		'extension'	=> 'ins',
		'name'		=> '<a href="http://insanity.jvvgindustries.com">Insanity 1.0</a>'
	),
	'insanity11' => array(
		'extension'	=> 'ins',
		'name'		=> '<a href="http://insanity.jvvgindustries.com">Insanity 1.1</a>'
	),
	'bingo' => array(
		'extension'	=> 'bingo',
		'name'		=> '<a href="http://bingoprogramming.weebly.com">Bingo 1.X</a>'
	),
	'bingo2' => array(
		'extension'	=> 'bingo',
		'name'		=> '<a href="http://bingoprogramming.weebly.com">Bingo 2.0</a>'
	),
	'panther' => array(
		'extension'	=> 'pt',
		'name'		=> '<a href="http://pantherprogramming.weebly.com">Panther</a>'
	),
	'byob' => array(
		'extension'	=> 'ypr',
		'name'		=> '<a href="http://byob.berkely.edu">BYOB</a>'
	),
	'stack' => array(
		'extension'	=> 'sb',
		'name'		=> 'Stack'
	),
	'kitcat' => array(
		'extension'	=> 'kct',
		'name'		=> 'Kitcat'
	),
	'other' => array(
		'extension'	=> 'sb',
		'name'		=> 'An unrecognized mod'
	)
);

define('MS_DEBUG', true); //enable debug mode (shows number of queries on the bottom of the page for admins and shows more detailed error information)
define('MS_EMERGENCY', false); //enables emergency mode