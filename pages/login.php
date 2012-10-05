<?php
if ($url == '/logout') {
	session_destroy();
	header('Location: /'); die;
}
if (isset($_SESSION['uid'])) {
	header('Location: /'); die;
}
$page_title = 'Log in - Mod Share';

session_regenerate_id(); //regenerate session ID for security purposes
if (isset($_POST['un'])) {
	$result = $db->query('SELECT id FROM users
	WHERE LOWER(username) = LOWER(\'' . $db->escape($_POST['un']) . '\')
	AND password_hash=\'' . $db->escape(ms_hash($_POST['pwd'])) . '\'
	AND status=\'normal\'') or error('Failed to check user', __FILE__, __LINE__, $db->error()); //user exists?
	if ($db->num_rows($result)) {
		//good login, let's continue
		$user_info = $db->fetch_assoc($result);
		$_SESSION['uid'] = $user_info['id'];
		
		addlog('User ' . $user_info['id'] . ' logged in');
		header('Location: /'); die;
	} else {
		$result = $db->query('SELECT id, password_hash FROM users WHERE LOWER(username) = LOWER(\'' . $db->escape($_POST['un']) . '\')');
		if ($db->num_rows($result)) {
			$user_info = $db->fetch_assoc($result);
			if($user_info['password_hash'] == 'reset') {
				header('Location: /forgot'); die;
			}
			//bad login, apprise the user of the situation
			echo '<h2>Invalid login</h2>
			<p>Invalid username or password. Hit the back button to try again.</p>
			<p><a href="/forgot">Forgot password?</a></p>';
		}
	}
} else {
?>
<h2>Log in</h2>
<p>Not registered yet? <a href="/register">Sign up!</a></p>
<form action="/login" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Username</td>
			<td><input type="text" name="un" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="pwd" /></td>
		</tr>
	</table>
	<input type="submit" value="Log in" />
</form>
<?php
}
?>