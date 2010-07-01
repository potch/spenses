<?php

require "./db.php";

$dbh = open_db();

if (array_key_exists("uid",$_GET)) {
  $uid = $_GET['uid'];

  $user = $dbh->query("select * from user where id=$uid", PDO::FETCH_ASSOC)->fetch();

  if ($user) {
    $expire = time() + 60 * 60 * 24;
    setcookie('user[name]', $res['name'], $expire, '/');
    setcookie('user[id]',   $uid,  $expire, '/');

    // We have our cookie, return to the index
    header( 'Location:' . '../index.php');
  } else {
    header( 'Location:' . '../userset.php?err=bad_uid' );
  }
} else {
  header( 'Location:' . '../userset.php?err=no_uid' );
}

?>