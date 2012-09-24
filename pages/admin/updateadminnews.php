<?php
	$res = stripslashes($_POST['news']);
	if(strstr($_SERVER['REQUEST_URI'], 'clearnews')) {
		set_config('adminnews', '');
	} elseif(strstr($_SERVER['REQUEST_URI'], 'clearfiles')) {
		set_config('downloadfiles', '');
	} else {
		if(strpos($res, '$') === 0) {
			$res = substr($res, 1);
			$res = '<span style="font-weight: bold;" id="news' . time() . '">' . substr($ms_user['username'], 0, 1) . ':</span> ' . $res;
			set_config('adminnews', $ms_config['adminnews'] . '<p>' . $res . '</p>');
		} else {
			set_config('downloadfiles', $ms_config['downloadfiles'] . $res . "\n");
		}
	}
	header('Location: ' . $_SERVER['HTTP_REFERER']);
?>