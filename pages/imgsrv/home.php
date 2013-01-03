<?php
$getid = $ms_user['id'];
if ($ms_user['is_admin'] && isset($_GET['uid'])) {
	$getid = $_GET['uid'];
}
if (!file_exists(SRV_ROOT . '/data/imgsrv/' . $getid)) {
	mkdir(SRV_ROOT . '/data/imgsrv/' . $getid);
}
if (is_uploaded_file($_FILES['image']['tmp_name'])) {
	$imageExts = array('png', 'jpg', 'jpeg', 'gif', 'bmp', 'ico');
	$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
	if (!in_array($ext, $imageExts)) {
		echo '<p>Invalid file extension</p>';
	} else {
		$db->query('INSERT INTO imgsrv(user,filename,uploaded)
		VALUES(' . $ms_user['id'] . ',\'' . $db->escape($_FILES['image']['name']) . '\',' . $_SERVER['REQUEST_TIME'] . ')') or error('Failed to insert image service entry', __FILE__, __LINE__, $db->error());
		move_uploaded_file($_FILES['image']['tmp_name'], SRV_ROOT . '/data/imgsrv/' . $getid . '/' . $_FILES['image']['name']);
	}
}
if (isset($_POST['awesomescreenshoturl']) && $ms_user['is_admin']) {
	if (preg_match('%^http://awesomescreenshot.com/%', $_POST['awesomescreenshoturl'])) {
		$fc = file_get_contents($_POST['awesomescreenshoturl']);
		preg_match('%<img id="screenshot" class="collapsed" src="(.*?)" alt=""/>%', $fc, $matches);
		if (isset($matches[1])) {
			copy($matches[1], SRV_ROOT . '/data/imgsrv/' . $ms_user['id'] . '/screenshot-' . $_SERVER['REQUEST_TIME'] . '.png');
		}
		$db->query('INSERT INTO imgsrv(user,filename,uploaded,comments)
		VALUES(' . $ms_user['id'] . ',\'' . $db->escape('screenshot-' . $_SERVER['REQUEST_TIME'] . '.png') . '\',' . $_SERVER['REQUEST_TIME'] . ',\'\')') or error('Failed to insert image info into database', __FILE__, __LINE__, $db->error());
	} else {
		echo '<p>Invalid URL</p>';
	}
}
if (isset($_POST['delfile'])) {
	unlink(SRV_ROOT . '/data/imgsrv/' . $ms_user['id'] . '/' . basename($_POST['delfile']));
	$db->query('DELETE FROM imgsrv WHERE id=' . intval($_POST['delid']) . ' AND user=' . $ms_user['id']) or error('Failed to delete image', __FILE__, __LINE__, $db->error());
}
$page_title = 'Image Service - Mod Share';
?>
<h2>Mod Share Image Service</h2>
<form action="/imgsrv" method="post" enctype="multipart/form-data">
	<h3>Upload an image</h3>
	<p>Please note that uploading any inappropriate images will result in an immediate ban from this site.</p>
	<p><input type="file" name="image" /> <input type="submit" value="Upload" /></p>
</form>
<?php
if ($ms_user['is_admin']) {
	?>
<form action="/imgsrv" method="post" enctype="multipart/form-data">
	<h3>From awesomescreenshot.com</h3>
	<p><input type="text" name="awesomescreenshoturl" /> <input type="submit" value="Import" /></p>
</form>
	<?php
}
?>
<h3>Your images</h3>
<table border="1">
<tr>
	<th>Filename</th>
	<th>BBCode</th>
	<th>Uploaded</th>
	<th>Delete</th>
</tr>
<?php
$result = $db->query('SELECT filename,uploaded,id FROM imgsrv
WHERE user=' . $getid . '
ORDER BY filename ASC') or error('Failed to get users', __FILE__, __LINE__, $db->error());
while ($image = $db->fetch_assoc($result)) {
	echo '<tr>
		<td><a href="/imgsrv/view/' . $getid . '/' . clearHTML($image['filename']) . '">' . clearHTML($image['filename']) . '</a></td>
		<td><code>[img]http://' . $_SERVER['HTTP_HOST'] . '/imgsrv/view/' . $getid . '/' . clearHTML($image['filename']) . '[/img]</code></td>
		<td>' . format_date($image['uploaded']) . '</td>
		<th><form style="display:inline" action="/imgsrv' . (isset($_GET['uid']) ? '?uid=' . $getid : '') . '" method="post" enctype="multipart/form-data"><input type="hidden" name="delfile" value="' . clearHTML($image['filename']) . '" /><input type="hidden" name="delid" value="' . $image['id'] . '" /><input type="submit" value="Delete" /></form></th>
	</tr>';
}
?>
</table>