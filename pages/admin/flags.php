<?php
$page_title = 'Project flags - Mod Share';
if (isset($_POST['markread'])) {
	foreach ($_POST['markread'] as $key => $val) {
		$db->query('UPDATE flags
		SET zapped=' . time() . '
		WHERE id=' . intval($key)) or error('Failed to mark flag as read', __FILE__, __LINE__, $db->error());
	}
}
if (isset($_POST['badflag'])) {
	foreach ($_POST['badflag'] as $key => $val) {
		$result = $db->query('SELECT flagged_by FROM flags
		WHERE id=' . intval($key)) or error('Failed to get flagger', __FILE__, __LINE__, $db->error());
		list($flagger) = $db->fetch_row($result);
		$db->query('INSERT INTO notifications(user,type,message)
		VALUES(' . $flagger . ',1,\'Please do not flag projects or comments unnecessarily.\')') or error('Failed to send notification', __FILE__, __LINE__, $db->error());
		$db->query('INSERT INTO adminhistory(to_user,from_user,time,action)
		VALUES(' . $flagger . ',' . $ms_user['id'] . ',' . time() . ',\'Flagged an unnecessary project.\')') or error('Failed to send notification', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE flags
		SET zapped=' . time() . '
		WHERE id=' . intval($key)) or error('Failed to mark flag as bad flag', __FILE__, __LINE__, $db->error());
	}
}
?>
<h2>Project flags</h2>
<form action="/admin/flags" method="post" enctype="multipart/form-data">
	<table border="1px">
		<tr>
			<th>Data flagged</th>
			<th>Reason</th>
			<th>User in question</th>
			<th>Flagger</th>
			<th>Actions</th>
		</tr>
<?php
$result = $db->query('SELECT f.project_id,f.comment_id,f.reason,f.id,
	p.title AS project_title,
	pa.username AS project_author,
	flagger.username AS flagger_username,
	c.content AS comment_text,
	ca.username AS comment_author,
	cp.title AS comment_project_title,cp.id AS comment_project_id,
	cpu.username AS comment_project_author
	FROM flags AS f
LEFT JOIN projects AS p
ON p.id=f.project_id
LEFT JOIN users AS pa
ON pa.id=p.uploaded_by
LEFT JOIN users AS flagger
ON flagger.id=f.flagged_by
LEFT JOIN comments AS c
ON c.id=f.comment_id
LEFT JOIN users AS ca
ON ca.id=c.author
LEFT JOIN projects AS cp
ON cp.id=c.project
LEFT JOIN users AS cpu
ON cpu.id=cp.uploaded_by
WHERE zapped IS NULL') or error('Failed to check flags', __FILE__, __LINE__, $db->error());
while ($cur_flag = $db->fetch_assoc($result)) {
	echo '<tr>
	<td>';
	if ($cur_flag['project_id']) {
		echo 'Project:<br /><a href="/projects/' . $cur_flag['project_author'] . '/' . $cur_flag['project_id'] . '">' . clearHTML($cur_flag['project_title']) . '</a><br />By: <a href="/users/' . $cur_flag['project_author'] . '">' . $cur_flag['project_author'] . '</a>';
	} elseif ($cur_flag['comment_id']) {
		echo 'Comment:<br /><pre>' . clearHTML($cur_flag['comment_text']) . '</pre><br />On project: <a href="/projects/' . $cur_flag['comment_project_author'] . '/' . $cur_flag['comment_project_id'] . '">' . clearHTML($cur_flag['comment_project_title']) . '</a> by <a href="/users/' . $cur_flag['comment_project_author'] . '">' . $cur_flag['comment_project_author'] . '</a>';
	}
		
	echo '</td>
	<td>
		' . clearHTML($cur_flag['reason']) . '
	</td>
	<td>';
	if ($cur_flag['project_id']) {
		echo '<a href="/users/' . $cur_flag['project_author'] . '">' . $cur_flag['project_author'] . '</a>';
	} elseif ($cur_flag['comment_id']) {
		echo '<a href="/users/' . $cur_flag['comment_author'] . '">' . $cur_flag['comment_author'] . '</a>';
	}
	echo '</td>
	<td>
		<a href="/users/' . $cur_flag['flagger_username'] . '">' . $cur_flag['flagger_username'] . '</a>
	</td>
	<td>
		<input type="submit" name="markread[' . $cur_flag['id'] . ']" value="Mark as read" style="width: 120px" /><br />
		<input type="submit" name="badflag[' . $cur_flag['id'] . ']" value="Bad flag" style="width: 120px" />
	</td>
</tr>';
}
?>
	</table>
</form>