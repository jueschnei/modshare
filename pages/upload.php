<?php
$page_title = 'Upload - Mod Share';
if (isset($_POST['title'])) {
	if (isset($_POST['project'])) {
		ob_end_clean();
		if(isset($_POST['pwd'])) {
			// check if user/pwd combo is correct
			$post_user = $db->escape($_POST['un']);
			$post_pwd = $db->escape(ms_hash($_POST['pwd']));
			$usercheck = $db->query('SELECT id, status FROM users WHERE username = \'' . $post_user . '\' AND password_hash=\'' . $post_pwd . '\'') or die('error-' . __LINE__ . '-' . $db->error());
			if($db->num_rows($usercheck) == 1) {
				$unresult = $db->fetch_assoc($usercheck);
				$ban_result = $db->query('SELECT 1 FROM bans
				WHERE (user_id=' . $unresult['id'] . '
				OR ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'
				OR ip LIKE \'%,' . $_SERVER['REMOTE_ADDR'] . '\'
				OR ip LIKE \'%,' . $_SERVER['REMOTE_ADDR'] . ',%\'
				OR IP LIKE \'' . $_SERVER['REMOTE_ADDR'] . ',%\')
				AND expires>' . time()) or die('Failed to check bans-' . __LINE__ . '-' . $db->error());
				if($unresult['status'] != 'normal' || $db->num_rows($ban_result)) {
					die('banned');
				}
				$result = $db->query('SELECT id FROM projects
				WHERE title=\'' . $db->escape($_POST['title']) . '\'
				AND uploaded_by=' . $unresult['id']) or die('error' . $db->error() . '-' . __LINE__);
				if ($db->num_rows($result)) {
					list($id) = $db->fetch_row($result);
					$db->query('UPDATE projects
					SET status=\'normal\',time=' . $_SERVER['REQUEST_TIME'] . '
					WHERE title=\'' . $db->escape($_POST['title']) . '\'
					AND uploaded_by=' . $unresult['id']) or die('error' . $db->error() . '-' . __LINE__);
				} else {
					$db->query('INSERT INTO projects(title,filename,description,license,uploaded_by,modification,time,ip_addr)
					VALUES(\'' . $db->escape($_POST['title']) . '\',
					\'' . $db->escape($_POST['title'] . '.' . $modlist[$_POST['mod']]['extension']) . '\',
					\'' . $db->escape($_POST['description']) . '\',
					\'' . $db->escape($_POST['license']) . '\',
					' . $unresult['id'] . ',
					\'' . $db->escape($_POST['mod']) . '\',
					' . time() . ',
					\'' . $_SERVER['REMOTE_ADDR'] . '\')') or die('error' . $db->error());
					$id = $db->insert_id();
				}
				file_put_contents(SRV_ROOT . '/data/projects/' . $id, $_POST['project']);
				// make thumbnail
				include SRV_ROOT . '/includes/thumbnail.php';
				if(isset($_POST['thumbnail'])) {
					$thumb = $_POST['thumbnail'];
				} else {
					$thumb = makeThumbnailFromProj(SRV_ROOT . '/data/projects/' . $id);
				}
				$db->query('UPDATE projects SET thumbnail=\'' . $db->escape($thumb) . '\' WHERE id=' . $id);
				echo 'success'; die;
			} else {
				die('badlogin');
			}
			//add search entry
			$words = array_merge(split_into_words($_POST['title']), split_into_words($_POST['description']));
			foreach ($words as &$val) {
				$val = '(' . $id . ',\'' . $db->escape($val) . '\')';
			}
			$db->query('INSERT INTO search_index(project,word)
			VALUES' . implode(',', $words)) or error('Failed to update search entry', __FILE__, __LINE__, $db->error());
			
		} else {
			echo 'failed - queue not supported';
		}
		unlink(SRV_ROOT . '/cache/cache_projects.php');
	}
	if ((isset($_POST['data']) || $_FILES['pfile']['error'] === 0) && $_FILES['thumbnail']['error'] == '0') {
		$ext = explode('.', $_FILES['pfile']['name']);
		$ext = end($ext);
		if (containsBadWords($_POST['desc'])) {
			echo 'Please do not use inappropriate words on Mod Share.'; return;
		}
		if ($modlist[$_POST['mod']]['extension'] != $ext) {
			echo 'Invalid file extension.';
			return;
		}
		$db->query('INSERT INTO projects(title,filename,license,uploaded_by,modification,description,time,ip_addr)
		VALUES(\'' . $db->escape($_POST['title']) . '\',
		\'' . $db->escape($_FILES['pfile']['name']) . '\',
		\'' . $_POST['license'] . '\',
		' . $ms_user['id'] . ',
		\'' . $_POST['mod'] . '\',
		\''. $db->escape($_POST['desc']) . '\',
		' . time() . ',
		\'' . $db->escape($_SERVER['REMOTE_ADDR']) . '\')') or error('Failed to upload project', __FILE__, __LINE__, $db->error());
		move_uploaded_file($_FILES['thumbnail']['tmp_name'], SRV_ROOT . '/data/icons/project/' . $db->insert_id() . '.png');
		move_uploaded_file($_FILES['pfile']['tmp_name'], SRV_ROOT . '/data/projects/' . $db->insert_id());
		
		addlog('Project ' . $db->insert_id() . ' uploaded: ' . $_POST['title']);
		unlink(SRV_ROOT . '/cache/cache_projects.php');
		
		//add search entry
		$words = array_merge(split_into_words($_POST['title']), split_into_words($_POST['desc']));
		foreach ($words as &$val) {
			$val = '(' . $db->insert_id() . ',\'' . $db->escape($val) . '\')';
		}
		$db->query('INSERT INTO search_index(project,word)
		VALUES' . implode(',', $words)) or error('Failed to update search entry', __FILE__, __LINE__, $db->error());
		
		header('Location: /projects/' . $ms_user['username'] . '/' . $db->insert_id()); die;
	} else {
		$problem = true;
	}
}
if (!$ms_user['valid']) {
	echo 'You lack permission to view this page.';
	return;
}
?>
<h2>Upload to Mod Share</h2>
<?php
if ($problem) {
	if ($_FILES['pfile']['error'] !== 0) {
		echo '<p>No project file found</p>';
	}
	if ($_FILES['thumbnail']['error'] !== 0) {
		echo '<p>No thumbnail file found</p>';
	}
}
?>
<form action="/upload" method="post" enctype="multipart/form-data">
	<table border="0">
		<tr>
			<td>Project title</td>
			<td><input type="text" name="title" /></td>
		</tr>
		<tr>
			<td>Project description</td>
			<td><textarea name="desc" rows="6" cols="50"></textarea></td>
		</tr>
		<tr>
			<td>Project file</td>
			<td><input type="file" name="pfile" /></td>
		</tr>
		<tr>
			<td>Project thumbnail image</td>
			<td><input type="file" name="thumbnail" /></td>
		</tr>
		<tr>
			<td>Mod</td>
			<td>
				<select name="mod">
					<?php
					foreach ($modlist as $key => $val) {
						echo '<option value="' . $key . '">' . strip_tags($val['name']) . '</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Project license</td>
			<td>
				<input type="radio" name="license" value="ms" id="msl" checked="checked" /><label for="msl">Mod Share Project License</label><br />
				<input type="radio" name="license" value="cc" id="ccl" /><label for="ccl">Creative Commons License</label><br />
				<input type="radio" name="license" value="pd" id="pdl" /><label for="pdl">Public Domain</label>
			</td>
		</tr>
	</table>
	<p><input type="submit" value="Upload" /></p>
</form>