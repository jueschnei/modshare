<?php
/*
						   ######
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						  #      #
						   ######
						   
						     ##
							####
						   ######
						    ####
							 ##
							 
							 
WARNING: This file is not to be viewed by anyone who isn't mature enough.
This file is the inappropriate word filter (and nothing else). For stuff like the clearHTML function, see global_functions.php
If you are mature enough to see the words we censor and the code used to censor them, go ahead and scroll down. Otherwise, close this. There is nothing else to see.
How the old filter worked is that it replaced the bad words with "[censored]", while the new one will prevent the message from being posted at all (also applies on the forums).







































						
*/
$badwordlist = array('damn*', '*fuck*', '*shit*', 'crap*', 'rape', 'rapist', 'raper', 'cunt*', 'ass', 'asshole', '*cialis*', '*viagra*', 'penis*', 'vagina*');
function censor($text) {
	global $ms_user, $badwordlist;
	$pattern = $badwordlist;
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
function containsBadWords($text) {
	global $ms_user, $badwordlist, $db;
	define('PUN_DEBUG', 1);
	$pattern = $badwordlist;
	$text = ' ' . $text . ' ';
	foreach ($pattern as &$val) {
		$val = '%(?<=[^\p{L}\p{N}])('.str_replace('\*', '[\p{L}\p{N}]*?', preg_quote($val, '%')).')(?=[^\p{L}\p{N}])%iu';
	}
	foreach ($pattern as $val) {
		if (preg_match($val, $text)) {
			$db->query('INSERT INTO notificationstoadmin(text)
			VALUES(\'' . $db->escape('The user "' . $ms_user['username'] . '" posted something inappropriate:
' . $text) . '\')') or error('Failed to report bad word', __FILE__, __LINE__, $db->error());
			return true;
		}
	}
	return false;
}