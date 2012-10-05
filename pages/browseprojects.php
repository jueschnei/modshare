<h2>Projects on Mod Share</h2>
<?php
$page_title = 'Projects - Mod Share';
if (!file_exists(SRV_ROOT . '/cache/cache_projects.php')) {
	$result = $db->query('SELECT p.id,p.title,p.thumbnail,u.username,u.permission FROM projects AS p
	LEFT JOIN users AS u
	ON u.id=p.uploaded_by
	WHERE p.status=\'normal\'
	ORDER BY p.time DESC') or error('Failed to get projects', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result)) {
		$out = '<table border="0">
		<tr>';
		$count = 0;
		while ($cur_project = $db->fetch_assoc($result)) {
			if ($count % 5 == 0 && $count > 0) {
				$out .= '</tr><tr>';
			}
			$count++;
			$out .= '
						<td style="text-align:center; width: 150px;">
							<a href="/projects/' . clearHTML(rawurlencode($cur_project['username'])) . '/' . $cur_project['id'] . '">
								<img src="' . dataURI($cur_project['thumbnail']) . '" width="100px" height="100px" alt="Project icon" /><br />
								' . clearHTML($cur_project['title']) . '</a><br />
								By ' . parse_username($cur_project) . '
						</td>
			';
		}
		$out .= '</tr>
	</table>';
		file_put_contents(SRV_ROOT . '/cache/cache_projects.php', $out);
	}
}
include SRV_ROOT . '/cache/cache_projects.php';
?>