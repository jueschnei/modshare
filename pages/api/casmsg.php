<?php
// Central Authentication System Messaging Utility for Scratch
// Use this tool to communicate private messages to members of the Scratch community
// The tool is linked with Mod Share and allows only admins to prepare messages

// Check whether the user is visiting for a message (message ID provided?)
if(is_numeric($dirs[2]) && (intval($dirs[2]) == $dirs[2]) && isset($dirs[2])) {
	// Look up the message in the database where id=$dirs[2]
	$msgid = intval($dirs[2]);
	$res = $db->query("SELECT id,recipient,title,message,read_time FROM casmsg
		WHERE id = $msgid") or error('Failed to retrieve message');
	if($db->num_rows($res)) {
		// Message with provided ID exists
		$data = $db->fetch_assoc($res);
		// Has the password already been entered for the recipient of the message?
		// Or let admins view it regardless
		if(isset($_POST['msgpassword']) || $ms_user['is_admin']) {
			// Check for actions (edit + delete) by admin
			if(($dirs[3] == 'delete') && $ms_user['is_admin']) {
				// Delete communication
			} elseif(($dirs[3] == 'edit') && $ms_user['is_admin']) {
				// Edit communication
			} else {
				// No action -> Display message, but first...
				// Check whether the password is correct
				if(strstr(file_get_contents('http://scratch.mit.edu/api/authenticateuser?username=' . rawurlencode($data['recipient']) . '&password=' . rawurlencode($_POST['msgpassword'])), 'unblocked') || $ms_user['is_admin']) {
					// Display the message
					echo '<h2>' . $data['title'] . ($ms_user['is_admin'] ? ' - to ' . $data['recipient'] : '') . '</h2>';
					if($ms_user['is_admin']) {
						if($data['read_time'] == 0) {
							$timeread = 'never';
						} else {
							$timeread = gmdate('d-M-y H:i', $data['read_time']) . ' UTC';
						}
						echo 'Read ' . $timeread . ' &bull; <a href="/casmessage/' . $msgid . '/edit">Edit</a> &bull; <a href="/casmessage/' . $msgid . '/delete">Delete</a>';
					}
					echo '<p>' . $data['message'] . '</p>';
					// Update the read_time field in database (if reader is not an admin)
					if(!$ms_user['is_admin']) {
						$db->query("UPDATE casmsg SET read_time = " . time() . ' WHERE id=' . $msgid) or error('Failed to update read time: ' . $db->error());
					}
				} else {
					// Show error message
					echo '<p>After contacting the Scratch servers, the page could not authenticate your user. Please check whether the servers are up and try again.</p>';
				}
			}
		} else {
			// Ask for the password
			echo '<h2>Scratch CAS Messaging Utility</h2>';
			echo '<p>This message is intended for <strong>' . $data['recipient'] . '</strong>.</p>';
			echo '<p>To view the communication, you must enter the password of that Scratch user in the textbox below.</p>';
			echo '<form action="/casmessage/' . $msgid . '" method="post" enctype="multipart/form-data" name="caspass">
			<input name="msgpassword" type="password" /><input name="go" type="submit" value="Submit" />
			</form>';
			// Show the privacy policy
			echo '<h4>We respect your privacy</h4>';
			echo '<p>This utility uses one MySQL table and stores 4 values: the recipient of the message, the title and contents, and the time at which the message was read.
				 The passwords are never stored in the database and are destroyed as soon as the script ends.
				 No cookies are stored in the browser other than those used by Mod Share (see its privacy policy for details).
				 No other data is stored about the recipient (including IP address, user agent, HTTP referer).
				 Please note that Mod Share is completely independent of this service and may have a different policy.</p>
				 <p>This service is not endorsed by Scratch.</p>';
		}
	} else {
		// There is no message with that ID
		echo '<p>The given message ID pointed to no existing communication. Check the url.</p>';
	}
} else {
	// No message ID provided
	if($ms_user['is_admin']) {
		// Check whether a message has just been created
		if(isset($_POST['newmessagesubmit'])) {
			$title = $_POST['title'];
			$recipient = $_POST['recipient'];
			$message = $db->escape($_POST['contents']);
			$db->query("INSERT INTO casmsg(recipient,title,message)
						VALUES ('$recipient','$title','$message')") or error('Failed to add message to database',__FILE__, 0, $db->error());
			echo '<p>Successfully created message! <a href="/casmessage/' . $db->insert_id() . '">View?</a></p>';
		}
		// Show the admin messaging console
		echo '<h2>Scratch CAS Messaging Utility - Admin Panel</h2>';
		echo '<h4>Create new message</h4>';
		echo '<form action="/casmessage" method="post" enctype="multipart/form-data" name="makenew">
		Title: <input name="title" type="text" /><br />
		Recipient: <input name="recipient" type="text" /><br />
		Message:<br />
		<textarea name="contents" cols="50" rows="10"></textarea><br />
		<input name="newmessagesubmit" type="submit" value="Create" />
		</form>';
		// Show past messages
		echo '<h4>Past messages</h4>';
		$res = $db->query("SELECT id,recipient,title,read_time FROM casmsg") or error('Failed to retrieve messages');
		if($db->num_rows($res)) {
			// Loop through past messages and display details in table
			echo '<table style="width: 100%;"><tr><th>ID</th><th>Recipient</th><th>Title</th><th>Read (GMT)</th></tr>';
			while($msg = $db->fetch_assoc($res)) {
				echo '<tr><td>' . $msg['id'] . '</td><td>';
				echo $msg['recipient'] . '</td><td>';
				echo $msg['title'] . '</td><td>';
				if($msg['read_time'] == 0) {
					$readtime = 'Never';
				} else {
					$readtime = gmdate('d/M/y H:i', $msg['read_time']);					
				}
				echo '<a href="/casmessage/' . $msg['id'] . '">' . $readtime . '</a></td></tr>';
			}
			echo '</table>';
		} else {
			// No past messages
			echo 'No past communications found in database.';
		}
	} else {
		// Show some info about the cas messaging utility
		echo '<h2>Scratch CAS Messaging Utility</h2>';
	}
}
?>