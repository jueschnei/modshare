<?php
$page_title = 'Election Setup - Mod Share';
if (isset($_POST['clear'])) {
	$db->query('TRUNCATE TABLE election_options') or error('Failed to clear options', __FILE__, __LINE__, $db->error());
	$db->query('TRUNCATE TABLE election_voted') or error('Failed to clear votes', __FILE__, __LINE__, $db->error());
}
if (isset($_POST['add_new'])) {
	$db->query('INSERT INTO election_options(text)
	VALUES(\'\')') or error('Failed to make new option', __FILE__, __LINE__, $db->error());
}
if (isset($_POST['form_sent'])) {
	set_config('election_question', $_POST['question']);
	foreach ($_POST['opts'] as $key => $val) {
		$db->query('UPDATE election_options
		SET text=\'' . $db->escape($val) . '\'
		WHERE id=' . intval($key)) or error('Failed to update election options', __FILE__, __LINE__, $db->error());
	}
}
?>
<h2>Election setup</h2>
<p>The link to vote is <a href="/vote">http://<?php echo $_SERVER['HTTP_HOST']; ?>/vote</a>.</p>
<form action="/admin/election_setup" method="post" enctype="multipart/form-data">
	<p><input type="submit" name="add_new" value="Add new option" /> &bull; <input type="submit" name="clear" value="Clear options and votes" /></p>
</form>
<form action="/admin/election_setup" method="post" enctype="multipart/form-data">
	<h4>Setup</h4>
	<table border="0">
		<tr>
			<td>Question</td>
			<td><input type="text" name="question" value="<?php echo clearHTML($ms_config['election_question']); ?>" size="50" /></td>
		</tr>
	</table>
	<h4>Poll options</h4>
	<table border="0">
		<?php
		$result = $db->query('SELECT id,text FROM election_options
		ORDER BY id ASC') or error('Failed to get current election options', __FILE__, __LINE__, $db->error());
		while ($cur_option = $db->fetch_assoc($result)) {
			echo '
		<tr>
			<td><input type="text" name="opts[' . $cur_option['id'] . ']" value="' . clearHTML($cur_option['text']) . '" /></td>
		</tr>
		';
		}
		?>
	</table>
	<input type="submit" name="form_sent" value="Save" />
</form>
<?php
if ($ms_config['election']) { ?>
<h3>Current Results</h3>
<?php
	$result = $db->query('SELECT ev.choice,eo.text FROM election_voted AS ev
	LEFT JOIN election_options AS eo
	ON eo.id=ev.choice') or error('Failed to get election results', __FILE__, __LINE__, $db->error());
	$results = array();
	while ($cur_vote = $db->fetch_assoc($result)) {
		if (isset($results[$cur_vote['text']])) {
			$results[$cur_vote['text']]++;
		} else {
			$results[$cur_vote['text']] = 1;
		}
	}
	foreach ($results as $key => $val) {
		echo '<p>' . clearHTML($key) . ': ' . $val . ' vote(s)</p>';
	}
}