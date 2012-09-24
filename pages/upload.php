<?php
$page_title = 'Upload - Mod Share';
if (isset($_POST['title'])) {
	if (isset($_POST['project'])) {
		if(isset($_POST['pwd'])) {
			// check if user/pwd combo is correct
			$post_user = $_POST['un'];
			$post_pwd = ms_hash($_POST['pwd']);
			$usercheck = $db->query("SELECT id, status FROM users WHERE username = '$post_user' AND password_hash = '$post_pwd'") or die('error');
			if($db->num_rows($usercheck) == 1) {
				$unresult = $db->fetch_assoc($usercheck);
				if($unresult['status'] != 'normal') {
					die('banned');
				}
				$db->query('INSERT INTO projects(title,filename,description,license,uploaded_by,modification,time,ip_addr)
				VALUES(\'' . $db->escape($_POST['title']) . '\',
				\'' . $db->escape($_POST['title'] . '.' . $modlist[$_POST['mod']]['extension']) . '\',
				\'' . $db->escape($_POST['description']) . '\',
				\'' . $db->escape($_POST['license']) . '\',
				' . $unresult['id'] . ',
				\'' . $db->escape($_POST['mod']) . '\',
				' . time() . ',
				\'' . $_SERVER['REMOTE_ADDR'] . '\')') or die('error' . $db->error());
				file_put_contents(SRV_ROOT . '/data/projects/' . $db->insert_id(), $_POST['project']);
				// make thumbnail
				include SRV_ROOT . '/includes/thumbnail.php';
				if(isset($_POST['thumbnail'])) {
					$thumb = $_POST['thumbnail'];
				} else {
					$thumb = makeThumbnailFromProj(SRV_ROOT . '/data/projects/' . $db->insert_id());
				}
				$db->query('UPDATE projects SET thumbnail=\'' . $db->escape($thumb) . '\' WHERE id=' . $db->insert_id());
				echo 'success';
			} else {
				die('badlogin');
			}
		} else {
			$db->query('INSERT INTO uploadqueue(username,time,description,modification,license,title)
			VALUES(\'' . $db->escape($_POST['un']) . '\',
			' . time() . ',
			\'' . $db->escape($_POST['description']) . '\',
			\'' . $db->escape($_POST['mod']) . '\',
			\'' . $db->escape($_POST['license']) . '\',
			\'' . $db->escape($_POST['title']) . '\')') or file_put_contents(SRV_ROOT . '/error.txt', $db->error() . "\n" . $_POST['description']);
			file_put_contents(SRV_ROOT . '/data/uploadqueue/' . $db->insert_id(), $_POST['project']);
			echo 'queued';
		}
	}
	if ((isset($_POST['data']) || $_FILES['pfile']['error'] === 0) && $_FILES['thumbnail']['error'] == '0') {
		$ext = explode('.', $_FILES['pfile']['name']);
		$ext = end($ext);
		if ($modlist[$_POST['mod']]['extension'] != $ext) {
			echo 'Invalid file extension.';
			return;
		}
		$thumbnail_file = file_get_contents($_FILES['thumbnail']['tmp_name']);
		$db->query('INSERT INTO projects(title,filename,license,uploaded_by,modification,thumbnail,description,time,ip_addr)
		VALUES(\'' . $db->escape($_POST['title']) . '\',
		\'' . $db->escape($_FILES['pfile']['name']) . '\',
		\'' . $_POST['license'] . '\',
		' . $ms_user['id'] . ',
		\'' . $_POST['mod'] . '\',
		\'' . $db->escape($thumbnail_file) . '\',
		\''. $db->escape($_POST['desc']) . '\',
		' . time() . ',
		\'' . $db->escape($_SERVER['REMOTE_ADDR']) . '\')') or error('Failed to upload project', __FILE__, __LINE__, $db->error());
		move_uploaded_file($_FILES['pfile']['tmp_name'], SRV_ROOT . '/data/projects/' . $db->insert_id());
		
		addlog('Project ' . $db->insert_id() . ' uploaded: ' . $_POST['title']);
		header('Location: /projects/' . $ms_user['username'] . '/' . $db->insert_id()); die;
	} else {
		$problem = true;
	}
}
if (!$ms_user['valid']) {
	echo 'failed';
}
?>
<h2>Upload to Mod Share</h2>
<?php
if ($problem) {
	if (!is_uploaded_file($_FILES['pfile'])) {
		echo '<p>No project file found</p>';
	}
	if (!is_uploaded_file($_FILES['thumbnail'])) {
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
						echo '<option value="' . $key . '">' . $val['name'] . '</option>';
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
	<input type="submit" value="Upload" />
</form>