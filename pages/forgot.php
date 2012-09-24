<?php
$page_title = 'Forgot password - Mod Share';
$errors = array();
if (isset($_POST['un'])) {
	if ($_POST['newpwd'] != $_POST['cnewpwd']) {
		$errors[] = 'Passwords don&apos;t match.';
	}
	if (strstr(file_get_contents('http://scratch.mit.edu/api/authenticateuser?username=' . rawurlencode($_POST['un']) . '&password=' . rawurlencode($_POST['scratchpwd'])), 'false')) {
		$errors[] = 'Scratch password is incorrect';
	}
	if (empty($errors)) {
		$db->query('UPDATE users
		SET password_hash=\'' . $db->escape(ms_hash($_POST['newpwd'])) . '\'
		WHERE username=\'' . $db->escape($_POST['un']) . '\'') or error('Failed to update password', __FILE__, __LINE__, $db->error());
		$result = $db->query('SELECT id FROM users
		WHERE username=\'' . $db->escape($_POST['un']) . '\'') or error('Failed to get user ID', __FILE__, __LINE__, $db->error());
		$uinfo = $db->fetch_assoc($result);
		$_SESSION['uid'] = $uinfo['id'];
		header('Location: /'); die;
	}
}
?>
<h2>Forgot password</h2>
<?php if (!empty($errors)) {
	echo '<h4>The following errors need to be fixed:</h4>';
	echo '<ul>';
	foreach ($errors as $val) {
		echo '<li>' . $val . '</li>';
	}
	echo '</ul>';
} ?>
<form action="/forgot" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Username</td>
			<td><input type="text" name="un" value="<?php echo $_POST['un']; ?>" /></td>
		</tr>
		<tr>
			<td>New password</td>
			<td><input type="password" name="newpwd" /></td>
		</tr>
		<tr>
			<td>Confirm new password</td>
			<td><input type="password" name="cnewpwd" /></td>
		</tr>
		<tr>
			<td>Scratch password</td>
			<td><input type="password" name="scratchpwd" /></td>
		</tr>
	</table>
	<p><input type="submit" value="Reset password" /></p>
</form>