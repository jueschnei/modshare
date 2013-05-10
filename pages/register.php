<?php
$page_title = 'Register - Mod Share';

if ($ms_user['valid']) {
	header('Location: /');
	return;
}

if ($ms_config['disableregistration']) {
	$page_title = 'Registrations disabled - Mod Share';
	echo '<p><img src="/img/denied.jpg" alt="registrations disabled"/><br />Registrations have been temporarily disabled. Please come back later.</p>';
	return;
}

function registrationform($error = null) {
	?>
	<form action="/register" method="post" enctype="multipart/form-data">
		<p>Verification successful. Please proceed. (note that the form below does not do anything right now, as this is just a code draft)</p>
		<?php if (isset($error)) { ?>
		<p><?php echo $error; ?></p>
		<?php } ?>
		<table border="0">
			<tr>
				<td>Username</td>
				<td><?php echo clearHTML($_SESSION['verifieduser']); ?></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="pwd1" /></td>
			</tr>
			<tr>
				<td>Confirm password</td>
				<td><input type="password" name="pwd2" /></td>
			</tr>
		</table>
		<p><input type="submit" name="reg_form_sent" value="Register" /></p>
		<p><a href="?restart">Start over with a different user</a></p>
	</form>
	<?php
}

$project_id = '10135908/';
$project_url = 'http://scratch.mit.edu/projects/' . $project_id;
$api_url = 'http://scratch.mit.edu/site-api/comments/project/' . $project_id . '?page=1&salt=' . md5(time()); //salt is to prevent caching
if (isset($_POST['reg_form_sent']) && !empty($_SESSION['verifieduser'])) {
	$result = $db->query('SELECT 1 FROM users WHERE username=\'' . $db->escape($_SESSION['verifieduser']) . '\'') or error('Failed to check if user already exists', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		registrationform('This user already exists on Mod Share. Please try a different user.');
		return;
	}
	if ($_POST['pwd1'] != $_POST['pwd2']) {
		registrationform('Passwords do not match');
		return;
	}
	$db->query('INSERT INTO users(username,password_hash,registered,registration_ip)
	VALUES(\'' . $db->escape($_SESSION['verifieduser']) . '\',\'' . $db->escape(ms_hash($_POST['pwd1'])) . '\',' . time() . ',\'' . $db->escape($_SERVER['REMOTE_ADDR']) . '\')') or error('Failed to create user', __FILE__, __LINE__, $db->error());
	$_SESSION['uid'] = $db->insert_id();
		
	addlog('New user registered! ' . $_POST['username']);
	header('Location: /'); die;
} else if (isset($_SESSION['verifieduser']) && $_SESSION['verifieduser'] != '' && !isset($_GET['restart'])) {
	registrationform();
} else if (!isset($_POST['username1']) && !isset($_POST['username2'])) {
	if (isset($_GET['restart'])) {
		unset($_SESSION['verifieduser']);
	}
	?>
<form action="/register" method="post" enctype="multipart/form-data">
	<p>Enter username: <input type="text" name="username1" /><input type="submit" value="Go" /></p>
</form>
	<?php
} else if (!isset($_POST['enteredcode']) && isset($_POST['username1'])) {
	$_SESSION['verifycode'] = sha1(time() . $_POST['username1']);
	echo '<p>Please go to the <a href="' . $project_url . '#comments" target="_BLANK">user verification project</a> and comment the following code: <b>' . $_SESSION['verifycode'] . '</b></p>';
	echo '<form action="/register" method="post" enctype="multipart/form-data"><p><input type="hidden" name="username2" value="' . clearHTML($_POST['username1']) . '" /><input type="submit" name="enteredcode" value="I have commented the code, continue" /></p></form>';
} else if (isset($_POST['enteredcode']) && isset($_POST['username2'])) {
	$data = file_get_contents($api_url);
	if (!$data) {
		echo '<p>API access failed. Please try again later.</p>';
		return;
	}
	$success = false;
	preg_match_all('%<div id="comments-\d+" class="comment.*?" data-comment-id="\d+">.*?<a href="/users/(.*?)">.*?<div class="content">(.*?)</div>%ms', $data, $matches);
	foreach ($matches[2] as $key => $val) {
		$user = $matches[1][$key];
		$comment = trim($val);
		if ($user == $_POST['username2'] && $comment == $_SESSION['verifycode']) {
			$success = true;
			$_SESSION['verifieduser'] = $_POST['username2'];
			registrationform();
			break;
		}
	}
	if (!$success) {
		echo '<p>Verification failed. It does not appear you commented the code on the project. Note that you must comment the code <i>exactly</i> as it appears, with nothing extra.</p><form action="/register" method="post" enctype="multipart/form-data"><p><input type="hidden" name="username1" value="' . $_POST['username2'] . '" /><input type="submit" value="Try again" /><br /><a href="/register?restart">Try again with a different user</a></p></form>';
	}
}
?>