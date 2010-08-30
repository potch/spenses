<?php

require_once("./lib/openid.php");

set_include_path(get_include_path() . PATH_SEPARATOR . './action');
require_once "db.php";

$needslogin = false;
if (!isset($_COOKIE) || !isset($_COOKIE['user'])) $needslogin = true;

if ($_GET['debug'] == $cfg['godmode'] && is_numeric($_GET['userid'])) {
  login_as($_GET['userid'], $cfg['godmode']);
  $needslogin = false;
}

?>