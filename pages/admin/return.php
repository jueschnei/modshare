<?php
$_SESSION['uid'] = $_SESSION['origid'];
unset($_SESSION['origid']);
echo '<meta http-equiv="Refresh" content="0; url=/" />';