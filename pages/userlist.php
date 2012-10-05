<?php
$page_title = 'Users - Mod Share';
function getUsers($query = false) {
	global $db;
	$result = $db->query('SELECT username,id,permission FROM users
	WHERE status<>\'disabledbyadmin\'' . ($query ? ' AND username LIKE \'' . str_replace('*', '%', $db->escape($query)) . '\'' : '') . '
	ORDER BY username ASC') or error('Failed to get users', __FILE__, __LINE__, $db->error());
	?>
	
	<?php
	if (!$db->num_rows($result)) {
		echo '<p>Your search returned no results! :(</p>';
	}
	while ($cur_user = $db->fetch_assoc($result)) {
		echo '<p>' . parse_username($cur_user) . '</p>';
	}
}
if (isset($_POST['query'])) {
	ob_end_clean();
	getUsers($_POST['query']);
	die;
}
?>
<h2>Users on Mod Share</h2>
<p>Search users: <input type="text" onkeypress="if (event.keyCode == 13) { searchUser(this.value) }" /></p>
<div id="users">
<?php
getUsers();
?>
</div>
<script type="text/javascript">
//<![CDATA[
function searchUser(query) {
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else {
		 req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	document.getElementById('users').innerHTML = 'Working...';
	req.open("POST", "/users", true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	req.send("query=" + encodeURIComponent(query));
	
	req.onreadystatechange = function() {
		if (req.readyState==4 && req.status==200) {
			document.getElementById('users').innerHTML = req.responseText;
		} else {
			document.getElementById('users').innerHTML = 'Error: ' + req.status;
		}
	 }
}
//]]>
</script>