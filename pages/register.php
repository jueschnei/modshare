<?php
if ($ms_config['status'] == 'warning') {
	header('Location: /');
	die;
}
$page_title = 'Register - Mod Share';
if (isset($_POST['username'])) {
	$errors = array();
	if ($_POST['pwd1'] != $_POST['pwd2']) {
		$errors[] = 'Passwords do not match.';
	}
	if (strstr(file_get_contents('http://scratch.mit.edu/api/authenticateuser?username=' . rawurlencode($_POST['username']) . '&password=' . rawurlencode($_POST['scratchpwd'])), 'false')) {
		$errors[] = 'Your password does not match that on the Scratch account with the same name.';
	}
	$result = $db->query('SELECT 1 FROM users
	WHERE LOWER(username) = LOWER(\'' . $db->escape($_POST['username']) . '\')') or error('Could not check for existing users', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$errors[] = 'User already exists';
	}
	if (empty($errors)) {
		$db->query('INSERT INTO users(username,password_hash,registered,registration_ip)
		VALUES(\'' . $db->escape($_POST['username']) . '\',\'' . $db->escape(ms_hash($_POST['pwd1'])) . '\',' . time() . ',\'' . $db->escape($_SERVER['REMOTE_ADDR']) . '\')') or error('Failed to create user', __FILE__, __LINE__, $db->error());
		$_SESSION['uid'] = $db->insert_id();
		
		addlog('New user registered! ' . $_POST['username']);
		header('Location: /'); die;
	}
}
?>
<h2>Register for Mod Share</h2>
<h3>Username and password</h3>
<?php if (!empty($errors)) {
	echo '<h4>The following errors need to be fixed:</h4>';
	echo '<ul>';
	foreach ($errors as $val) {
		echo '<li>' . $val . '</li>';
	}
	echo '</ul>';
} ?>
<form action="/register" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Username</td>
			<td><input type="text" name="username" value="<?php echo clearHTML($_POST['username']); ?>" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="pwd1" value="<?php echo clearHTML($_POST['pwd1']); ?>" /></td>
		</tr>
		<tr>
			<td>Confirm password</td>
			<td><input type="password" name="pwd2" value="<?php echo clearHTML($_POST['pwd2']); ?>" /></td>
		</tr>
		<tr>
			<td>Scratch password</td>
			<td><input type="password" name="scratchpwd" value="<?php echo clearHTML($_POST['scratchpwd']); ?>" /></td>
		</tr>
	</table>
	<input type="submit" value="Register" />
</form>