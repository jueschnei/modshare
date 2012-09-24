<?php
$db->query('DELETE FROM notifications
WHERE id=' . intval($_POST['delid']) . '
AND user=' . $ms_user['id']) or error('Failed to delete notification', __FILE__, __LINE__, $db->error());