<?php
// ARCHIVE THE LOG AND START A NEW ONE

$oldlog = file_get_contents(SRV_ROOT . '/data/log.txt');
file_put_contents(SRV_ROOT . '/data/logs/log' . time() . '.txt', $oldlog);
file_put_contents(SRV_ROOT . '/data/log.txt', 'MOD SHARE 4 LOG - ' . time());

header('Location: /admin/admin_menu');
?>