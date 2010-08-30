<?php

try {

  require "./db.php";

  require $cfg['docroot'] . '/lib/openid.php';

  if (!isset($_GET['openid_mode']))
    throw new Exception('Bad request');

  if (!isset($_COOKIE) || !isset($_COOKIE['openid']))
    throw new Exception('Bad cookies');

  if ($_GET['openid_mode'] == 'cancel')
    throw new Exception('User canceled authentication');

  if ($_GET['openid_mode'] != 'id_res')
    throw new Exception('Bad openid_mode: ' . $_GET['openid_mode']);

  $openid = new LightOpenID;

  if (!$openid->validate())
    throw new Exception('OpenID did not validate properly');

  if (!isset($_COOKIE['openid']['userid']))
    throw new Exception('Did not obtain userid');

  $userid = $_COOKIE['openid']['userid'];

  login_as($userid, null);

  setcookie ("openid[userid]", "", time() - 3600, '/');
  setcookie ("openid[status]", "", time() - 3600, '/');
  unset($_COOKIE['openid']);

  header('Location:' . '../');

} catch (Exception $e) {
  header('Location:' . '../?err=' . urlencode($e->getMessage()));
}

?>