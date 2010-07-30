<?php

require "lib/openid.php";

$needslogin = false;
if (!isset($_COOKIE) || !isset($_COOKIE['user'])) $needslogin = true;    

?>