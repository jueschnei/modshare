<?php
$page_title = 'Users - Mod Share';
$result = $db->query('SELECT username,id,permission FROM users
WHERE status<>\'disabledbyadmin\'
ORDER BY username ASC') or error('Failed to get users', __FILE__, __LINE__, $db->error());
?>
<h2>Users on Mod Share</h2>
<?php
while ($cur_user = $db->fetch_assoc($result)) {
	echo '<p>' . parse_username($cur_user) . '</a></p>';
}