<?php

require "./db.php";

$dbh = open_db();

if (array_key_exists("uid",$_GET)) {
  $uid = $_GET['uid'];

  if (($res = $dbh->query("select * from user where userid=$uid", PDO::FETCH_ASSOC)) == false)
    die("Could not query database");

  $user = $res->fetch();

  if ($user) {
    $expire = time() + 60 * 60 * 24;
    setcookie('user[name]'  , $user['name'], $expire, '/');
    setcookie('user[userid]', $uid,          $expire, '/');

    // We have our cookie, return to the index
    header( 'Location:' . '../index.php');
  } else {
    header( 'Location:' . '../userset.php?err=bad_uid' );
  }
} else {
  header( 'Location:' . '../userset.php?err=no_uid' );
}

?>