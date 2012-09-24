<?php
$page_title = 'Maintenance - Mod Share';
if (isset($_POST['daysold'])) {
	$handle = opendir(SRV_ROOT . '/sessions');
	while ($file = readdir($handle)) {
		if ($file != '.' && $file != '..') {
			$path = SRV_ROOT . '/sessions/' . $file;
			$contents = file_get_contents($path);
			$arr = explode(';', $contents);
			$arr = explode(':', $arr[0]);
			$last = $arr[1];
			$time = $arr[1];
			if (!$time || $time < $_SERVER['REQUEST_TIME'] - 60 * 60 * 24 * intval($_POST['daysold'])) {
				unlink($path);
			}
		}
	}
	closedir($handle);
}
?>
<h2>Maintenance</h2>
<h3>Remove old sessions</h3>
<form action="/admin/maintenance" method="post" enctype="multipart/form-data">
	<p>Remove sessions older than <input type="text" name="daysold" size="1" value="7" /> day(s) old. <input type="submit" value="Go" /></p>
</form>