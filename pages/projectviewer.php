<?php
//fetch info about the project
$result = $db->query('SELECT p.title,p.description,p.status,p.uploaded_by,p.ip_addr,p.modification,p.time,p.filename,p.views,p.downloads,u.username AS author,u.username,u.permission FROM projects AS p
LEFT JOIN users AS u
ON u.id=p.uploaded_by
WHERE p.id=' . intval($dirs[3])) or error('Failed to get project info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result)) {
	ob_end_clean();
	header('HTTP/1.1 404 Not found');
	include SRV_ROOT . '/errorpages/404.php';
	die;
}
$project_info = $db->fetch_assoc($result);
$page_title = $project_info['title'] . ' - Mod Share';
if ($project_info['status'] == 'deleted') {
	echo 'This project has been deleted by its author.';
	return;
}

//update project file?
if ($ms_user['id'] == $project_info['uploaded_by'] && $_FILES['newfile']['error'] === 0 /*this triple equals is very important - it means the error must equal zero, not null or false */) {
	$ext = explode('.', $_FILES['newfile']['name']);
	$ext = end($ext);
	if ($ext == $modlist[$project_info['modification']]['extension']) {
		unlink(SRV_ROOT . '/data/projects/' . intval($dirs[3]));
		move_uploaded_file($_FILES['newfile']['tmp_name'], SRV_ROOT . '/data/projects/' . intval($dirs[3]));
		$db->query('UPDATE projects
		SET filename=\'' . $db->escape($_FILES['newfile']['name']) . '\'
		WHERE id=' . intval($dirs[3])) or error('Failed to update filename entry in database', __FILE__, __LINE__, $db->error());
	} else {
		echo '<h3>Error</h3>
<p>Invalid file extension</p>';
		return;
	}
}

//update project file?
if ($ms_user['id'] == $project_info['uploaded_by'] && $_FILES['newthumb']['error'] === 0 /*this triple equals is very important - it means the error must equal zero, not null or false */) {
	$fc = file_get_contents($_FILES['newthumb']['tmp_name']);
	$db->query('UPDATE projects
	SET thumbnail=\'' . $db->escape($fc) . '\'
	WHERE id=' . intval($dirs[3])) or error('Failed to update thumbnail', __FILE__, __LINE__, $db->error());
}

$lovequery = $db->query('SELECT user FROM loves WHERE project = ' . intval($dirs[3])) or error('Failed to get project info', __FILE__, __LINE__, $db->error());
$project_info['loves'] = $db->num_rows($lovequery);
$userlovesproject = false;
while($currlove = $db->fetch_assoc($lovequery)) {
	if($currlove['user'] == $ms_user['id']) {
		$userlovesproject = true;
		break;
	}
}

//download project
if ($dirs[4] == 'download') {
	ob_end_clean();
	header('Content-type: application/x-scratch-project');
	header('Content-disposition: attachment; filename=' . $project_info['filename']);
	echo file_get_contents(SRV_ROOT . '/data/projects/' . intval($dirs[3]));
	$db->query('UPDATE projects SET downloads=downloads+1
	WHERE id=' . intval($dirs[3]));
	die;
}
//love project
if ($dirs[4] == 'love') {
	ob_end_clean();
	$result = $db->query('SELECT 1 FROM loves
	WHERE user=' . $ms_user['id'] . '
	AND project=' . intval($dirs[3])) or error('Failed to check if project is loved already', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$db->query('DELETE FROM loves
		WHERE user=' . $ms_user['id'] . '
		AND project=' . intval($dirs[3])) or error('Failed to remove from loves', __FILE__, __LINE__, $db->error());
		echo '<a onclick="loveProject(this.parentNode);"><img src="/img/love.png" alt="loves" title="love" height="32" width="32" style="cursor: pointer;" /></a><br />' . ($project_info['loves'] - 1);
	} else {
		$db->query('INSERT INTO loves(user,project)
		VALUES(' . $ms_user['id'] . ',' . intval($dirs[3]) . ')') or error('INSERT INTO loves(user,project)
		VALUES(' . $ms_user['id'] . ',' . intval($dirs[3]) . ')', __FILE__, __LINE__, $db->error());
		echo '<a onclick="loveProject(this.parentNode);"><img src="/img/loveyes.png" alt="loves" title="unlove" height="32" width="32" style="cursor: pointer;" /></a><br />' . ($project_info['loves'] + 1);
	}
	die;
}
//feature project
if ($dirs[4] == 'feature') {
	ob_end_clean();
	$db->query('UPDATE projects
	SET featured=' . time() . '
	WHERE id=' . intval($dirs[3])) or error('Failed to feature project', __FILE__, __LINE__, $db->error());
	header('Location: /projects/' . $dirs[2] . '/' . $dirs[3]);
	die;
}
//all of this AJAX fun
if (isset($_POST['reason']) && $ms_user['valid']) {
	//flag project
	$db->query('INSERT INTO flags(project_id,flagged_by,time_flagged,reason)
	VALUES(' . intval($dirs[3]) . ',' . $ms_user['id'] . ',' . time() . ',\'' . $db->escape($_POST['reason']) . '\')') or error('Failed to flag project', __FILE__, __LINE__, $db->error()); //flag project
	header('Location: /'); die;
}

if ($dirs[4] == 'delete' && $ms_user['id'] == $project_info['uploaded_by']) {
	//delete the project
	$db->query('UPDATE projects SET status=\'deleted\'
	WHERE id=' . intval($dirs[3])) or error('Failed to delete project', __FILE__, __LINE__, $db->error());
	header('Location: /users/' . $ms_user['username']);
	die;
}
if (isset($_POST['deletecomment']) && ($ms_user['is_mod'] || $ms_user['id'] == $project_info['uploaded_by'])) {
	//delete comment
	$db->query('DELETE FROM comments
	WHERE project=' . intval($dirs[3]) . '
	AND (id=' . intval($_POST['deletecomment']) . ' OR parent=' . intval($_POST['deletecomment']) . ')') or error('Failed to delete comment', __FILE__, __LINE__, $db->error());
	die;
}
if (isset($_POST['flagcomment']) && $ms_user['valid']) {
	//flag comment
	$result = $db->query('SELECT content FROM comments
	WHERE id=' . intval($_POST['flagcomment'])) or error('Failed to get comment info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result)) {
		header('HTTP/1.1 400 Bad request'); die;
	}
	$comment_info = $db->fetch_assoc($result);
	$db->query('INSERT INTO flags(project_id,flagged_by,reason,time_flagged)
	VALUES(' . intval($dirs[3]) . ',' . $ms_user['id'] . ',\'' . $db->escape('The comment "' . $comment_info['content'] . '"') . '\',' . time() . ')') or error('Failed to flag comment', __FILE__, __LINE__, $db->error());
	die;
}
if (isset($_POST['showcomments'])) {
	//show comments (for AJAX)
	ob_end_clean();
	comments();
	die;
}
if (isset($_POST['favoriteproject'])) {
	//favorite project
	ob_end_clean();
	$result = $db->query('SELECT 1 FROM favorites
	WHERE user_id=' . $ms_user['id'] . '
	AND project_id=' . intval($dirs[3])) or error('Failed to check if project is favorited already', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$db->query('DELETE FROM favorites
		WHERE user_id=' . $ms_user['id'] . '
		AND project_id=' . intval($dirs[3])) or error('Failed to remove from favorites', __FILE__, __LINE__, $db->error());
		echo '<img src="/img/fav.png" alt="favourite" title="favourite" height="48" width="48" />';
	} else {
		$db->query('INSERT INTO favorites(user_id,project_id,time)
		VALUES(' . $ms_user['id'] . ',' . intval($dirs[3]) . ',' . time() . ')') or error('Failed to mark as favorite', __FILE__, __LINE__, $db->error());
		echo '<img src="/img/favyes.png" alt="favourite" title="favourite" height="48" width="48" />';
	}
	die;
}
if (isset($_POST['settitle'])) {
	//set project title
	$db->query('UPDATE projects
	SET title=\'' . $db->escape($_POST['settitle']) . '\'
	WHERE id=' . intval($dirs[3]) . '
	AND uploaded_by=' . $ms_user['id']) or error('Failed to update title', __FILE__, __LINE__, $db->error());
	ob_end_clean();
	echo clearHTML($_POST['settitle']); die;
}
if (isset($_POST['setdesc'])) {
	//set project description
	$db->query('UPDATE projects
	SET description=\'' . $db->escape($_POST['setdesc']) . '\'
	WHERE id=' . intval($dirs[3]) . '
	AND uploaded_by=' . $ms_user['id']) or error('Failed to update description', __FILE__, __LINE__, $db->error());
	ob_end_clean();
	echo clearHTML($_POST['setdesc'], true); die;
}
if (isset($_POST['origid'])) {
	//reply to comment
	ob_end_clean();
	$db->query('INSERT INTO comments(project,posted,author,content,parent,ip_addr)
	VALUES(' . intval($dirs[3]) . ',' . time() . ',' . $ms_user['id'] . ',\'' . $db->escape($_POST['comment']) . '\',' . intval($_POST['origid']) . ',\'' . $_SERVER['REMOTE_ADDR'] . '\')') or error('Failed to submit comment', __FILE__, __LINE__, $db->error());
	$result = $db->query('SELECT author FROM comments
	WHERE id=' . intval($_POST['origid'])) or error('Failed to get original author', __FILE__, __LINE__, $db->error());
	$originfo = $db->fetch_assoc($result);
	$db->query('INSERT INTO notifications(user,type,message)
	VALUES(' . $originfo['author'] . ',0,\'' . $db->escape('Your comment on the project <a href="/projects/' . $project_info['author'] . '/' . intval($dirs[3]) . '">' . clearHTML($project_info['title']) . '</a> has been replied to by ' . parse_username($ms_user)) . '\')') or error('Failed to create notification', __FILE__, __LINE__, $db->error());
	die;
}
if (isset($_POST['comment'])) {
	//submit comment
	$db->query('INSERT INTO comments(project,posted,author,content,ip_addr)
	VALUES(' . intval($dirs[3]) . ',' . time() . ',' . $ms_user['id'] . ',\'' . $db->escape($_POST['comment']) . '\',\'' . $_SERVER['REMOTE_ADDR'] . '\')') or error('Failed to submit comment', __FILE__, __LINE__, $db->error());
	//notify project owner
	$db->query('INSERT INTO notifications(user,type,message)
	VALUES(' . $project_info['uploaded_by'] . ',0,\'' . $db->escape('Your project <a href="/projects/' . $project_info['author'] . '/' . intval($dirs[3]) . '">' . clearHTML($project_info['title']) . '</a> has received a new comment by ' . parse_username($ms_user)) . '\')') or error('Failed to create notification', __FILE__, __LINE__, $db->error());
	if (!isset($_POST['noajax'])) {
		die;
	}
}
function comments() {
	//display the comments, this one is a doozy
	global $db, $dirs, $project_info, $ms_user;
	$result = $db->query('SELECT c.id,c.parent,c.content,c.ip_addr,u.username,c.posted,u.permission FROM comments AS c
	LEFT JOIN users AS u
	ON u.id=c.author
	WHERE project=' . intval($dirs[3]) . '
	ORDER BY parent ASC,posted DESC') or error('Failed to get comments', __FILE__, __LINE__, $db->error()); //get comments through query
	$comments = array();
	while ($cur_comment = $db->fetch_assoc($result)) {
		if ($cur_comment['parent'] == null) { //put the comments into arrays, based on thier parent comment
			$comments[$cur_comment['id']][0] = array('content' => $cur_comment['content'], 'username' => $cur_comment['username'], 'permission' => $cur_comment['permission'], 'time' => $cur_comment['posted'], 'ip' => $cur_comment['ip_addr']);
		} else {
			$comments[$cur_comment['parent']][$cur_comment['id']] = array('content' => $cur_comment['content'], 'username' => $cur_comment['username'], 'permission' => $cur_comment['permission'], 'time' => $cur_comment['posted'], 'ip' => $cur_comment['ip_addr']);
		}
	}
	//process the comments for output
	foreach ($comments as $key => $val) {
		$timedelta = $ms_user['timezone'] * 3600;
		echo '<hr /><h4>' . parse_username($val[0]) . ' - ' . gmdate('d M Y H:i:s', $val[0]['time'] + $timedelta) . '</h4>';
		echo '<p>' . clearHTML($val[0]['content']) . '</p>';
		if ($ms_user['valid']) {
			echo '<p id="cd' . $key . '"><a style="cursor:pointer" onclick="replyToComment(' . $key . ')">Reply</a> &bull; <a style="cursor:pointer" onclick="flagComment(' . $key . ')">Flag as inappropriate</a>';
			if (($ms_user['id'] == $project_info['uploaded_by'] && $val[0]['permission'] < 2) || $ms_user['is_mod']) {
				echo ' &bull; <a style="cursor:pointer" onclick="deleteComment(' . $key . ')">Delete' . $val[0]['permission'] . '</a>';
			}
			if ($ms_user['is_mod']) {
				echo ' &bull; <a href="/admin/search_ip/' . $val[0]['ip'] . '">' . $val[0]['ip'] . '</a>';
			}
			echo '</p>';
		}
		$out = array(); //for ordering
		foreach ($val as $subkey => $subval) {
			if ($subkey != 0) {
				$outval = '<h5 style="margin-left: 15px; font-size:16px;">' . parse_username($subval) . ' - ' . gmdate('d M Y H:i:s', $subval['time'] + $timedelta) . '</h5><p style="margin-left: 15px">' . clearHTML($subval['content']) . '</p>';
				$outval .= '<p style="margin-left: 15px">';
				if ($ms_user['is_mod'] || ($ms_user['id'] == $project_info['uploaded_by'] && $subval['permission'] < 2)) {
					$outval .= '<a onclick="deleteComment(' . $subkey . ')" style="cursor:pointer">Delete</a>';
				}
				if ($ms_user['is_mod']) {
					$outval .= ' &bull; <a href="/admin/search_ip/' . $subval['ip'] . '">' . $subval['ip'] . '</a>';
				}
				$outval .= '</p>';
				$out[] = $outval;
			}
		}
		//echo output in correct order
		$out = array_reverse($out);
		foreach ($out as $curout) {
			echo $curout;
		}
	}
}
?>
<?php $db->query('UPDATE projects SET views=views+1 WHERE id=' . intval($dirs[3])) or error('Failed to update views', __FILE__, __LINE__, $db->error()); ?>

<h2><span id="titleSpan"<?php if ($ms_user['id'] == $project_info['uploaded_by']) echo ' onclick="changeTitle();" class="editable"'; ?>><?php echo clearHTML($project_info['title']); ?></span><?php if ($ms_user['id'] == $project_info['uploaded_by']) { ?><input type="text" id="newTitle" value="<?php echo clearHTML($project_info['title']); ?>" style="display:none" onkeypress="checkForEnter(event)" /><?php } ?></h2>
<h3>Description</h3>
<p <?php if ($ms_user['id'] == $project_info['uploaded_by']) echo 'onclick="editDesc()" class="editable"'; ?>>
	<span id="descSpan"><?php if ($project_info['description'] == '') echo '<i>The creator didn&apos;t put a description</i>'; else echo clearHTML($project_info['description'], true); ?></span>
	<?php if ($ms_user['id'] == $project_info['uploaded_by']) { ?>
	<span id="descEdit" style="display:none">
		<textarea id="newDesc" rows="6" cols="60"><?php echo clearHTML($project_info['description']); ?></textarea><br />
		<input type="submit" value="Update" onclick="updateDesc()" />
	</span>
	<?php } ?>
</p>
<table width="600" border="0">
  <tr>
    <td>
      <object type="application/x-shockwave-flash" data="/player" width="482" height="401" id="flashplayer" style="visibility: visible; position: relative; margin-left: 4px; z-index: 1000; ">
        <param name="allowScriptAccess" value="sameDomain" />
        <param name="allowFullScreen" value="true" />
        <param name="flashvars" value="project=<?php if ($project_info['status'] == 'blocked') echo '/data/blocked.sb'; else echo '/data/projects/' . intval($dirs[3]); ?>" />
      </object>
    </td>
    <td width="100">
    <div style="width: 96px; height: 90px; background: url(/img/smallbkg.png) no-repeat; text-align: center; padding-top: 6px;">
    	<p><img src="/img/views.png" width="32" height="32" alt="views" title="views" /><br /><?php echo ($project_info['views'] + 1) ?></p>
    </div>
    <div style="width: 96px; height: 90px; background: url(/img/smallbkg.png) no-repeat; text-align: center; padding-top: 6px;">
    	<p><?php if($ms_user['valid']) {
				if($userlovesproject) {
					echo '<a onclick="loveProject(this.parentNode);"><img src="/img/loveyes.png" width="32" height="32" alt="loves" title="unlove" style="cursor: pointer;" /></a>';
					echo '<br />' . $project_info['loves'];
				} else {
					echo '<a onclick="loveProject(this.parentNode);"><img src="/img/love.png" width="32" height="32" alt="loves" title="love" style="cursor: pointer;" /></a>';
					echo '<br />' . $project_info['loves'];
				}
			} else {
				echo '<img src="/img/love.png" width="32" height="32" alt="loves" title="loves" />';
			} ?></p>
    </div>
    <div style="width: 96px; height: 90px; background: url(/img/smallbkg.png) no-repeat; text-align: center; padding-top: 6px;">
    	<p><?php if ($project_info['blocked'])
				echo '<img src="/img/downloadno.png" width="32" height="32" alt="no download" title="download disabled" />';
			else
				echo '<a href="/projects/' . clearHTML($dirs[2]) . '/' . intval($dirs[3]) . '/download"><img src="/img/download.png" width="32" height="32" alt="download" title="download" /></a><br />' . intval($project_info['downloads']); ?></p>
    </div>
    </td>
  </tr>
</table>
<table width="500" border="0" style="height: 48px;">
  <tr valign="middle">
    <th scope="col" style="background: url(/img/leftbar.png) no-repeat;"><?php echo parse_username($project_info); ?></th>
    <th scope="col" style="background: url(/img/middlebar.png) repeat-x;"><?php echo format_date($project_info['time']); ?></th>
    <th scope="col" style="background: url(/img/middlebar.png) repeat-x;"><?php echo getMod($project_info['modification']); ?></th>
    <th scope="col" style="background: url(/img/rightbar.png) right top no-repeat;"><a onclick="favoriteProject(this);" style="cursor:pointer"><?php
	$result = $db->query('SELECT 1 FROM favorites
	WHERE user_id=' . ($ms_user['valid'] ? $ms_user['id'] : '-1') . '
	AND project_id=' . intval($dirs[3])) or error('Failed to check if project is favorited already', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		echo '<img src="/img/favyes.png" alt="favourite" title="favourite" height="48" width="48" />';
	} else {
		echo '<img src="/img/fav.png" alt="favourite" title="favourite" height="48" width="48" />';
	}
?></a></th>
  </tr>
</table>

<?php
if ($ms_user['id'] == $project_info['uploaded_by'] || $ms_user['is_admin']) {
	echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '/delete">Delete this project</a></p>';
}
if ($ms_user['id'] == $project_info['uploaded_by']) {
	echo '<form action"/projects/' . $dirs[2] . '/' . $dirs[3] . '" method="post" enctype="multipart/form-data">
	<p>New file: <input type="file" name="newfile" /> <input type="submit" value="Update" /></p>
</form>
<form action"/projects/' . $dirs[2] . '/' . $dirs[3] . '" method="post" enctype="multipart/form-data">
	<p>New thumbnail: <input type="file" name="newthumb" /> <input type="submit" value="Update" /></p>
</form>';
}
?>

<?php if ($ms_user['valid']) { ?>
<p><a onclick="document.getElementById('flagform').style.display = 'block';" style="cursor:pointer"><img src="/img/flag.png" width="22" height="22" alt="flag" /> Flag as inappropriate</a></p>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" style="display:none" id="flagform">
	<p>Please enter a short reason why you are flagging this project.<br />
	<textarea rows="5" cols="60" name="reason"></textarea><br />
	<input type="submit" value="Flag project" />
	</p>
</form>
<?php
}
if ($ms_user['is_mod']) {
	echo '<p>Uploaded from IP <a href="/admin/search_ip/' . $project_info['ip_addr'] . '">' . $project_info['ip_addr'] . '</a> &bull; <a href="/admin/block_project/' . intval($dirs[3]) . '">Block project</a>';
	if ($ms_user['is_admin']) {
		echo ' &bull; <a href="/projects/' . clearHTML($dirs[2]) . '/' . intval($dirs[3]) . '/feature">Feature project</a>';
	}
	echo '</p>';
}
?>

<h3>Comments</h3>
<?php if ($ms_user['valid']) { ?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	<textarea id="newComment" name="comment" rows="4" cols="60"></textarea><br />
	<input type="hidden" name="noajax" value="yes" />
	<input type="submit" onclick="sendComment(); return false" width="100px" />
</form>
<?php } ?>
<div id="comments">
<?php
comments();
?>
</div>

<script type="text/javascript">
//<![CDATA[
var flashapp = document.getElementById('flashplayer');

function JSsetPresentationMode(fillScreen) {
	if (fillScreen) {
		var r = flashapp.getBoundingClientRect();
		flashapp.style.left = -r.left + 'px';
		flashapp.style.top = -r.top + 'px';
		flashapp.style.position = 'relative';
		var h = window.innerHeight;
		if (typeof(w) != 'number') { // If IE:
			w = document.documentElement.clientWidth;
			h = document.documentElement.clientHeight;
		}
		setPlayerSize(w, h - 10);
	} else {
		flashapp.style.position = "inherit";
		setPlayerSize(482, 387);
		flashapp.style.left = flashapp.style.top = '0px';
	}
}

function setPlayerSize(w, h) {
	var isFirefox = navigator.userAgent.toLowerCase().indexOf("firefox") > 0;
	if (isFirefox) w += 1;
	if (navigator.appName == 'Microsoft Internet Explorer') {
		flashapp.style.width = w;
		flashapp.style.height = h;
	} else {
		flashapp.width = w;
		flashapp.height = h;
	}
}

function checkForEnter(e) {
	if (e.keyCode == 13) {
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		} else {
			 req = new ActiveXObject("Microsoft.XMLHTTP");
		}
		req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		req.send("settitle=" + encodeURIComponent(document.getElementById('newTitle').value));
		
		req.onreadystatechange = function() {
			if (req.readyState==4 && req.status==200) {
				document.getElementById('titleSpan').style.display = 'inline';
				document.getElementById('titleSpan').innerHTML = req.responseText;
				document.getElementById('newTitle').style.display = 'none';
			} else {
				//failure
			}
		 }
	}
}
function updateDesc() {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send("setdesc=" + encodeURIComponent(document.getElementById('newDesc').value));
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			document.getElementById('descSpan').style.display = 'inline';
			document.getElementById('descEdit').innerHTML = req.responseText;
			document.getElementById('descSpan').style.display = 'none';
		} else {
			//failure
		}
	 }
}
function favoriteProject(sender) {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send('favoriteproject=1');
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			sender.innerHTML = req.responseText;
		} else {
			//failure
		}
	 }
}
function loveProject(sender) {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("GET", '<?php echo $_SERVER['REQUEST_URI']; ?>/love', true);
	req.send();
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			sender.innerHTML = req.responseText;
		} else {
			//failure
		}
	 }
}
function sendComment() {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send("comment=" + encodeURIComponent(document.getElementById('newComment').value));
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			getComments();
		} else {
			//failure
		}
	 }
}
function submitReply(id) {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send('origid=' + id + '&comment=' + encodeURIComponent(document.getElementById('replyto' + id).value));
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			getComments();
		} else {
			//failure
		}
	 }
}
function deleteComment(id) {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send('deletecomment=' + id);
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			getComments();
		} else {
			//failure
		}
	 }
}
function flagComment(id) {
	if (confirm('Are you sure?')) {
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		} else {
			 req = new ActiveXObject("Microsoft.XMLHTTP");
		}
		req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
		req.send('flagcomment=' + id);
		
		req.onreadystatechange = function() {
			if (req.readyState==4 && req.status==200) {
				alert('Comment flagged. Thank you for helping us keep the site clean');
			} else {
				//failure
			}
		 }
		}
}
function replyToComment(id) {
	document.getElementById('cd' + id).innerHTML = '<textarea id="replyto' + id + '" rows="4" cols="40"></textarea><br /><input type="submit" value="Submit" onclick="submitReply(' + id + ')" />';
}
function getComments() {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.open("POST", '<?php echo $_SERVER['REQUEST_URI']; ?>', true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send('showcomments=1');
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			document.getElementById('comments').innerHTML = req.responseText;
		} else {
			//failure
		}
	 }
}

function editDesc() {
	document.getElementById('descSpan').style.display = 'none';
	document.getElementById('descEdit').style.display = 'inline';
}

function changeTitle() {
	document.getElementById('titleSpan').style.display = 'none';
	document.getElementById('newTitle').style.display = 'inline';
}
//]]>
</script>