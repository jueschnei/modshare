<?php
if ($url == '/users' || $url == '/users/') {
	include SRV_ROOT . '/pages/userlist.php';
	return;
}
if (isset($_POST['form_sent'])) {
	if ($_FILES['avatar']['error'] === 0) {
		move_uploaded_file($_FILES['avatar']['tmp_name'], SRV_ROOT . '/data/icons/user/' . $ms_user['id'] . '.png');
	}
	$db->query('UPDATE users SET style_col=\'' . $db->escape($_POST['stylecol']) . '\',timezone=' . intval($_POST['tzone']) . ',style_logo=\'' . $db->escape($_POST['stylelogo']) . '\'
	WHERE id=' . $ms_user['id']) or error('Failed to update style color', __FILE__, __LINE__, $db->error());
}
$user = $dirs[2];
if (isset($_POST['permission']) && $ms_user['is_admin']) {
	$db->query('UPDATE users
	SET permission=' . intval($_POST['permission']) . ',imgsrv=' . intval($_POST['imgsrv']) . '
	WHERE LOWER(username) = LOWER(\'' . $db->escape($user) . '\')') or error('Failed to update user info', __FILE__, __LINE__, $db->error());
	header('Refresh: 0'); die;
}
if ($user == $ms_user['username']) {
	$page_title = 'My Stuff - Mod Share';
	$me = true;
} else {
	$page_title = clearHTML($user) . ' - Mod Share';
	$me = false;
}
$result = $db->query('SELECT id,permission,style_col,style_logo,status,registration_ip,registered,imgsrv,featured_project FROM users
WHERE username=\'' . $db->escape($user) . '\'') or error('Failed to get user', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	ob_end_clean();
	header('HTTP/1.1 404 Not found');
	include SRV_ROOT . '/errorpages/404.php'; die;
}
$user_info = $db->fetch_assoc($result);
if ($user_info['status'] == 'disabledbyadmin') {
	header('HTTP/1.1 404 Not found');
	echo '<h2>' . clearHTML($user) . '</h2>
	<p>This account has been disabled by the Mod Share Team.</p>';
	if ($ms_user['is_admin']) {
		echo '<p><a href="/admin/delete_user/' . $user_info['id'] . '">Undelete </a></p>';
	}
	return;
}
if ($dirs[3] == 'removefriend') {
	$db->query('DELETE FROM friends
	WHERE friendee=' . intval($dirs[4]) . '
	AND friender=' . $ms_user['id']) or error('Failed to delete friend', __FILE__, __LINE__, $db->error());
	header('Location: /users/' . $dirs[2]); die;
}
if ($dirs[3] == 'addfriend') {
	$db->query('INSERT INTO friends(friender,friendee,time)
	VALUES(' . $ms_user['id'] . ','.  $user_info['id'] . ',' . time() . ')') or error('Failed to add friend', __FILE__, __LINE__, $db->error());
	header('Location: /users/' . $dirs[2]); die;
}
if ($me && $dirs[3] == 'accept') {
	$result = $db->query('SELECT title,description,modification,license FROM uploadqueue
	WHERE id=' . intval($dirs[4]) . '
	AND username=\'' . $db->escape($ms_user['username']) . '\'') or error('Failed to check if project is valid', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		include SRV_ROOT . '/includes/thumbnail.php';
		$thumb = makeThumbnailFromProj(SRV_ROOT . '/data/uploadqueue/' . intval($dirs[4]));
		$info = $db->fetch_assoc($result);
		$db->query('INSERT INTO projects(title,filename,description,license,uploaded_by,modification,time,ip_addr)
		VALUES(\'' . $db->escape($info['title']) . '\',
		\'' . $db->escape($info['title'] . '.' . $modlist[$info['modification']]['extension']) . '\',
		\'' . $db->escape($info['description']) . '\',
		\'' . $db->escape($info['license']) . '\',
		' . $ms_user['id'] . ',
		\'' . $db->escape($info['modification']) . '\',
		' . time() . ',
		\'' . $db->escape($_SERVER['REMOTE_ADDR']) . '\')') or error('Failed to move project', __FILE__, __LINE__, $db->error());
		rename(SRV_ROOT . '/data/uploadqueue/' . intval($dirs[4]), SRV_ROOT . '/data/projects/' . $db->insert_id());
		file_put_contents(SRV_ROOT . '/data/icons/project/' . $db->insert_id() . '.png', $thumb);
		
		//add search entry
		$words = array_merge(split_into_words($info['title']), split_into_words($info['description']));
		foreach ($words as &$val) {
			$val = '(' . $db->insert_id() . ',\'' . $db->escape($val) . '\')';
		}
		$db->query('INSERT INTO search_index(project,word)
		VALUES' . implode(',', $words)) or error('Failed to update search entry', __FILE__, __LINE__, $db->error());
		
		$db->query('DELETE FROM uploadqueue
		WHERE id=' . intval($dirs[4])) or error('Failed to delete queue entry', __FILE__, __LINE__, $db->error());
	}
}
if ($me && $dirs[3] == 'reject') {
	$db->query('DELETE FROM uploadqueue
	WHERE id=' . intval($dirs[4]) . '
	AND username=\'' . $db->escape($ms_user['username']) . '\'') or error('Failed to delete queue entry', __FILE__, __LINE__, $db->error());
	if(file_exists(SRV_ROOT . '/data/uploadqueue/' . intval($dirs[4]))) {
		unlink(SRV_ROOT . '/data/uploadqueue/' . intval($dirs[4]));
	}
}
if ($me) {
	$result = $db->query('SELECT title,id FROM uploadqueue
	WHERE username=\'' . $db->escape($ms_user['username']) . '\'') or error('Failed to see if you have direct-uploaded any projects', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		echo '<h3>Did you upload these projects recently?</h3>
		<ul>';
		while ($cur_project = $db->fetch_assoc($result)) {
			echo '<li>' . clearHTML($cur_project['title']) . ' -
			<a href="/users/' . $ms_user['username'] . '/accept/' . $cur_project['id'] . '">Yes</a> &bull; 
			<a href="/users/' . $ms_user['username'] . '/reject/' . $cur_project['id'] . '">No</a></li>';
		}
		echo '</ul>';
	}
}
if (isset($_GET['assumeid']) && $ms_user['is_admin']) {
	$_SESSION['uid'] = $user_info['id'];
	$_SESSION['origid'] = $ms_user['id'];
	echo '<meta http-equiv="Refresh" content="0; url=/" />';
}
if (isset($_GET['resetpwd']) && $ms_user['is_admin']) {
	$newpwd = md5(time());
	$db->query('UPDATE users
	SET password_hash=\'' . ms_hash($newpwd) . '\'
	WHERE id=' . $user_info['id']) or error('Failed to reset password', __FILE__, __LINE__, $db->error());
	echo 'Password reset! New password is "' . $newpwd . '"';
}
if (isset($_POST['newpwd']) && $me) {
	if ($_POST['newpwd'] != $_POST['cnewpwd']) {
		echo '<p>Passwords didn&apos;t match.</p>';
	} else {
		$db->query('UPDATE users
		SET password_hash=\'' . $db->escape(ms_hash($_POST['newpwd'])) . '\'
		WHERE id=' . $ms_user['id']) or error('Failed to update password', __FILE__, __LINE__, $db->error());
	}
}
?>
<table border="0">
	<tr>
		<td style="width: 150px; vertical-align: top">
			<p><img src="/data/icons/user/<?php echo $user_info['id']; ?>.png" alt="User avatar" width="120px" height="90px" /></p>
			<h2 class="userpageheading" style="text-align: center;"><?php echo clearHTML($user); ?></h2>
			<?php if ($ms_user['valid'] && !$me) {
				$result = $db->query('SELECT 1 FROM friends
				WHERE friender=' . $ms_user['id'] . '
				AND friendee=' . $user_info['id']) or error('Failed to check if user is already friends', __FILE__, __LINE__, $db->error());
				if (!$db->num_rows($result)) {
					echo '<p><a href="/users/' . $user . '/addfriend">Add as a friend</a></p>';
				}
			} ?>
			<h3>Friends</h3>
			
			<?php
			$result = $db->query('SELECT u.username,u.permission,u.id FROM friends AS f
			LEFT JOIN users AS u
			ON u.id=f.friendee
			WHERE f.friender=' . $user_info['id'] . '
			ORDER BY time DESC') or error('Failed to get friends list', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				echo '<ul id="friendslist">';
				while ($cur_friend = $db->fetch_assoc($result)) {
					echo '<li>';
					if ($me) {
						echo '<a href="/users/' . $user . '/removefriend/' . $cur_friend['id'] . '">[X]</a>';
					}
					echo '<img src="/data/icons/user/' . $cur_friend['id'] . '.png" width="32px" height="32px" alt="' . clearHTML($cur_friend['username']) . '&apos;s avatar" />' . parse_username($cur_friend) . '</li>';
				}
				echo '</ul>';
			} else {
				echo '<p>No friends yet</p>';
			}
			?>
		</td>
		<td>
            <?php
			if($user_info['featured_project'] != 0) {
				$result = $db->query('SELECT id,title,status,description FROM projects
				WHERE uploaded_by=' . $user_info['id'] . '
				' . ($ms_user['is_mod'] ? '' : 'AND status=\'normal\'') . '
				AND id='. $user_info['featured_project'] .'
				ORDER BY time DESC') or error('Failed to get featured project', __FILE__, __LINE__, $db->error());
				if ($db->num_rows($result)) {
					echo '<h2 class="userpageheading">Featured project</h2>';
					$cur_project = $db->fetch_assoc($result);
					echo '<table style="width: 100%;"><tr><td>
					<a href="/projects/' . clearHTML(rawurlencode($user)) . '/' . $cur_project['id'] . '">
						<img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /><br />
						' . clearHTML($cur_project['title']) . '
					</a>';
					if ($cur_project['status'] == 'blocked') {
						echo ' (b)';
					} else if ($cur_project['status'] == 'deleted') {
						echo ' (d)';
					}
					echo '</td><td>';
					if(strlen($cur_project['description']) > 140) {
						echo clearHTML(substr($cur_project['description'], 0, 140)) . '...';
					} else {
						echo clearHTML($cur_project['description']);
					}
					echo '</td></tr></table>';
				}
			}
			?>
			<h2 class="userpageheading"><?php if ($me) { echo 'My'; } else { echo $user . '&apos;s'; } ?> projects</h2>
			<?php
			$result = $db->query('SELECT id,title,status FROM projects
			WHERE uploaded_by=' . $user_info['id'] . '
			' . ($ms_user['is_mod'] ? '' : 'AND status=\'normal\'') . '
			ORDER BY time DESC') or error('Failed to get projects', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				echo '<table border="0">
				<tr>';
				$count = 0;
				while ($cur_project = $db->fetch_assoc($result)) {
					if ($count % 3 == 0 && $count > 0) {
						echo '</tr><tr>';
					}
					$count++;
					echo '
								<td style="text-align:center; max-width: 130px; vertical-align:top" ' . ($cur_project['status'] != 'normal' ? 'class="alphaover"' : '') . '>
									<a href="/projects/' . clearHTML(rawurlencode($user)) . '/' . $cur_project['id'] . '">
										<img src="/data/icons/project/' . $cur_project['id'] . '.png" width="120px" height="90px" alt="Project icon" /><br />
										' . clearHTML($cur_project['title']) . '
									</a>';
									if ($cur_project['status'] == 'blocked') {
										echo ' (b)';
									} else if ($cur_project['status'] == 'deleted') {
										echo ' (d)';
									}
									
									// if has privileges, allow modification of featured project
									if($me || $ms_user['is_admin']) {
										echo '<br /><sup><a href="/projects/' . clearHTML(rawurlencode($user)) . '/' . $cur_project['id'] . '/featureonuserpage">(Feature)</a></sup>';
									}
								echo '</td>
					';
				}
				echo '</tr>
			</table>';
			}
			?>
			<h2 class="userpageheading"><?php if ($me) { echo 'My'; } else { echo $user . '&apos;s'; } ?> favorites</h2>
			<?php
			$result = $db->query('SELECT p.id AS pid,u.username AS author,p.title FROM favorites AS f
			LEFT JOIN projects AS p
			ON p.id=f.project_id
			LEFT JOIN users AS u
			ON u.id=p.uploaded_by
			WHERE f.user_id=' . $user_info['id'] . '
			AND p.status=\'normal\'
			ORDER BY f.time DESC') or error('Failed to get favorites', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				echo '<table border="0">
				<tr>';
				$count = 0;
				while ($cur_favorite = $db->fetch_assoc($result)) {
					if ($count % 3 == 0 && $count > 0) {
						echo '</tr><tr>';
					}
					$count++;
					echo '
								<td style="text-align:center; max-width: 130px; vertical-align:top">
									<a href="/projects/' . clearHTML(rawurlencode($cur_favorite['author'])) . '/' . $cur_favorite['pid'] . '">
										<img src="/data/icons/project/' . $cur_favorite['pid'] . '.png" width="120px" height="90px" alt="Project icon" /><br />
										' . clearHTML($cur_favorite['title']) . '
									</a>
								</td>
					';
				}
				echo '</tr>
			</table>';
			}
			//galleries
			?>
			<h2 class="userpageheading"><?php if ($me) { echo 'My'; } else { echo $user . '&apos;s'; } ?> galleries</h2>
			<?php if ($me) { ?><p><a href="/newgallery">Create a new gallery</a></p><?php } ?>
			<?php
			$result = $db->query('SELECT name,url FROM galleries WHERE creator=' . $user_info['id']) or error('Failed to find galleries', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				echo '<ul>';
				while (list($name,$url) = $db->fetch_row($result)) {
					echo '<li><a href="/galleries/' . clearHTML(rawurlencode($url)) . '">' . clearHTML($name) . '</a></li>';
				}
				echo '</ul>';
			} else {
				echo '<p>No galleries</p>';
			}
			?>
			<?php if ($me) { ?>
			<h2 class="userpageheading">My settings</h2>
			<form action="<?php echo $url; ?>" method="post" enctype="multipart/form-data">
				<table border="0">
					<tr>
						<td>Update avatar</td>
						<td><input type="file" name="avatar" /></td>
					</tr>
					<tr>
						<td>Style colour</td>
						<td><input type="text" name="stylecol" value="<?php echo $user_info['style_col']; ?>" /></td>
					</tr>
					<tr>
						<td>Header logo</td>
					  <td>
						  <select name="stylelogo" id="stylelogo">
						    <option value="default" <?php if($user_info['style_logo'] == 'default') echo 'selected="selected"'; ?>>Default</option>
						    <option value="black" <?php if($user_info['style_logo'] == 'black') echo 'selected="selected"'; ?>>Black</option>
						    <option value="white" <?php if($user_info['style_logo'] == 'white') echo 'selected="selected"'; ?>>White</option>
						    <option value="red" <?php if($user_info['style_logo'] == 'red') echo 'selected="selected"'; ?>>Red</option>
						    <option value="green" <?php if($user_info['style_logo'] == 'green') echo 'selected="selected"'; ?>>Green</option>
						    <option value="blue" <?php if($user_info['style_logo'] == 'blue') echo 'selected="selected"'; ?>>Blue</option>
						    <option value="yellow" <?php if($user_info['style_logo'] == 'yellow') echo 'selected="selected"'; ?>>Yellow</option>
						    <option value="purple" <?php if($user_info['style_logo'] == 'purple') echo 'selected="selected"'; ?>>Purple</option>
		                </select></td>
					</tr>
					<tr>
						<td>Time zone</td>
						<td>
							<select name="tzone">
							<?php
							for ($i = -12; $i <= 12; $i++) {
								if ($i < 0) {
									$display = 'GMT' . $i;
								} else if ($i == 0) {
									$display = 'GMT';
								} else {
									$display = 'GMT+' . $i;
								}
								echo '<option value="' . $i . '"';
								if ($i == $ms_user['timezone']) {
									echo ' selected="selected"';
								}
								echo '>' . $display . '</option>';
							}
							?>
							</select>
						</td>
					</tr>
				</table>
			  <p><input type="submit" name="form_sent" value="Update" /></p>
			  <h3>Change password</h3>
			  <form action="/users/<?php echo $user; ?>" method="post" enctype="multipart/form-data">
				<table border="0">
					<tr>
						<td>New password</td>
						<td><input type="password" name="newpwd" /></td>
					</tr>
					<tr>
						<td>Confirm password</td>
						<td><input type="password" name="cnewpwd" /></td>
					</tr>
				</table>
				<p><input type="submit" value="Change password" />
			  </form>
			</form>
			<?php } ?>
			<?php if ($ms_user['is_mod']) { ?>
            <div class="roundedbevel">
                <h2>User administration</h2>
                <p>Registered on <?php echo format_date($user_info['registered']); ?> from IP <a href="/admin/search_ip/<?php echo $user_info['registration_ip']; ?>"><?php echo $user_info['registration_ip']; ?></a></p>
                <p><a href="/admin/ban_user/<?php echo $user_info['id']; ?>">Ban user</a> &bull; <a href="/admin/notify/<?php echo $user_info['id']; ?>">Send notification</a> &bull; <a href="/admin/history/<?php echo $user_info['id']; ?>">Admin history</a><?php
                $result = $db->query('SELECT 1 FROM bans
                WHERE user_id=' . $user_info['id']) or error('Failed to check if user is banned', __FILE__, __LINE__, $db->error()); 
                if ($db->num_rows($result)) {
                    echo ' - <b style="color:#F00">User is banned</b>';
                } ?><?php if ($ms_user['is_admin']) { ?> &bull; <a href="?assumeid">Assume identity</a> &bull; <a href="?resetpwd">Reset password</a><?php } ?></p>
                <?php if ($ms_user['is_admin']) { ?>
                <form action="<?php echo $url; ?>" method="post" enctype="multipart/form-data">
                    <table border="0">
                        <tr>
                            <td>User permission</td>
                            <td><select name="permission"><option value="1"<?php if ($user_info['permission'] == '1') echo ' selected="selected"'; ?>>Normal</option><option value="2"<?php if ($user_info['permission'] == '2') echo ' selected="selected"'; ?>>Moderator</option><option value="3"<?php if ($user_info['permission'] == '3') echo ' selected="selected"'; ?>>Administrator</option></select></td>
                        </tr>
                    </table>
                    <p><a href="/admin/delete_user/<?php echo $user_info['id']; ?>">Delete user</a></p>
                    <p>Image service allowed? <input type="radio" name="imgsrv" value="1" <?php if ($user_info['imgsrv']) echo ' checked="checked"'; ?> />Yes <input type="radio" name="imgsrv" value="0" <?php if (!$user_info['imgsrv']) echo ' checked="checked"'; ?> />No - <a href="/imgsrv?uid=<?php echo $user_info['id']; ?>">Uploaded images</a></p>
                    <p><input type="submit" value="Update admin stuff" /></p>
                </form>
            </div>
			<?php }
			} ?>
		</td>
	</tr>
</table>