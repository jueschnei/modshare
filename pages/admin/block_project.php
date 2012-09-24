<?php

$db->query('UPDATE projects

SET status=\'blocked\'

WHERE id=' . intval($dirs[3])) or error('Failed to block project', __FILE__, __LINE__, $db->error());

addlog('Project block status updated for ' . intval($dirs[3]));

header('Location: ' . $_SERVER['HTTP_REFERER']);