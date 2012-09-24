<?php

$page_title = 'Admin Menu - Mod Share';

if (isset($_POST['form_sent'])) {
	set_config('status', $_POST['status']);
	set_config('maintenance_msg', $_POST['maintenance_msg']);
	set_config('announcement', $_POST['announcement']);
	set_config('election', $_POST['election']);
	header('Refresh: 0');

	addlog('Updated site configuration'); die;
}

if(isset($_POST['goquery'])) {
	$qry = $_POST['qry'];
	$result = $db->query($qry) or error('Failed MySQL query', __FILE__, __LINE__, $db->error());
	while($currdata = $db->fetch_assoc($result)) {
		$mysqlresponse .= chr(13) . print_r($currdata);
	}
	addlog('Executed custom query: ' . $qry);
}

?>

<h2>Admin Tools</h2>

<p>This contains stuff such as site configuration. To access stuff like bans, please visit the <a href="/admin/mod_menu">moderator menu</a>.</p>
<p><a href="/pma">&gt; PHPmyAdmin</a>&nbsp;&nbsp;<a href="/data/log.txt">&gt; View Log</a>&nbsp;&nbsp;<a href="/admin/archive_log">&gt; Archive Log</a></p>
<h3>Site info</h3>
<p>Username: modshare</p>
<p>Time: <?php echo date('r') . ', Unix ' . time() ?></p>
<p>Server root: <?php echo SRV_ROOT; ?></p>
<h3>Management pages</h3>
<p><a href="/admin/maintenance">Maintenance</a></p>
<h3>MySQL query</h3>
<form id="mysql" name="mysql" method="post" action="/admin/admin_menu">
  <p>
    <input name="qry" type="text" id="qry" size="40" style="min-height: 20px;" />
    <input type="submit" name="goquery" id="goquery" value="Submit" style="min-height: 26px;" />
  </p>
</form>
<form action="/admin/admin_menu" method="post" enctype="multipart/form-data">
  <h3>Site status</h3>

	<p>

		<input type="radio" name="status" value="normal"<?php if ($ms_config['status'] == 'normal') echo ' checked="checked"'; ?> />Normal<br />

		<input type="radio" name="status" value="warning"<?php if ($ms_config['status'] == 'warning') echo ' checked="checked"'; ?> />Warning (disable uploading and registration)<br />

		<input type="radio" name="status" value="maint"<?php if ($ms_config['status'] == 'maint') echo ' checked="checked"'; ?> />Maintenance

	</p>

	<p><a href="/admin/election_setup">Election?</a> <input type="radio" name="election" value="1"<?php if ($ms_config['election']) echo ' checked="checked"'; ?> />Yes &nbsp; <input type="radio" name="election" value="0"<?php if (!$ms_config['election']) echo ' checked="checked"'; ?> />No</p>

	<p><strong>Maintenance message</strong><br /><textarea name="maintenance_msg" rows="4" cols="60"><?php echo clearHTML($ms_config['maintenance_msg']); ?></textarea></p>

	<p><strong>Announcement message</strong><br /><textarea name="announcement" rows="4" cols="60"><?php echo clearHTML($ms_config['announcement']); ?></textarea></p>

	<input type="submit" name="form_sent" value="Save changes" />

</form>