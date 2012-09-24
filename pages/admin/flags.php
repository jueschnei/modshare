<?php
$page_title = 'Project flags - Mod Share';
if (isset($_POST['markread'])) {
	foreach ($_POST['markread'] as $key => $val) {
		$db->query('UPDATE flags
		SET zapped=' . time() . '
		WHERE id=' . intval($key)) or error('Failed to mark ban as read', __FILE__, __LINE__, $db->error());
	}
}
?>
<h2>Project flags</h2>
<form action="/admin/flags" method="post" enctype="multipart/form-data">
<?php
$result = $db->query('SELECT f.id,f.flagged_by,f.reason,f.time_flagged,f.project_id,p.title AS project_title,u.username AS flagged_by,pa.username AS project_owner
FROM flags AS f
LEFT JOIN projects AS p
ON p.id=f.project_id
LEFT JOIN users AS u
ON u.id=f.flagged_by
LEFT JOIN users AS pa
ON pa.id=p.uploaded_by
WHERE zapped IS NULL') or error('Failed to check flags', __FILE__, __LINE__, $db->error());
while ($cur_flag = $db->fetch_assoc($result)) {
	echo '<h3>' . clearHTML($cur_flag['flagged_by']) . ' flagged the project <a href="/projects/' . clearHTML($cur_flag['project_owner']) . '/' . $cur_flag['project_id'] . '">' . clearHTML($cur_flag['project_title']) . '</a> by ' . clearHTML($cur_flag['project_owner']) . '</h3>
	<p>Reason: ' . clearHTML($cur_flag['reason']) . '</p>
	<p><input type="submit" name="markread[ ' . $cur_flag['id'] . ']" value="Mark as read" /></p>';
}
?>
</form>