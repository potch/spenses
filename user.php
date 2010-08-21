<?php

require "lib/openid.php";

require_once("action/db.php");

$needslogin = false;
if (!isset($_COOKIE) || !isset($_COOKIE['user'])) $needslogin = true;

if ($_GET['debug'] == $cfg['godmode'] && is_numeric($_GET['userid'])) {
  login_as($_GET['userid'], $cfg['godmode']);
  $needslogin = false;
}

?>