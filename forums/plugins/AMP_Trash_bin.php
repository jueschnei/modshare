<?php

/**
 * Copyright (C) 2008-2010 FluxBB
 * based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

##
##
##  A few notes of interest for aspiring plugin authors:
##
##  1. If you want to display a message via the message() function, you
##     must do so before calling generate_admin_menu($plugin).
##
##  2. Plugins are loaded by admin_loader.php and must not be
##     terminated (e.g. by calling exit()). After the plugin script has
##     finished, the loader script displays the footer, so don't worry
##     about that. Please note that terminating a plugin by calling
##     message() or redirect() is fine though.
##
##  3. The action attribute of any and all <form> tags and the target
##     URL for the redirect() function must be set to the value of
##     $_SERVER['REQUEST_URI']. This URL can however be extended to
##     include extra variables (like the addition of &amp;foo=bar in
##     the form of this example plugin).
##
##  4. If your plugin is for administrators only, the filename must
##     have the prefix "AP_". If it is for both administrators and
##     moderators, use the prefix "AMP_". This example plugin has the
##     prefix "AMP_" and is therefore available for both admins and
##     moderators in the navigation menu.
##
##  5. Use _ instead of spaces in the file name.
##
##  6. Since plugin scripts are included from the FluxBB script
##     admin_loader.php, you have access to all FluxBB functions and
##     global variables (e.g. $db, $pun_config, $pun_user etc).
##
##  7. Do your best to keep the look and feel of your plugins' user
##     interface similar to the rest of the admin scripts. Feel free to
##     borrow markup and code from the admin scripts to use in your
##     plugins. If you create your own styles they need to be added to
##     the "base_admin" style sheet.
##
##  8. Plugins must be released under the GNU General Public License or
##     a GPL compatible license. Copy the GPL preamble at the top of
##     this file into your plugin script and alter the copyright notice
##     to refrect the author of the plugin (i.e. you).
##
##


// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
	exit;


if (file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/admin_plugin_trash_bin.php'))
	require PUN_ROOT.'lang/'.$pun_user['language'].'/admin_plugin_trash_bin.php';
else
	require PUN_ROOT.'lang/English/admin_plugin_trash_bin.php';

require PUN_ROOT.'lang/'.$admin_language.'/admin_plugin_trash_bin.php';

require PUN_ROOT.'include/parser.php';
require PUN_ROOT.'include/search_idx.php';

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);

//
// The rest is up to you!
//

// Generate $_SERVER['REQUEST_URI_SHORT'] without the GET parameters
$_SERVER['REQUEST_URI_SHORT'] = explode('&', $_SERVER['REQUEST_URI']);
$_SERVER['REQUEST_URI_SHORT'] = $_SERVER['REQUEST_URI_SHORT'][0];

// Function to display "header" informations
function display_header($trash_infos = FALSE)
{

	global $db;
	global $pun_user;
	global $lang_admin_plugin_trash_bin;
	global $plugin;

	// Display the admin navigation menu
	generate_admin_menu($plugin);
	?>
	<div class="plugin blockform">
	<?php
	if($trash_infos)
	{
	
?>
		<h2><span><?php echo $lang_admin_plugin_trash_bin['Plugin title'] ?></span></h2>
		<div class="box">
			<div class="inbox">
			<?php
			// We count and display the number of topics in the trash bin
			$result = $db->query('SELECT count(*) AS nb_discussions, SUM(num_replies) AS nb_replies FROM '.$db->prefix.'trash_topics') or error('Unable to count topcis in trash', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result))
				{
				$cur_trash = $db->fetch_assoc($result);
				$nb_topics = $cur_trash['nb_discussions'];
				$nb_replies = intval($cur_trash['nb_replies']) ? $cur_trash['nb_replies'] : 0;
				
				echo '<p>';
				printf($lang_admin_plugin_trash_bin['Nb trash topic'], $nb_topics, $nb_replies);
				echo ' <a href="admin_loader.php?plugin=AMP_Trash_bin.php&amp;show=topics">'.$lang_admin_plugin_trash_bin['Show topics in trash'].'</a></p>';
				}
				
			// We count and display the number of posts alone in the trash bin
			$result = $db->query('SELECT count(*) nb_messages FROM '.$db->prefix.'trash_posts WHERE post_alone = 1') or error('Unable to count topcis in trash', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result))
				{
				$cur_trash = $db->fetch_assoc($result);
				$nb_posts = $cur_trash['nb_messages'];
				
				echo '<p>';
				printf($lang_admin_plugin_trash_bin['Nb trash post'], $nb_posts);
				echo ' <a href="admin_loader.php?plugin=AMP_Trash_bin.php&amp;show=posts">'.$lang_admin_plugin_trash_bin['Show posts in trash'].'</a></p>';
				}
				
			if($pun_user['group_id'] == PUN_ADMIN)
				echo "<p><a href='admin_loader.php?plugin=AMP_Trash_bin.php&amp;admin'>".$lang_admin_plugin_trash_bin['Admin link']."</a></p>";
			
			?>
			</div>
		</div>
	<?php
	}
}

// Delete / restore post from trash bin
if(isset($_POST['pid']))
{
	$pid = intval($_POST['pid']);
	if ($pid < 1)
		message($lang_common['Bad request']);
	else
	{
		// Does the post exists in the trash_posts table ?
		$result = $db->query('SELECT p.id, p.message, p.topic_id, t.forum_id FROM '.$db->prefix.'trash_posts AS p INNER JOIN '.$db->prefix.'topics AS t ON p.topic_id = t.id AND p.id = '.$pid) or error('Unable to fetch trash post information', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang_common['Bad request']);
		else
		{
			$cur_post = $db->fetch_assoc($result);
			// What action do we have to do ?
			// We want te restore the post
			if(isset($_POST['restore']))
			{
				// Do we have the right to do it ?
				if(!$pun_user['g_bin_restore'])
					message($lang_common['Bad request']);
				else
				{
					// Does the post already exists in the post table ?
					$result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE id = '.$pid) or error('Unable to fetch post information', __FILE__, __LINE__, $db->error());
					if ($db->num_rows($result))
						message($lang_common['Bad request']);
					else
					{
						// We restore the post
						$db->query('INSERT INTO '.$db->prefix.'posts (id, poster, poster_id, poster_ip, poster_email, message, hide_smilies, posted, edited, edited_by, topic_id) SELECT id, poster, poster_id, poster_ip, poster_email, message, hide_smilies, posted, edited, edited_by, topic_id FROM '.$db->prefix.'trash_posts WHERE id = '.$pid) or error('Unable to restore the post', __FILE__, __LINE__, $db->error());
						
						// We index the post
						update_search_index('post', $pid, $cur_post['message']);
						
						// We delete the post from the trash_posts table
						$db->query('DELETE FROM '.$db->prefix.'trash_posts WHERE id = '.$pid) or error('Unable to delete the post that we restored', __FILE__, __LINE__, $db->error());
						
						// We update topic and forum informations
						update_topic($cur_post['topic_id']);
						update_forum($cur_post['forum_id']);
						
						
						redirect('viewtopic.php?pid='.$pid.'#p'.$pid, $lang_admin_plugin_trash_bin['Restore post redirect']);	
					}
				}
			}
			// We want to definitely delete the post
			else if($_POST['delete'])
			{
				// Do we have the right to do it ?
				if(!$pun_user['g_bin_delete'])
					message($lang_common['Bad request']);
				else
				{
					// We delete the post from the trash_posts table
					$db->query('DELETE FROM '.$db->prefix.'trash_posts WHERE id = '.$pid) or error('Unable to delete the post', __FILE__, __LINE__, $db->error());
					redirect($_SERVER['REQUEST_URI_SHORT'], $lang_admin_plugin_trash_bin['Delete post redirect']);	
				}
			}
		}
	}
}
// Delete / restore topic from trash bin
else if(isset($_POST['tid']))
{
	$tid = intval($_POST['tid']);
	if ($tid < 1)
		message($lang_common['Bad request']);
	else
	{
		// Does the topic exists in the trash_posts table ?
		$result = $db->query('SELECT id, forum_id FROM '.$db->prefix.'trash_topics WHERE id = '.$tid) or error('Unable to fetch trash topic information', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang_common['Bad request']);
		else
		{
			$cur_forum = $db->fetch_assoc($result);
			
			// What action do we have to do ?
			// We want te restore the topic
			if(isset($_POST['restore']))
			{
				// Do we have the right to do it ?
				if(!$pun_user['g_bin_restore'])
					message($lang_common['Bad request']);
				else
				{
					// Does the topic already exists in the topic table, or one of the post in the post table ?
					$result_topic = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE id = '.$tid) or error('Unable to fetch topic information', __FILE__, __LINE__, $db->error());
					$result_posts = $db->query('SELECT p.id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'trash_posts AS tp ON p.id = tp.id AND tp.topic_id = '.$tid) or error('Unable to fetch post information', __FILE__, __LINE__, $db->error());
					
					if ($db->num_rows($result_topic) || $db->num_rows($result_posts))
						message($lang_common['Bad request']);
					else
					{
						// We restore the topic
						$db->query('INSERT INTO '.$db->prefix.'topics (id, poster, subject, posted, first_post_id, last_post, last_post_id, last_poster, num_views, num_replies, closed, sticky, moved_to, forum_id) SELECT id, poster, subject, posted, first_post_id, last_post, last_post_id, last_poster, num_views, num_replies, closed, sticky, moved_to, forum_id FROM '.$db->prefix.'trash_topics WHERE id = '.$tid) or error('Unable to restore the topic', __FILE__, __LINE__, $db->error());
						// We delete the topic from the trash_topics table
						$db->query('DELETE FROM '.$db->prefix.'trash_topics WHERE id = '.$tid) or error('Unable to delete the topic that we restored', __FILE__, __LINE__, $db->error());
						
						// We restore the posts
						$db->query('INSERT INTO '.$db->prefix.'posts (id, poster, poster_id, poster_ip, poster_email, message, hide_smilies, posted, edited, edited_by, topic_id) SELECT id, poster, poster_id, poster_ip, poster_email, message, hide_smilies, posted, edited, edited_by, topic_id FROM '.$db->prefix.'trash_posts WHERE topic_id = '.$tid) or error('Unable to restore the post from this topic', __FILE__, __LINE__, $db->error());
						// We delete the post from the trash_posts table
						$db->query('DELETE FROM '.$db->prefix.'trash_posts WHERE topic_id = '.$tid) or error('Unable to delete the post that we restored with this topic', __FILE__, __LINE__, $db->error());
						
						// We fetch all the post to re-index them
						$result = $db->query('SELECT p.id, p.message, t.first_post_id, t.id AS topic_id, t.subject FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON p.topic_id = t.id AND t.id = '.$tid) or error('Unable to fetch posts messages', __FILE__, __LINE__, $db->error());
						
						while($cur_post = $db->fetch_assoc($result))
						{
							// If it's the first post, we index the subject
							if($cur_post['id'] == $cur_post['first_post_id'])
								update_search_index('post', $cur_post['id'], $cur_post['message'], $cur_post['subject']);
							else
								update_search_index('post', $cur_post['id'], $cur_post['message']);
						
						}
						
						// We update forum informations
						update_forum($cur_forum['forum_id']);
						
						// We redirect to the topic restored
						redirect('viewtopic.php?id='.$tid, $lang_admin_plugin_trash_bin['Restore topic redirect']);	
					}
				}
			}
			// We want to definitely delete the topic
			else if($_POST['delete'])
			{
				// Do we have the right to do it ?
				if(!$pun_user['g_bin_delete'])
					message($lang_common['Bad request']);
				else
				{
					// We delete the post from the trash_posts table
					$db->query('DELETE FROM '.$db->prefix.'trash_posts WHERE topic_id = '.$tid) or error('Unable to delete the post from the topic to delete', __FILE__, __LINE__, $db->error());
					// We delete the topic from the trash_topics table
					$db->query('DELETE FROM '.$db->prefix.'trash_topics WHERE id = '.$tid) or error('Unable to delete the topic', __FILE__, __LINE__, $db->error());
					redirect($_SERVER['REQUEST_URI_SHORT'], $lang_admin_plugin_trash_bin['Delete topic redirect']);	
				}
			}
		}
	}
}
// Update right group
else if(isset($_POST['right_update']))
{
	if($pun_user['group_id'] != PUN_ADMIN)
		message($lang_admin_plugin_trash_bin['Bad request']);
	else
	{
		$result = $db->query('SELECT g_id, g_title, g_moderator, g_bin_posts, g_bin_topics, g_empty_bin, g_bin_restore, g_bin_delete FROM '.$db->prefix.'groups') or error('Unable fecth group', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
		{
			while($cur_group = $db->fetch_assoc($result))
			{
				if(isset($_POST['g_'.$cur_group['g_id']]))
				{
					for($i=0; $i<5; $i++)
						{
						if(!isset($_POST['g_'.$cur_group['g_id']][$i]))
							$_POST['g_'.$cur_group['g_id']][$i] = 0;
						else
							$_POST['g_'.$cur_group['g_id']][$i] = (!$_POST['g_'.$cur_group['g_id']][$i] ? 0 : 1);
						}
														
						$db->query('UPDATE '.$db->prefix.'groups SET g_bin_posts = '.$_POST['g_'.$cur_group['g_id']][0].', g_bin_topics = '.$_POST['g_'.$cur_group['g_id']][1].', g_bin_restore = '.$_POST['g_'.$cur_group['g_id']][2].', g_bin_delete = '.$_POST['g_'.$cur_group['g_id']][3].', g_empty_bin = '.$_POST['g_'.$cur_group['g_id']][4].' WHERE g_id = '.$cur_group['g_id']) or error('Unable to update group rights', __FILE__, __LINE__, $db->error());
				}
			}
		}
		redirect($_SERVER['REQUEST_URI'], $lang_admin_plugin_trash_bin['Update right redirect']);
	}
}
// We want to empty the posts bin
else if(isset($_POST['empty_post_bin']))
{
	if(!$pun_user['g_empty_bin'])
		message($lang_admin_plugin_trash_bin['Bad request']);
	
	if(isset($_POST['comply']))
	{
		$db->query('DELETE FROM '.$db->prefix.'trash_posts WHERE post_alone = 1') or error('Unable to empty posts bin', __FILE__, __LINE__, $db->error());
		redirect($_SERVER['REQUEST_URI'], $lang_admin_plugin_trash_bin['Empty posts bin redirect']);
	}
	else
	{
		display_header(FALSE);
		?>
		<h2><span><?php echo $lang_admin_plugin_trash_bin['Empty posts bin title'] ?></span></h2>
		<div class="box">
			<div class="inbox">
				<form method='post' action='<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>'>
				<input type='hidden' name='comply' value='1' />
				<p><?php echo $lang_admin_plugin_trash_bin['Empty posts bin'] ?></p>
				<p><input type='submit' value='<?php echo $lang_admin_plugin_trash_bin['Empty posts bin title'] ?>' name='empty_post_bin' />  <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
				</form>
			</div>
		</div>
	</div>
		<?php
	}
}
// We want to empty the topics bin
else if(isset($_POST['empty_topic_bin']))
{
	if(!$pun_user['g_empty_bin'])
		message($lang_admin_plugin_trash_bin['Bad request']);
	
	if(isset($_POST['comply']))
	{
		$db->query('DELETE FROM '.$db->prefix.'trash_posts WHERE post_alone = 0') or error('Unable to empty posts bin', __FILE__, __LINE__, $db->error());
		$db->query('DELETE FROM '.$db->prefix.'trash_topics') or error('Unable to empty topics bin', __FILE__, __LINE__, $db->error());
		
		redirect($_SERVER['REQUEST_URI'], $lang_admin_plugin_trash_bin['Empty topics bin redirect']);
	}
	else
	{
		display_header(FALSE);
		?>
		<h2><span><?php echo $lang_admin_plugin_trash_bin['Empty topics bin title'] ?></span></h2>
		<div class="box">
			<div class="inbox">
				<form method='post' action='<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>'>
				<input type='hidden' name='comply' value='1' />
				<p><?php echo $lang_admin_plugin_trash_bin['Empty topics bin'] ?></p>
				<p><input type='submit' value='<?php echo $lang_admin_plugin_trash_bin['Empty topics bin title'] ?>' name='empty_topic_bin' />  <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
				</form>
			</div>
		</div>
	</div>
		<?php
	}
}
// We want to modify groups right
else if (isset($_GET['admin']))
{
	// Make sure that we have the right to be here
	if($pun_user['group_id'] != PUN_ADMIN)
		message($lang_admin_plugin_trash_bin['Bad request']);
	else
	{
		// Display the admin navigation menu
		display_header(FALSE);

		?>
		<h2><span><?php echo $lang_admin_plugin_trash_bin['Admin title'] ?></span></h2>
		<div class="box">
			<div class="inbox">
				<form method='post' action='<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>'>
				<p><?php echo $lang_admin_plugin_trash_bin['Def bin post'] ?></p>
				<p><?php echo $lang_admin_plugin_trash_bin['Def bin topic'] ?></p>
				<p><?php echo $lang_admin_plugin_trash_bin['Def restore'] ?></p>
				<p><?php echo $lang_admin_plugin_trash_bin['Def delete'] ?></p>
				<p><?php echo $lang_admin_plugin_trash_bin['Def empty bin'] ?></p>
				
				<table id='forumperms'>
				<thead>
					<tr>
						<th><?php echo $lang_admin_plugin_trash_bin['Title group'] ?></th>
						<th><?php echo $lang_admin_plugin_trash_bin['Title bin post'] ?></th>
						<th><?php echo $lang_admin_plugin_trash_bin['Title bin topic'] ?></th>
						<th><?php echo $lang_admin_plugin_trash_bin['Title restore'] ?></th>
						<th><?php echo $lang_admin_plugin_trash_bin['Title delete'] ?></th>
						<th><?php echo $lang_admin_plugin_trash_bin['Title empty bin'] ?></th>
					</tr>
				</thead>
				<?php
				$result = $db->query('SELECT g_id, g_title, g_moderator, g_bin_posts, g_bin_topics, g_empty_bin, g_bin_restore, g_bin_delete FROM '.$db->prefix.'groups') or error('Unable to cfecth group', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result))
			{
				?>
				<tbody>
				<?php
				while($cur_group = $db->fetch_assoc($result))
				{
					if($cur_group['g_id'] != PUN_ADMIN && $cur_group['g_moderator'] == 0)
					{
					?>
					<tr>
						<td><?php echo pun_htmlspecialchars($cur_group['g_title']) ?></td>
						<td><input type='checkbox' name='g_<?php echo $cur_group['g_id'] ?>[0]'<?php echo $cur_group['g_bin_posts'] == 1 ? " checked='checked'" : "" ?> /></td>
						<td><input type='checkbox' name='g_<?php echo $cur_group['g_id'] ?>[1]'<?php echo $cur_group['g_bin_topics'] == 1 ? " checked='checked'" : "" ?> /></td>
						<td><input type='hidden' name='g_<?php echo $cur_group['g_id'] ?>[2]' value='FALSE' /></td>
						<td><input type='hidden' name='g_<?php echo $cur_group['g_id'] ?>[3]' value='FALSE' /></td>
						<td><input type='hidden' name='g_<?php echo $cur_group['g_id'] ?>[4]' value='FALSE' /></td>
					</tr>
					<?php
					}
					else
					{
					?>
					<tr>
						<td><?php echo pun_htmlspecialchars($cur_group['g_title']) ?></td>
						<td><input type='checkbox' name='g_<?php echo $cur_group['g_id'] ?>[0]'<?php echo $cur_group['g_bin_posts'] == 1 ? " checked='checked'" : "" ?> /></td>
						<td><input type='checkbox' name='g_<?php echo $cur_group['g_id'] ?>[1]'<?php echo $cur_group['g_bin_topics'] == 1 ? " checked='checked'" : "" ?> /></td>
						<td><input type='checkbox' name='g_<?php echo $cur_group['g_id'] ?>[2]'<?php echo $cur_group['g_bin_restore'] == 1 ? " checked='checked'" : "" ?> /></td>
						<td><input type='checkbox' name='g_<?php echo $cur_group['g_id'] ?>[3]'<?php echo $cur_group['g_bin_delete'] == 1 ? " checked='checked'" : "" ?> /></td>
						<td><input type='checkbox' name='g_<?php echo $cur_group['g_id'] ?>[4]'<?php echo $cur_group['g_empty_bin'] == 1 ? " checked='checked'" : "" ?> /></td>
					</tr>
					<?php
					}
				}
				?>
				</tbody>
				<?php
			}
				?>
				</table>
				<p><input type='submit' value='<?php echo $lang_admin_plugin_trash_bin['Save right'] ?>' name='right_update' /></p>
				</form>
				<p><a href='<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI_SHORT']); ?>'><?php echo $lang_admin_plugin_trash_bin['Go back'] ?></a></p>
			</div>
		</div>
	</div>
	<?php
	}
}
else 
{
		if(isset($_GET['show']))
		{
			if($_GET['show'] == 'topics')
			{
			// Display the admin navigation menu
			display_header(TRUE);
			
				?>
		<h2 class="block2"><span><?php echo $lang_admin_plugin_trash_bin['Show topics title'] ?></span></h2>
			<div class="box">
			<?php
				$result = $db->query('SELECT tp.id, tp.poster, tp.subject, tp.forum_id, tp.trasher, tp.trasher_id, tp.trashed, f.forum_name FROM '.$db->prefix.'trash_topics AS tp INNER JOIN '.$db->prefix.'forums AS f ON tp.forum_id = f.id ORDER BY trashed DESC') or error('Unable to fetch posts list', __FILE__, __LINE__, $db->error());

				if (!$db->num_rows($result))
					echo "<p>".$lang_admin_plugin_trash_bin['No topic']."</p>";
				else
				{
				if($pun_user['g_empty_bin'])
					{
					?>
					<form method="post" action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
					<p><input type='submit' value='<?php echo $lang_admin_plugin_trash_bin['Empty topics bin title'] ?>' name='empty_topic_bin' /></p>
					</form>
					<?php
					}
				?>
				<div id="vf" class="blocktable">
				<table>
				<thead>
					<tr>
						<th><?php echo $lang_admin_plugin_trash_bin['Topics infos'] ?></th>
						<th><?php echo $lang_admin_plugin_trash_bin['Trash infos'] ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					while($cur_trash = $db->fetch_assoc($result))
					{
						$topic_infos = '<a href="admin_loader.php?plugin=AMP_Trash_bin.php&amp;tid='.$cur_trash['id'].'">'.pun_htmlspecialchars($cur_trash['forum_name']).'&#160;»&#160;'.pun_htmlspecialchars($cur_trash['subject']).'</a>';
						$trash_infos = $lang_admin_plugin_trash_bin['Trashed by'].' <a href="profile.php?id='.$cur_trash['trasher_id'].'">'.pun_htmlspecialchars($cur_trash['trasher']).'</a> ('.format_time($cur_trash['trashed']).')';
						
						?>
					<tr>
						<td><?php echo $topic_infos ?></td>
						<td><?php echo $trash_infos ?></td>
					</tr>
						<?php
					}
				?>
				
				</tbody>
				</table>
				</div>
				<?php
				}
			?>
			</div>
			<?php
			}
			else if($_GET['show'] == 'posts')
			{
			// Display the admin navigation menu
			display_header(TRUE);
			
			?>
			<h2 class="block2"><span><?php echo $lang_admin_plugin_trash_bin['Show posts title'] ?></span></h2>
			<div class="box">
			<?php
				$result = $db->query('SELECT tp.id, tp.poster, tp.posted, tp.trasher, tp.trasher_id, tp.trashed, tp.topic_id, t.subject, t.forum_id, f.forum_name FROM '.$db->prefix.'trash_posts AS tp
INNER JOIN '.$db->prefix.'topics AS t ON tp.topic_id = t.id AND tp.post_alone = 1 INNER JOIN '.$db->prefix.'forums AS f ON t.forum_id = f.id ORDER BY trashed DESC') or error('Unable to fetch posts list', __FILE__, __LINE__, $db->error());

				if (!$db->num_rows($result))
					echo "<p>".$lang_admin_plugin_trash_bin['No post']."</p>";
				else
				{
				if($pun_user['g_empty_bin'])
					{
					?>
					<form method="post" action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
					<p><input type='submit' value='<?php echo $lang_admin_plugin_trash_bin['Empty posts bin title'] ?>' name='empty_post_bin' /></p>
					</form>
					<?php
					}
				?>
				<div id="vf" class="blocktable">
				<table>
				<thead>
					<tr>
						<th><?php echo $lang_admin_plugin_trash_bin['Posts infos'] ?></th>
						<th><?php echo $lang_admin_plugin_trash_bin['Trash infos'] ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					while($cur_trash = $db->fetch_assoc($result))
					{
						$posts_infos = pun_htmlspecialchars($cur_trash['forum_name']).'&#160;»&#160;'.pun_htmlspecialchars($cur_trash['subject']).'&#160;»&#160;<a href="admin_loader.php?plugin=AMP_Trash_bin.php&amp;pid='.$cur_trash['id'].'">'.format_time($cur_trash['posted']).' '.$lang_common['by']. ' '.pun_htmlspecialchars($cur_trash['poster']).'</a>';
						if ($cur_trash['method'] == 1) {
							$method = 'Edited by';
						} else {
							$method = 'Deleted by';
						}
						$trash_infos = $method . ' <a href="profile.php?id='.$cur_trash['trasher_id'].'">'.pun_htmlspecialchars($cur_trash['trasher']).'</a> ('.format_time($cur_trash['trashed']).')';
						
						?>
					<tr>
						<td><?php echo $posts_infos ?></td>
						<td><?php echo $trash_infos ?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
				</table>
				<?php
				}
			?>
			</div>
		</div>
			<?php
			}
		}
		else if(isset($_GET['tid']))
		{
			$tid = intval($_GET['tid']);
			if ($tid < 1)
				message($lang_common['Bad request']);
			
			$result = $db->query('SELECT tp.id, tp.subject, tp.forum_id, tp.trasher, tp.trasher_id, tp.trashed, f.forum_name FROM '.$db->prefix.'trash_topics AS tp INNER JOIN '.$db->prefix.'forums AS f ON tp.forum_id = f.id AND tp.id = '.$tid) or error('Unable to fetch posts list', __FILE__, __LINE__, $db->error());
			
			if (!$db->num_rows($result))
				message($lang_common['Bad request']);
			else
			{
				// We check the rights
				if((isset($_GET['delete']) && !$pun_user['g_bin_delete']) || isset($_GET['restore']) && !$pun_user['g_bin_restore'])
					message($lang_common['Bad request']);
				else
					{
						$cur_topic = $db->fetch_assoc($result);
						$trashed_infos = $lang_admin_plugin_trash_bin['Trashed by'].'<a href="profile.php?id='.$cur_topic['trasher_id'].'">'.pun_htmlspecialchars($cur_topic['trasher']).'</a> ('.format_time($cur_topic['trashed']).')';
						
						$result = $db->query('SELECT poster, poster_id, message, posted FROM '.$db->prefix.'trash_posts WHERE topic_id = '.$tid.' ORDER BY id LIMIT 0, '.$pun_user['disp_posts'].'') or error('Unable to fetch posts list', __FILE__, __LINE__, $db->error());
						
						// We want to delete the topic
						if(isset($_GET['delete']))
						{
						
						// Display the admin navigation menu
						display_header(FALSE);
						
							?>
						<h2 class="block2"><span><?php echo $lang_admin_plugin_trash_bin['Link delete topic'] ?></span></h2>
					<div class="box">	
					<p><?php echo $lang_admin_plugin_trash_bin['Delete topic'].' '.$trashed_infos ?></p>
					<form method="post" action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
					<input type='hidden' name='tid' value='<?php echo $tid ?>' />
					<p><input type='submit' name='delete' value='<?php echo $lang_admin_plugin_trash_bin['Link delete topic'] ?>' />  <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
					</form>
					<div id="vf" class="blocktable">
						<table>
					
							<tr>
								<th><?php echo pun_htmlspecialchars($cur_topic['forum_name']) ?>&#160;»&#160;<?php echo pun_htmlspecialchars($cur_topic['subject']) ?></th>
							</tr>
							
							<?php
							while($cur_post = $db->fetch_assoc($result))
								{
									?>
									<tr>
										<th><a href='profile.php?id=<?php echo $cur_post['poster_id'] ?>'><?php echo pun_htmlspecialchars($cur_post['poster']).'</a> ('.format_time($cur_post['posted']).')' ?></th>
									</tr>
									<!--<tr class='pun'>-->
									<tr>
										<td class='postmsg'><?php echo parse_message($cur_post['message'], 0) ?></td>
									</tr>
									<?php
								}
							?>
						</table>
							<?php
						
						}			
						// We want to restore the topic
						else if(isset($_GET['restore']))
						{
							// Display the admin navigation menu
							display_header(FALSE);
							?>
					<h2 class="block2"><span><?php echo $lang_admin_plugin_trash_bin['Link restore topic'] ?></span></h2>
					<div class="box">	
					<p><?php echo $lang_admin_plugin_trash_bin['Restore topic'].' '.$trashed_infos ?></p>
					<form method="post" action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
					<input type='hidden' name='tid' value='<?php echo $tid ?>' />
					<p><input type='submit' name='restore' value='<?php echo $lang_admin_plugin_trash_bin['Link restore topic'] ?>' />  <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
					</form>
					<div id="vf" class="blocktable">
						<table>
					
							<tr>
								<th><?php echo pun_htmlspecialchars($cur_topic['forum_name']) ?>&#160;»&#160;<?php echo pun_htmlspecialchars($cur_topic['subject']) ?></th>
							</tr>
							
							<?php
							while($cur_post = $db->fetch_assoc($result))
								{
									?>
									<tr>
										<th><a href='profile.php?id=<?php echo $cur_post['poster_id'] ?>'><?php echo pun_htmlspecialchars($cur_post['poster']).'</a> ('.format_time($cur_post['posted']).')' ?></th>
									</tr>
									<tr class='pun'>
										<td class='postmsg'><?php echo parse_message($cur_post['message'], 0) ?></td>
									</tr>
									<?php
								}
							?>
						</table>
							<?php
						}
						else
						{
							if($pun_user['g_bin_restore'] == 1)
								$trashed_infos .= ' | <strong><a href="'.pun_htmlspecialchars($_SERVER['REQUEST_URI']).'&amp;restore">'.$lang_admin_plugin_trash_bin['Link restore topic'].'</a></strong>';
							if($pun_user['g_bin_delete'] == 1)
								$trashed_infos .= ' | <strong><a href="'.pun_htmlspecialchars($_SERVER['REQUEST_URI']).'&amp;delete">'.$lang_admin_plugin_trash_bin['Link delete topic'].'</a></strong>';
							
							$trashed_infos .= ' | <strong><a href="javascript:history.go(-1)">'.$lang_common['Go back'].'</a></strong>';
							
							$result = $db->query('SELECT poster, poster_id, message, posted FROM '.$db->prefix.'trash_posts WHERE topic_id = '.$tid.' ORDER BY id LIMIT 0, '.$pun_user['disp_posts'].'') or error('Unable to fetch posts list', __FILE__, __LINE__, $db->error());
							
							// Display the admin navigation menu
							display_header(TRUE);
							
							?>
						<h2 class="block2"><span><?php echo pun_htmlspecialchars($cur_topic['forum_name']) ?>&#160;»&#160;<?php echo pun_htmlspecialchars($cur_topic['subject']) ?></span></h2>
						<div class="box">	
							<p><?php echo $trashed_infos ?></p>
							
							<div id="vf" class="blocktable">
								<table>
							<?php
					
							while($cur_post = $db->fetch_assoc($result))
							{
								?>
								<tr>
									<th><a href='profile.php?id=<?php echo $cur_post['poster_id'] ?>'><?php echo pun_htmlspecialchars($cur_post['poster']).'</a> ('.format_time($cur_post['posted']).')' ?></th>
								</tr>
								
								<!--<tr class='pun'>-->
								<tr>
									<td class='postmsg'><?php echo parse_message($cur_post['message'], 0) ?></td>
								</tr>
								<?php
							}
							?>
								</table>
							</div>
							<?php
						}
					}
			}
			?>
			</div>
			<?php
		}
		else if(isset($_GET['pid']))
		{
			$pid = intval($_GET['pid']);
			if ($pid < 1)
				message($lang_common['Bad request']);
			
			$result = $db->query('SELECT p.id, p.poster, p.poster_id, p.message, p.posted, p.topic_id, p.trasher, p.trasher_id, p.trashed, t.subject, t.forum_id, f.forum_name FROM '.$db->prefix.'trash_posts AS p INNER JOIN '.$db->prefix.'topics AS t ON p.topic_id = t.id AND p.id = '.$pid.' INNER JOIN '.$db->prefix.'forums AS f ON t.forum_id = f.id') or error('Unable to fetch post information', __FILE__, __LINE__, $db->error());
			
			if (!$db->num_rows($result))
				message($lang_common['Bad request']);
			else
			{
				// We check the rights
				if((isset($_GET['delete']) && !$pun_user['g_bin_delete']) || isset($_GET['restore']) && !$pun_user['g_bin_restore'])
					message($lang_common['Bad request']);
				else
				{				
					// On récupère les informations du messages
					$cur_post = $db->fetch_assoc($result);
					$trashed_infos = $lang_admin_plugin_trash_bin['Trashed by'].'<a href="profile.php?id='.$cur_post['trasher_id'].'">'.pun_htmlspecialchars($cur_post['trasher']).'</a> ('.format_time($cur_post['trashed']).')';
					
					if(isset($_GET['delete']))
					{
					// Display the admin navigation menu
					display_header(FALSE);
					
						?>
		<h2 class="block2"><span><?php echo $lang_admin_plugin_trash_bin['Link delete post'] ?></span></h2>
		<div class="box">	
		<p><?php echo $lang_admin_plugin_trash_bin['Delete post'].' '.$trashed_infos ?></p>
		<form method="post" action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
		<input type='hidden' name='pid' value='<?php echo $pid ?>' />
		<p><input type='submit' name='delete' value='<?php echo $lang_admin_plugin_trash_bin['Link delete post'] ?>' />  <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
		<div id="vf" class="blocktable">
			<table>
		
				<tr>
					<th><?php echo pun_htmlspecialchars($cur_post['forum_name']) ?>&#160;»&#160;<?php echo pun_htmlspecialchars($cur_post['subject']) ?>&#160;»&#160;<a href='profile.php?id=<?php echo $cur_post['poster_id'] ?>'><?php echo pun_htmlspecialchars($cur_post['poster']).'</a> ('.format_time($cur_post['posted']).')' ?></th>
				</tr>
				<!--<tr class='pun'>-->
				<tr>
					<td class='postmsg'><?php echo parse_message($cur_post['message'], 0) ?></td>
				</tr>
			</table>
		</div>
						<?php	
					}			
					else if(isset($_GET['restore']))
					{
					// Display the admin navigation menu
					display_header(FALSE);
					
						?>
		<h2 class="block2"><span><?php echo $lang_admin_plugin_trash_bin['Link restore post'] ?></span></h2>
		<div class="box">	
		<p><?php echo $lang_admin_plugin_trash_bin['Restore post'].' '.$trashed_infos ?></p>
		<form method="post" action="<?php echo pun_htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
		<input type='hidden' name='pid' value='<?php echo $pid ?>' />
		<p><input type='submit' name='restore' value='<?php echo $lang_admin_plugin_trash_bin['Link restore post'] ?>' />  <a href="javascript:history.go(-1)"><?php echo $lang_common['Go back'] ?></a></p>
		</form>
		<div id="vf" class="blocktable">
			<table>
		
				<tr>
					<th><?php echo pun_htmlspecialchars($cur_post['forum_name']) ?>&#160;»&#160;<?php echo pun_htmlspecialchars($cur_post['subject']) ?>&#160;»&#160;<a href='profile.php?id=<?php echo $cur_post['poster_id'] ?>'><?php echo pun_htmlspecialchars($cur_post['poster']).'</a> ('.format_time($cur_post['posted']).')' ?></th>
				</tr>
				<!--<tr class='pun'>-->
				<tr>
					<td class='postmsg'><?php echo parse_message($cur_post['message'], 0) ?></td>
				</tr>
			</table>
		</div>
						<?php	
					}				
					else
					{
						if($pun_user['g_bin_restore'] == 1)
							$trashed_infos .= ' | <strong><a href="'.pun_htmlspecialchars($_SERVER['REQUEST_URI']).'&amp;restore">'.$lang_admin_plugin_trash_bin['Link restore post'].'</a></strong>';
						if($pun_user['g_bin_delete'] == 1)
							$trashed_infos .= ' | <strong><a href="'.pun_htmlspecialchars($_SERVER['REQUEST_URI']).'&amp;delete">'.$lang_admin_plugin_trash_bin['Link delete post'].'</a></strong>';
						
					$trashed_infos .= ' | <strong><a href="javascript:history.go(-1)">'.$lang_common['Go back'].'</a></strong>';
					
					// Display the admin navigation menu
					display_header(FALSE);
					?>
				<h2 class="block2"><span><?php echo pun_htmlspecialchars($cur_post['forum_name']) ?>&#160;»&#160;<?php echo pun_htmlspecialchars($cur_post['subject']) ?></span></h2>
				<div class="box">	
					<p><?php echo $trashed_infos ?></p>
					
					<div id="vf" class="blocktable">
						<table>
					
							<tr>
								<th><a href='profile.php?id=<?php echo $cur_post['poster_id'] ?>'><?php echo pun_htmlspecialchars($cur_post['poster']).'</a> ('.format_time($cur_post['posted']).')' ?></th>
							</tr>
							<!--<tr class='pun'>-->
							<tr>
								<td class='postmsg'><?php echo parse_message($cur_post['message'], 0) ?></td>
							</tr>
						</table>
					</div>
					<?php
					}
				}
			}
			
			?>
			</div>
			<?php
		}
		else
		{	
			// Display the admin navigation menu
			display_header(TRUE);
		}
		?>
		
	</div>
<?php

}

// Note that the script just ends here. The footer will be included by admin_loader.php
