<?php

try {

  require "./db.php";
  require "../lib/openid.php";

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

  $dbh = open_db();

  if (!isset($_COOKIE['openid']['userid']))
    throw new Exception('Did not obtain userid');

  if (($res = $dbh->query("SELECT * FROM user WHERE userid=${_COOKIE["openid"]["userid"]}", PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not query for user");

  $user = $res->fetch();

  if (!$user)
    throw new Exception("No user found");

  $expire = time() + 60 * 60 * 24;
  setcookie('user[name]'  , $user['name'],   $expire, '/');
  setcookie('user[userid]', $user['userid'], $expire, '/');
  setcookie('user[nick]'  , $user['nick'],   $expire, '/');
  setcookie('user[email]' , $user['email'],  $expire, '/');

  header('Location:' . '../index.php');

} catch (Exception $e) {
  header('Location:' . '../index.php?err=' . urlencode($e->getMessage()));
}

?>