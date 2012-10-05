<?php
$content_type = 'image';
$img = basename(rawurldecode($dirs[4]));
if (file_exists(SRV_ROOT . '/data/imgsrv/' . $dirs[3] . '/' . $img)) {
	echo file_get_contents(SRV_ROOT . '/data/imgsrv/' . $dirs[3] . '/' . $img);
} else {
	header('HTTP/1.1 404 Not found');
	imageerror('Image does not exist');
}

function imageerror($text) {
	$img = imagecreate(256, 30);
	$background = imagecolorallocate($img, 223, 223, 223);
	$text_colour = imagecolorallocate($img, 200, 20, 20);
	imagesetthickness($img, 3);
	$font = realpath(SRV_ROOT . '/includes/static/font.ttf');
	imagettftext($img, 13, 0, 50, 20, $text_colour, $font, $text);
	imagepng($img);
}