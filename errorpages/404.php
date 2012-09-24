<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Page Not Found</title>
<style type="text/css">
body {font-family: arial; text-align:center}
</style>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
<h1 style="font-style: italic; color: #E00;">Oops...</h1>
<p>The document "<?php echo $_SERVER["REQUEST_URI"]; ?>" was not found on our server.</p>
<p>You may want to try going back to <a href="javascript:history.go(-1);" style="color: #C00; text-decoration: none;">your last visited webpage</a> or <a href="/" style="color: #C00; text-decoration: none;">the homepage</a>!</p>
<p><?php
$r = $_SERVER["REQUEST_URI"];
if(stristr($r, 'stuff') || stristr($r, 'my')) {
	echo 'Were you looking for the <a style="color: #C00; text-decoration: none;" href="/home">my stuff</a> page?';
} elseif(stristr($r, 'forum')) {
	echo 'Were you looking for the <a style="color: #C00; text-decoration: none;" href="/forums">forums</a>?';
} elseif(stristr($r, 'project')) {
	echo 'Were you looking for the <a style="color: #C00; text-decoration: none;" href="/browse">project list</a>?';
} elseif(stristr($r, 'support')) {
	echo 'Were you looking for the <a style="color: #C00; text-decoration: none;" href="/help">help desk</a>?';
} elseif(stristr($r, 'up')) {
	echo 'Were you looking for the <a style="color: #C00; text-decoration: none;" href="/upload">project upload</a> page?';
} elseif(stristr($r, 'user')) {
	echo 'Were you looking for the <a style="color: #C00; text-decoration: none;" href="/users">user list</a> page?';
}
?></p>
</body>
</html>