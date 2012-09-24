<?php
$page_title = 'Moderator Election';
if (!$ms_config['election']) {
	echo '<p>There is currently no election.</p>';
	return;
}
if (isset($_POST['vote'])) {
	$db->query('INSERT INTO election_voted(voter,choice)
	VALUES(' . $ms_user['id'] . ',' . intval($_POST['vote']) . ')') or error('Failed to submit vote', __FILE__, __LINE__, $db->error());
}
$result = $db->query('SELECT 1 FROM election_voted
WHERE voter=' . $ms_user['id']) or error('Failed to check if you have voted', __FILE__, __LINE__, $db->error());
if ($db->num_rows($result)) {
	echo '<p>You have already voted.</p>';
	return;
}
$result = $db->query('SELECT id,text FROM election_options
ORDER BY text ASC') or error('Failed to get election options', __FILE__, __LINE__, $db->error());
?>
<h2>Election</h2>
<h3><?php echo $ms_config['election_question']; ?></h3>
<form action="/vote" method="post" enctype="multipart/form-data">
	<table border="0">
	<?php
	while ($cur_option = $db->fetch_assoc($result)) {
		echo '
		<tr>
			<td><input type="radio" name="vote" value="' . $cur_option['id'] . '" id="opt' . $cur_option['id'] . '"	/></td>
			<td><label for="opt' . $cur_option['id'] . '">' . clearHTML($cur_option['text']) . '</label</td>
		</tr>';
	}
	?>
	</table>
	<input type="submit" value="Submit vote" onclick="return window.confirm('You can\'t edit your choice after voting. Are you OK with your current choice?');" />
</form>