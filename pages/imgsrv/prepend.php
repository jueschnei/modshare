<?php
if (!$ms_user['imgsrv']) {
	echo '<p>You need to be approved to use this service. You may contact us if you want to be approved.</p>';
	$continue = false;
	return;
}