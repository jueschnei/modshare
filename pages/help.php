<?php
$page_title = 'Contact us - Mod Share';
if (isset($_POST['form_sent'])) {
	if ($ms_config['lasthelp'] > time() - 60 * 5) {
		echo '<p>A message has already been sent recently. Please come back later.</p>';
		return;
	}
	set_config('lasthelp', time());
	mail('Jacob G. <jacob-x@jvvgindustries.com>, SilviSoft <silvisoft@ymail.com>',
		$_POST['subject'],
		'<p>' . clearHTML($_POST['msg'], true) . '</p>
		<h4>Details</h4>
		<p>Sent from IP <a href="http://' . $_SERVER['HTTP_HOST'] . '/admin/search_ip/' . $_SERVER['REMOTE_ADDR'] . '">' . $_SERVER['REMOTE_ADDR'] . '</a>' . ($ms_user['valid'] ? '<br />Logged in as user ' . parse_username($ms_user) : '') . '<br />HTTP_USER_AGENT: ' . $_SERVER['HTTP_USER_AGENT'] . ($ms_user['banned'] ? '<br /><b style="color:#F00">Note: user is banned</b>' : '') . '</p>',
		'From: ' . strip_tags($_POST['name']) . '<' . strip_tags($_POST['email']) . '>' . "\r\n" . 'Content-type: text/html'
	);
	echo '<p>Thank you for contacting us. We will reply to your email ASAP.</p>';
	return;
}
?>
<h2>Contact us</h2>
<p>Note: due to bugs with this, we recommend contacting us directly.</p>
<?php
if ($ms_user['valid'] || $ms_user['banned']) { ?>
<p>Please email is at "helpdesk [at] futuresight [dot] org"</p>
<?php return;
} ?>
<form action="/help" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Your name</td>
			<td><input type="text" name="name" /></td>
		</tr>
		<tr>
			<td>Your email address</td>
			<td><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td>Subject</td>
			<td><input type="text" name="subject" /></td>
		</tr>
		<tr>
			<td>Your message</td>
			<td><textarea rows="7" cols="60" name="msg"></textarea></td>
		</tr>
	</table>
	<input type="submit" name="form_sent" value="Send email" />
</form>