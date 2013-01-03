<?php

/**
 * Copyright (C) 2010-2012 Visman (visman@inbox.ru)
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);
define('PLUGIN_VERSION', '1.4.9');
define('PLUGIN_URL', pun_htmlspecialchars(get_base_url(true).'/admin_loader.php?plugin='.$_GET['plugin']));

// If the "Show text" button was clicked
if (isset($_GET['form_sent']))
{
	generate_admin_menu($plugin);
	?>
	<div class="plugin blockform">
		<h2><span>Topic matches</span></h2>
		<div class="box">
			<div class="inbox">
	<?php
	$result = $db->query('SELECT poster,subject,id,trasher,trashed FROM ' . $db->prefix . 'trash_topics
	WHERE poster=\'' . $db->escape($_GET['username']) . '\' ' . (isset($_GET['adminonly']) ? ' AND trasher<>\'' . $db->escape($_GET['username']) . '\'' : '') . ' ORDER BY trashed DESC') or error('Failed to get topic matches', __FILE__, __LINE__, $db->error());
	$first = true;
	while ($cur_topic = $db->fetch_assoc($result)) {
		if ($first) {
			$first = false;
			echo '
			<table border="0">
				<tr>
					<th>Subject</th>
					<th>Deleted by</th>
					<th>Deletion time</th>
				</tr>';
		}
		echo '<tr>
			<td><a href="admin_loader.php?plugin=AMP_Trash_bin.php&amp;tid=' . $cur_topic['id'] . '">' . pun_htmlspecialchars($cur_topic['subject']) . '</a></td>
			<td>' . $cur_topic['trasher'] . '</td>
			<td>' . format_time($cur_topic['trashed']) . '</td>
		</tr>';
	}
	if ($first) {
		echo '<p>No topic matches</p>';
	} else {
		echo '</table>';
	}
	
	?>
			</div>
		</div>
		
		<h2 class="block2"><span>Post matches</span></h2>
		<div class="box">
			<div class="inbox">
				<?php
				$result = $db->query('SELECT p.id,p.message,p.trasher,p.trashed,f1.forum_name AS forum1,f2.forum_name AS forum2,t1.subject AS subject1,t2.subject AS subject2 FROM ' . $db->prefix . 'trash_posts AS p
				LEFT JOIN ' . $db->prefix . 'topics AS t1 ON t1.id=p.topic_id
				LEFT JOIN ' . $db->prefix . 'forums AS f1 ON f1.id=t1.forum_id
				LEFT JOIN ' . $db->prefix . 'trash_topics AS t2 ON t2.id=p.topic_id
				LEFT JOIN ' . $db->prefix . 'forums AS f2 ON f2.id=t2.forum_id
				WHERE p.poster=\'' . $db->escape($_GET['username']) . '\' AND p.post_alone=1 ' .
				(isset($_GET['adminonly']) ? ' AND p.trasher<>\'' . $db->escape($_GET['username']) . '\'' : '')
				. ' ORDER BY trashed DESC') or error('Failed to check for post matches', __FILE__, __LINE__, $db->error());
				$first = true;
				while ($cur_GET = $db->fetch_assoc($result)) {
					if ($first) {
						$first = false;
						echo '<table border="0">
						<tr>
							<th>Forum</th>
							<th>Topic</th>
							<th>Deleted by</th>
							<th>Deletion time</th>
						</tr>';
					}
					if ($cur_GET['forum1']) {
						$cur_GET['forum'] = $cur_GET['forum1'];
						$cur_GET['subject'] = $cur_GET['subject1'];
					} else {
						$cur_GET['forum'] = $cur_GET['forum2'];
						$cur_GET['subject'] = $cur_GET['subject2'];
					}
					echo '
					<tr>
						<td>' . pun_htmlspecialchars($cur_GET['forum']) . '</td><td><a href="admin_loader.php?plugin=AMP_Trash_bin.php&amp;pid=' . $cur_GET['id'] . '">' . pun_htmlspecialchars($cur_GET['subject']) . '</a></td><td>' . $cur_GET['trasher'] . '</td><td>' . format_time($cur_GET['trashed']) . '</td>
					</tr>';
				}
				if ($first) {
					echo '<p>No post matches</p>';
				} else {
					echo '
					</table>';
				}
				?>
			</div>
		</div>
	</div>
	<?php

}
else
{
	// Display the admin navigation menu
	generate_admin_menu($plugin);

	$cur_index = 1;

?>
	<div class="plugin blockform">
		<h2><span>Search trash bin</span></h2>
		<div class="box">
			<div class="inbox">
				<p>This plugin allows you to search for posts by a specific user in the trash bin. Note that it is incomplete and does not work.</p>
			</div>
		</div>

		<h2 class="block2"><span>Enter data</span></h2>
		<div class="box">
			<form action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="get">
				<table border="0">
					<tr>
						<th>Username</th>
						<td><input type="text" name="username" /><input type="hidden" name="plugin" value="<?php echo $plugin; ?>" /></td>
					</tr>
					<tr>
						<th>Mod deletes only</th>
						<td><input type="checkbox" name="adminonly" value="1" /></td>
					</tr>
				</table>
				<p class="submitbottom"><input type="submit" name="form_sent" value="Search" /></p>
			</form>
		</div>
	</div>
<?php
}