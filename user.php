<?php

require "lib/openid.php";

$needslogin = false;
if (!isset($_COOKIE) || !isset($_COOKIE['user'])) $needslogin = true;    

if ($needslogin && isset($_GET['openid_mode'])) {

    if ($_GET['openid_mode'] == 'cancel') {
    } else {
      $openid = new LightOpenID;
      $valid = $openid->validate();

      if ($valid)
	header('Location: action/dologin.php');
    }
}

?>