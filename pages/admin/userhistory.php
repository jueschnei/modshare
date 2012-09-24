<?php
$result = $db->query('SELECT username FROM users
WHERE id=' . intval($dirs[3])) or error('Failed to check user', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	echo 'Invalid user';
	return;
}
$user_info = $db->fetch_assoc($result);
$page_title = $user_info['username'] . '&apos;s history';
?>
<h2><?php echo $user_info['username'] . '&apos;s history'; ?></h2>
<table border="0">
	<tr>
		<th style="padding: 5px">Date</th>
		<th style="padding: 5px">Done by</th>
		<th style="padding: 5px">Action</th>
	</tr>
	<?php $result = $db->query('SELECT adm.username AS by_user,h.action,h.time,h.action FROM adminhistory AS h
	LEFT JOIN users AS adm
	ON adm.id=h.from_user
	WHERE to_user=' . intval($dirs[3]) . '
	ORDER BY h.time DESC') or error('Failed to check actions', __FILE__, __LINE__, $db->error());
	while ($cur_action = $db->fetch_assoc($result)) {
		echo '<tr>
			<td style="padding: 5px">' . format_date($cur_action['time']) . '</td>
			<td style="padding: 5px">' . clearHTML($cur_action['by_user']) . '</td>
			<td style="padding: 5px">' . clearHTML($cur_action['action']) . '</td>
		</tr>';
	}
	?>
</table>