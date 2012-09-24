<?php
header('Content-type: application/swf-object');
echo file_get_contents(SRV_ROOT . '/includes/static/player.swf');