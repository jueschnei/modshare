<?php
$page_title = 'Bans - Mod Share';
?>
<?php
$db->query('DELETE FROM bans
WHERE expires<' . time()) or error('Failed to remove old bans', __FILE__, __LINE__, $db->error());
$result = $db->query('SELECT b.id,b.user_id,b.ip,u.username FROM bans AS b
LEFT JOIN users AS u ON u.id=b.user_id') or error('Failed to check bans', __FILE__, __LINE__, $db->error());
?>
<h2>Manage bans</h2>
<p><a href="/admin/ban_user/0">Add an IP ban</a></p>
<table border="0">
	<tr>
		<th>Username</th>
		<th>IP address</th>
		<th>Edit</th>
	</tr>
	<?php
	while ($cur_ban = $db->fetch_assoc($result)) {
		echo '<tr>
		<td>' . clearHTML($cur_ban['username']) . '</td>
		<td>' . $cur_ban['ip'] . '</td>
		<td><a href="/admin/edit_ban/' . $cur_ban['id'] . '">Edit</a></td>
	</tr>';
	}
	?>
</table>