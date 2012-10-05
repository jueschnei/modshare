<?php

include PUN_ROOT . '../config/bootstrap.php';

$db_type = 'mysqli';
$db_host = $db_info['host'];
$db_name = $db_info['name'];
$db_username = $db_info['user'];
$db_password = $db_info['pass'];
$db_prefix = 'flux_';
$p_connect = false;

$cookie_name = 'pun_cookie_e0abc9';
$cookie_domain = '';
$cookie_path = '/';
$cookie_secure = 0;
$cookie_seed = '8f48af2de5829a25';

define('PUN', 1);
