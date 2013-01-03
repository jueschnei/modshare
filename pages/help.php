<?php
include SRV_ROOT . '/includes/recaptchalib.php';
define('RECAPTCHA_PUBLIC_KEY', '6LcgIdgSAAAAAEWv45j_z6es_5bBE6IMD9qgxCA7');
define('RECAPTCHA_PRIVATE_KEY', '6LcgIdgSAAAAAHtw8gFGNQinC4QWlc3Ae2vCZhRR');
$page_title = 'Contact us - Mod Share';
if ($ms_config['lasthelp'] > time() - 60 * 1) {
	echo '<p>A message has already been sent recently. Please come back later.</p>';
	return;
}
if (isset($_POST['form_sent'])) {
	if ($_POST['confirm_pass'] != '') {
		echo '<p>Get off our website. Now.</p>';
		$db->query('INSERT INTO bans(user_id,ip,expires,message,type) VALUES(0,\'' . $db->escape($_SERVER['REMOTE_ADDR']) . '\',' . (time() + 60 * 60 * 24 * 7) . ',\'You have been banned for spamming the contact form.\',\'full\')') or error('Failed to ban spammer', __FILE__, __LINE__, $db->error());
		die;
	}
	$email_ok = preg_match('%^(.*)@(.*?)\.(.*?)$%', $_POST['email']);
	if ($email_ok) {
		$captcha_ok = true;
		$resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		if ($resp->is_valid) {
			set_config('lasthelp', time());
			if ($ms_user['banned']) {
				$to = 'Jacob G. <jacob-x@jvvgindustries.com>';
			} else {
				$to = 'Jacob G. <jacob-x@jvvgindustries.com>, LS97 <silvisoft@ymail.com>';
			}
			mail($to,
				$_POST['subject'],
				'<p>' . clearHTML($_POST['msg'], true) . '</p>
				<h4>Details</h4>
				<p>Sent from IP <a href="http://' . $_SERVER['HTTP_HOST'] . '/admin/search_ip/' . $_SERVER['REMOTE_ADDR'] . '">' . $_SERVER['REMOTE_ADDR'] . '</a>' . ($ms_user['valid'] ? '<br />Logged in as user ' . parse_username($ms_user, true) : '') . '<br />HTTP_USER_AGENT: ' . $_SERVER['HTTP_USER_AGENT'] . ($ms_user['banned'] ? '<br /><b style="color:#F00">Note: user is banned</b>' : '') . '</p>',
				'From: ' . strip_tags($_POST['name']) . '<' . strip_tags($_POST['email']) . '>' . "\r\n" . 'Content-type: text/html'
			);
			echo '<p>Thank you for contacting us. We will reply to your email ASAP. Normally we check our email at least once every day, but sometimes it takes longer.</p>';
			return;
		} else {
			$captcha_ok = false;
		}
	}
}
?>
<h2>Contact us</h2>
<?php
if (isset($_POST['form_sent']) && !$email_ok) {
	echo '<p>Email address was <i>not</i> acceptable. Please enter a real email address.</p>';
}
if (isset($_POST['form_sent']) && !$captcha_ok) {
	echo '<p>The CAPTCHA you entered was not valid. Please try again.</p>';
}
?>
<form action="/help" method="post" enctype="multipart/form-data" <?php if ($ms_user['banned']) echo 'onsubmit="return confirm(\'Are you sure that the email address you entered is valid? If not, then we will not be able to respond and will discard your message.\')"'; ?>>
    <table border="0">
        <tr>
            <td>Your name</td>
            <td><input type="text" name="name" value="<?php echo clearHTML($_POST['name']); ?>" /></td>
        </tr>
        <tr>
            <td>Your email address</td>
            <td><input type="text" name="email" value="<?php echo clearHTML($_POST['email']); ?>" /><?php if ($ms_user['banned']) echo '<br />If the email address you provide is not a real email address, then we will discard your email.'; ?></td>
        </tr>
        	<tr>
            	<td>Subject</td>
            	<td><input type="text" name="subject" value="<?php echo clearHTML($_POST['subject']); ?>" /></td>
       	</tr>
        	<tr>
            	<td>Your message</td>
            	<td><textarea rows="7" cols="60" name="msg"><?php echo clearHTML($_POST['msg']); ?></textarea></td>
       	</tr>
		<tr>
			<td>CAPTCHA</td>
			<td><?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY); ?></td>
		</tr>
		<td style="display:none">
			<td>Verify this</td>
			<td><input type="password" name="confirm_pass" /></td>
		</td>
    </table>
    <input type="submit" name="form_sent" value="Send email" />
</form>