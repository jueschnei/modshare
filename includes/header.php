<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title><$page_title/></title>
<link rel="stylesheet" type="text/css" href="/styles/default.css" />
<link rel="icon" href="/favicon.ico" />
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>
<body>
<div id="container">
<div id="hheader">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td id="homelinktd"><h1 id="homelink"><a href="/"><img src="<?php if($ms_user['valid']) {echo '/img/header/' . $ms_user['style_logo'] . '.png';} else {echo '/img/header/default.png';} ?>" height="28px" style="vertical-align: middle; border: none;" alt="Link to Mod Share" /> Mod Share</a></h1></td>
<td>&nbsp;</td>
<td align="center" class="ltd"><a href="/users/">Users</a></td>
<td align="center" class="ltd"><a href="/browse">Browse</a></td>
<td align="center" class="ltd"><a href="/upload">Upload</a></td>
<td align="center" class="ltd"><a href="/forums/">Forums</a></td>
<td align="center" class="ltd"><a href="/<?php if ($ms_user['valid']) { echo 'users/' . rawurlencode($ms_user['username']); } else { echo 'login'; } ?>"><?php if ($ms_user['valid']) { echo $ms_user['username']; } else { echo 'Log in'; } ?></a></td>
</tr>
</table>

</div>

<div id="sidebarcontainer">
<div id="sidebar">
<?php if ($ms_user['valid']) {
	echo '<table border="0" width="100%"><tr>';
	echo '<td style="width:33%" align="center"><a href="/help">Contact us</a></td>';
	echo '<td style="width:33%" align="center"><a href="/users/' . clearHTML(rawurlencode($ms_user['username'])) . '">My stuff</a></td>';
	echo '<td style="width:33%" align="center"><a href="/logout">Log out</a></td>';
	echo '</tr></table>';
} ?>
<div id="sidecontents">
<?php
if($ms_user['is_admin']) {
	echo '<h4>Admin News <a href="/admin/filesandnews/clearnews">(x)</a></h4>';
	echo $ms_config['adminnews'];
	echo '<h4>Files to Download <a href="/admin/filesandnews/clearfiles">(x)</a></h4><p>';
	$array = explode("\n", $ms_config['downloadfiles']);
	sort($array);
	foreach ($array as $key => $val) {
		if ($val == '')
			unset($array[$key]);
	}
	echo implode($array, '<br />');
	echo '</p><form action="/admin/filesandnews" id="admintextbox" method="post">';
	echo '<p><input type="text" name="news" onkeypress="adminnewsadd(event);" /></p>';
	echo '</form>';
	echo '<script type="text/javascript">';
	echo 'function adminnewsadd(e) {if(e.keyCode == 13) {document.getElementById("admintextbox").submit();}}';
	echo '</script>';
}
if (!$ms_user['valid']) { ?>
<form action="/login" method="post" enctype="multipart/form-data">
	<p>Log in or <a href="/register">register</a> to upload projects and access many more site features.</p>
	<p><input type="text" name="un" /><br />
	<input type="password" name="pwd" /><br />
	<input type="submit" value="Log in" /></p>
</form>
<?php 
} else {
	
}
?>
</div>
</div>
</div>

<?php if ($ms_config['announcement'] != '') { ?>
<div class="announcement">
<?php echo $ms_config['announcement']; ?>
</div>
<?php } ?>

<?php
if ($ms_user['valid']) {
	//get notifications
	$result = $db->query('SELECT type,message,id FROM notifications
	WHERE user=' . $ms_user['id'] . '
	ORDER BY type ASC') or error('Failed to get notifications', __FILE__, __LINE__, $db->error());
	$num_notifs = $db->num_rows($result);
	if ($num_notifs) {
		echo '<div class="announcement">
		<a href="/notifications">You have ' . $num_notifs . ' unread notification';
		if($num_notifs != 1) echo 's';
		echo '</a></div>';
	}
} ?>

<div id="mainContent">