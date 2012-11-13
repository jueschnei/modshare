<?php
$page_title = 'Search results - Mod Share';
$ip = $db->escape(str_replace('*', '%', $dirs[3]));
$results = array();
$results['forums'] = $db->query('SELECT p.id,p.topic_id,p.poster,t.subject FROM flux_posts AS p
LEFT JOIN flux_topics AS t
ON t.id=p.topic_id
WHERE p.poster_ip LIKE \'' . $ip . '\'
ORDER BY p.posted DESC') or error('Failed to check forum posts', __FILE__, __LINE__, $db->error());
$results['projects'] = $db->query('SELECT p.id,p.title,u.username,u.permission FROM projects AS p
LEFT JOIN users AS u
ON u.id=p.uploaded_by
WHERE p.ip_addr LIKE \'' . $ip . '\'
ORDER BY p.time DESC') or error('Failed to check projects', __FILE__, __LINE__, $db->error());
$results['comments'] = $db->query('SELECT c.content,u.username,u.permission,p.title AS project_title,p.id AS pid,pa.username AS project_author FROM comments AS c
LEFT JOIN users AS u
ON u.id=c.author
LEFT JOIN projects AS p
ON p.id=c.project
LEFT JOIN users AS pa
ON pa.id=p.uploaded_by
WHERE c.ip_addr LIKE \'' . $ip . '\'
ORDER BY c.posted DESC') or error('Failed to check comments', __FILE__, __LINE__, $db->error());
$results['registrations'] = $db->query('SELECT username,permission FROM users
WHERE registration_ip LIKE \'' . $ip . '\'
ORDER BY registered DESC') or error('Failed to get registered users', __FILE__, __LINE__, $db->error());
echo '<h2>Search results for "' . $ip . '"</h2>';
if ($db->num_rows($results['forums'])) {
	echo '<h3>Forum posts</h3>';
	while ($cur_post = $db->fetch_assoc($results['forums'])) {
		echo '<h4><a href="/forums/viewtopic.php?pid=' . $cur_post['id'] . '#p' . $cur_post['id'] . '">' . $cur_post['subject'] . '</a>, by ' . clearHTML($cur_post['poster']) . '</h4>';
	}
}
if ($db->num_rows($results['projects'])) {
	echo '<h3>Projects</h3>';
	while ($cur_project = $db->fetch_assoc($results['projects'])) {
		echo '<h4><a href="/projects/' . $cur_project['username'] . '/' . $cur_project['id'] . '">' . clearHTML($cur_project['title']) . '</a>, by ' . parse_username($cur_project) . '</h4>';
	}
}
if ($db->num_rows($results['comments'])) {
	echo '<h3>Comments</h3>';
	while ($cur_comment = $db->fetch_assoc($results['comments'])) {
		echo '<h4>On ' . $cur_comment['project_author'] . '&apos;s project <a href="/projects/' . $cur_comment['project_author'] . '/' . $cur_comment['pid'] . '">' . $cur_comment['project_title'] . '</a>, by ' . parse_username($cur_comment) . '</h4>';
		echo '<p>' . clearHTML($cur_comment['content']) . '</p>';
	}
}
if ($db->num_rows($results['registrations'])) {
	echo '<h3>User registrations</h3>';
	while ($cur_user = $db->fetch_assoc($results['registrations'])) {
		echo '<p>' . parse_username($cur_user) . '</p>';
	}
}