	<hr class="clearfloat" />
	<div id="footer">
		<p>Copyright &copy;2011-2012 - LS97 and jvvg<?php if (!$ms_user['banned']) { ?> - <a href="/terms/">Terms of Use</a><?php } ?> - <a href="/donate">Donate</a>
		<?php if ($ms_user['is_mod']) {
			echo ' - <a href="/admin/mod_menu">Moderator Menu</a>';
			$result = $db->query('SELECT 1 FROM flags WHERE zapped IS NULL') or error('Failed to check for unread flags', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				echo ' (there are new flags)';
			}
			$result = $db->query('SELECT 1 FROM notificationstoadmin WHERE zapped=0 OR zapped IS NULL') or error('Failed to check for unread flags', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result)) {
				echo ' (there are new alerts)';
			}
		}
		if ($ms_user['is_admin']) echo ' - <a href="/admin/admin_menu">Admin Menu</a>'; ?>
		<?php if ($ms_config['status'] == 'warning') echo '<br />Be advised that registration and uploading are disabled for now.'; ?>
		<?php if ($ms_user['permission'] == 3 && MS_DEBUG) echo '<br />Page generated in ' . $db->num_queries . ' queries.'; ?></p>
	</div>