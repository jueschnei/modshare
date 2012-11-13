<?php

if ($dirs[2] == 'getallusers') {
	// INPUT: none
	// OUTPUT: usernames separated by colons
	$result = $db->query("SELECT username FROM users WHERE status != 'disabledbyadmin'") or die('ERROR');

	if ($db->num_rows($result)) {
		$res = '';
		while ($var_info = $db->fetch_assoc($result)) {
			$res .= ':' . $var_info['username'];
		}
		$res = substr($res, 1);
		echo $res;
	} else {
		echo 'ERROR';
	}
	
} elseif ($dirs[2] == 'getallprojects') {
	// INPUT: none
	// OUTPUT: project IDs separated by colons
	$result = $db->query("SELECT id FROM projects WHERE status = 'normal'") or die('ERROR');

	if ($db->num_rows($result)) {
		$res = '';
		while ($var_info = $db->fetch_assoc($result)) {
			$res .= ':' . $var_info['id'];
		}
		$res = substr($res, 1);
		echo $res;
	} else {
		echo 'ERROR';
	}
	
} elseif ($dirs[2] == 'featured') {
	// INPUT: none
	// OUTPUT: title:user:id
	$result = $db->query('SELECT p.id,p.title,u.username FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by
	WHERE p.status=\'normal\'
	AND p.featured IS NOT NULL
	ORDER BY p.featured DESC
	LIMIT 0,3') or die('ERROR');
	if ($db->num_rows($result)) {
		$res = '';
		while ($cur_project = $db->fetch_assoc($result)) {
			$res .= ':' . str_replace(':', ';', $cur_project['title']) . ' by ' . $cur_project['username'] . ':' . $cur_project['id'];
		}
		echo substr($res, 1);
	}

	if ($db->num_rows($result)) {
		$res = '';
		while ($var_info = $db->fetch_assoc($result)) {
			$res .= ':' . $var_info['id'];
		}
		$res = substr($res, 1);
		echo $res;
	} else {
		echo 'ERROR';
	}
	
} elseif ($dirs[2] == 'latest') {
	// INPUT: none
	// OUTPUT: title:user:id
	$result = $db->query('SELECT p.id,p.title,u.username FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by
	WHERE p.status=\'normal\'
	AND p.featured IS NOT NULL
	ORDER BY p.time DESC
	LIMIT 0,3') or die('ERROR');
	if ($db->num_rows($result)) {
		$res = '';
		while ($cur_project = $db->fetch_assoc($result)) {
			$res .= ':' . str_replace(':', ';', $cur_project['title']) . ' by ' . $cur_project['username'] . ':' . $cur_project['id'];
		}
		echo substr($res, 1);
	}

	if ($db->num_rows($result)) {
		$res = '';
		while ($var_info = $db->fetch_assoc($result)) {
			$res .= ':' . $var_info['id'];
		}
		$res = substr($res, 1);
		echo $res;
	} else {
		echo 'ERROR';
	}

} elseif ($dirs[2] == 'getuserinfo') {
	// INPUT: user name or id
	// OUTPUT: id:username:status:num_projects:num_friends
	$qid = $dirs[3];
	if(is_numeric($qid)) {
		$result = $db->query("SELECT id, username, status FROM users WHERE id = $qid") or die('ERROR');
	} else {
		$result = $db->query("SELECT id, username, status FROM users WHERE username = '$qid'") or die('ERROR');
	}

	if ($db->num_rows($result)) {
		$var_info = $db->fetch_assoc($result);
		// get projects and friends
		$qid = $var_info['id'];
		$result2 = $db->query("SELECT id FROM projects WHERE uploaded_by = $qid") or die('ERROR');
		$result3 = $db->query("SELECT id FROM friends WHERE friender = $qid") or die('ERROR');
		// render
		$res = $qid . ':' . $var_info['username'] . ':' . $var_info['status'] . ':' . $db->num_rows($result2) . ':' . $db->num_rows($result2);
		echo $res;
	} else {
		echo 'ERROR';
	}

} elseif ($dirs[2] == 'getprojectsbyuser') {
	// INPUT: user name or id
	// OUTPUT: id's of projects separated by colons
	$qid = $dirs[3];
	if(is_numeric($qid)) {
		$result = $db->query("SELECT id FROM projects WHERE uploaded_by = $qid AND status = 'normal'") or die('ERROR');
	} else {
		$result2 = $db->query("SELECT id FROM users WHERE username = '$qid'") or die('ERROR');
		$unm = $db->fetch_assoc($result2);
		$unm = $unm['id'];
		$result = $db->query("SELECT id FROM projects WHERE uploaded_by = '$unm' AND status = 'normal'") or die('ERROR');
	}

	if ($db->num_rows($result)) {
		while ($var_info = $db->fetch_assoc($result)) {
			$res .= ':' . $var_info['id'];
		}
		$res = substr($res, 1);
		echo $res;
	} else {
		echo '';
	}

} elseif ($dirs[2] == 'getprojectinfo') {
	// INPUT: project ID
	// OUTPUT: title:creation_timestamp:uploader:license:mod:description:views:downloads:loves
	// NOTE: title and description are encoded for raw URL, so use rawurldecode()
	$qid = $dirs[3];
	$result = $db->query("SELECT title, time, uploaded_by, license, modification, description, views, downloads FROM projects WHERE id = $qid") or die('ERROR');
	$result2 = $db->query("SELECT id FROM loves WHERE project = $qid") or die('ERROR');

	if ($db->num_rows($result)) {
		$var_info = $db->fetch_assoc($result);
		// render
		$res = rawurlencode($var_info['title']) . ':' .
		$var_info['time'] . ':' .
		$var_info['uploaded_by'] . ':' .
		$var_info['license'] . ':' .
		$var_info['modification'] . ':' .
		rawurlencode($var_info['description']) . ':' .
		$var_info['views'] . ':' .
		$var_info['downloads'] . ':' . 
		$db->num_rows($result2);
		echo $res;
	} else {
		echo 'ERROR';
	}

} elseif ($dirs[2] == 'authenticate') {
	// INPUT: username/password
	// OUTPUT: if allowed, output user_id:status. if failed, output 'false'
	// NOTE: use rawurlencode() to send password
	$unm = $dirs[3];
	$upwd = ms_hash(rawurldecode($dirs[4]));
	$result = $db->query("SELECT id, password_hash, status FROM users WHERE username = '$unm'") or die('ERROR');

	if ($db->num_rows($result)) {
		$var_info = $db->fetch_assoc($result);
		// check password
		if($upwd == $var_info['password_hash']) {
			$result = $db->query('SELECT 1 FROM bans WHERE id=' . $var_info['id']) or die('ERROR');
			echo $var_info['id'] . ':';
			if ($db->num_rows($result)) {
				echo 'banned';
			} else if ($var_info['status'] == 'disabledbyadmin') {
				echo 'disabled';
			} else {
				echo 'unbanned';
			}
		} else {
			echo 'false';			
		}
	} else {
		echo 'false';
	}
	
} elseif ($dirs[2] == 'getallprojectsinfo') {
	$result = $db->query('SELECT id,title FROM projects
	' . ($dirs[3] == 'insanity' ? 'WHERE modification LIKE \'insanity%\'' : '') . '
	ORDER BY time DESC
	LIMIT 3') or error('Failed to get projects', __FILE__, __LINE__, $db->error());
	while ($cur_project = $db->fetch_assoc($result)) {
		$ids[] = $cur_project['id'] . ':' . str_replace('|', '_', $cur_project['title'], str_replace(':', '_', str_replace("\n", '', $cur_project['title'])));
	}
	echo implode($ids, "|");
} elseif($dirs[2] == '') {
	// Show help page
	ob_start();
	include SRV_ROOT . '/includes/header.php';
	echo '<p>This page serves as a quick reference to the API that allows other applications to connect with Mod Share 4.</p>';
	echo '<h4>Get all users</h4>';
	echo '<p>URL: /api/getallusers<br />
	Parameters: none<br />
	Returns: colon-separated list of users<br />
	Example URL: modshare.tk/api/getallusers<br />
	Example response: jvvg:LS97:jacob</p>';
	
	echo '<h4>Get all projects</h4>';
	echo '<p>URL: /api/getallprojects<br />
	Parameters: none<br />
	Returns: colon-separated list of project IDs<br />
	Example URL: modshare.tk/api/getallprojects<br />
	Example response: 8:10:27:49</p>';
	
	echo '<h4>Get user information</h4>';
	echo '<p>URL: /api/getuserinfo<br />
	Parameters: user ID or username<br />
	Returns: id:username:status:num_projects:num_friends<br />
	Example URL: modshare.tk/api/getuserinfo/LS97<br />
	Example response: 4:LS97:normal:3:8</p>';
	
	echo '<h4>Get project information</h4>';
	echo '<p>URL: /api/getprojectinfo<br />
	Parameters: project ID<br />
	Returns: title:creation_timestamp:uploader_id:license:mod:description:views:downloads:loves<br />
	Example URL: modshare.tk/api/getprojectinfo/6<br />
	Example response: Hyperactive%20cat:1347730658:4:ms:insanity11::75:7:1<br />
	NOTE: The title and description are encoded as URL codes, so use PHP function rawurldecode() on those values.</p>';
	
	echo '<h4>Get all projects by user</h4>';
	echo '<p>URL: /api/getprojectsbyuser<br />
	Parameters: user ID or username<br />
	Returns: colon-separated list of project IDs<br />
	Example URL: modshare.tk/api/getprojectsbyuser/2<br />
	Example response: 26:45:55:61:68:82</p>';
	
	echo '<h4>Authenticate a user against their password on Mod Share</h4>';
	echo '<p>URL: /api/authenticate<br />
	Parameters: username/password<br />
	Returns: if success, "user_id:status". if failed, "false"<br />
	Example URL: modshare.tk/api/authenticate/LS97/password123<br />
	Example response: 4:banned</p>';
	
	echo '<p></p>';
	include SRV_ROOT . '/includes/footer.php';
	$data = ob_get_contents();
	$data = str_replace('<$page_title/>', 'Mod Share API', $data);
	ob_end_clean();
	echo $data;

} else {

	echo 'INVALID';

}