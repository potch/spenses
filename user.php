<?php

require "lib/openid.php";

$needslogin = false;
if (!isset($_COOKIE) || !isset($_COOKIE['user'])) $needslogin = true;

$cfg = array();
require_once('action/config.php');

if (isset($_GET['skip_login_debug'])) {
  $expire = time() + 60 * 60 * 24;
  if ($_GET['skip_login_debug'] == 'Andrew') {
    setcookie('user[name]'  , "Andrew Pariser",    $expire, '/');
    setcookie('user[userid]', 1,                   $expire, '/');
    setcookie('user[nick]'  , "Andrew",            $expire, '/');
    setcookie('user[email]' , "pariser@gmail.com", $expire, '/');
    setcookie('godmode'     , $cfg['godmode'],     $expire, '/');
  } elseif ($_GET['skip_login_debug'] == 'Potch') {
    setcookie('user[name]'  , "Matt Claypotch",     $expire, '/');
    setcookie('user[userid]', 2,                    $expire, '/');
    setcookie('user[nick]'  , "Potch",              $expire, '/');
    setcookie('user[email]' , "thepotch@gmail.com", $expire, '/');
    setcookie('godmode'     , $cfg['godmode'],      $expire, '/');
  }
  $needslogin = false;
}

?>