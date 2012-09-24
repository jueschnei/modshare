<?php
header("Content-Type: image/png");
$im = @imagecreate(85, 20)
    or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0xDE, 0xDF, 0xDF);
$text_color = imagecolorallocate($im, 0, 0, 0);

$opentime = mktime(12, 00, 00, 9, 23, 2012);
$diff = ceil($opentime - $_SERVER['REQUEST_TIME']);
$diff /= 60;
$diff = ceil($diff);
$text = $diff . ' minute';
if ($diff != 1) {
	$text .= 's';
}

imagestring($im, 3, 2, 9,  $text, $text_color);
imagepng($im);
imagedestroy($im);
?>