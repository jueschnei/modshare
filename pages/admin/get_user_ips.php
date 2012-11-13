<?php
$page_title = 'Get user IPs';
$getid = intval($dirs[3]);

$result = $db->query('SELECT username FROM users
WHERE id=' . $getid) or error('Failed to get username', __FILE__, __LINE__, $db->error());
list($username) = $db->fetch_row($result);

$ips = array();
$result = $db->query('SELECT registration_ip FROM users
WHERE id=' . $getid) or error('Failed to get registration IP', __FILE__, __LINE__, $db->error());
list($registration_ip) = $db->fetch_row($result);
$ips[] = $registration_ip;

$result = $db->query('SELECT poster_ip FROM flux_posts
WHERE poster=\'' . $username . '\'') or error('Failed to get forum post IPs', __FILE__, __LINE__, $db->error());
while (list($ip) = $db->fetch_row($result)) {
	if (!in_array($ip, $ips)) {
		$ips[] = $ip;
	}
}

$result = $db->query('SELECT ip_addr FROM projects
WHERE uploaded_by=' . $getid) or error('Failed to get project IPs', __FILE__, __LINE__, $db->error());
while (list($ip) = $db->fetch_row($result)) {
	if (!in_array($ip, $ips)) {
		$ips[] = $ip;
	}
}

$result = $db->query('SELECT ip_addr FROM comments
WHERE author=' . $getid) or error('Failed to get project IPs', __FILE__, __LINE__, $db->error());
while (list($ip) = $db->fetch_row($result)) {
	if (!in_array($ip, $ips)) {
		$ips[] = $ip;
	}
}

echo '<textarea readonly="readonly" cols="100" rows="20">' . implode($ips, ',') . '</textarea>';