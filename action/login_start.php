<?php

try {
  require "./db.php";
  require "../lib/openid.php";

  $dbh = open_db();

  $REQUEST = get_request_data();

  if (!array_key_exists('email', $REQUEST))
    throw new Exception('No email provided');

  $sql = "SELECT openid, userid FROM user WHERE email='${REQUEST["email"]}'";

  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not select from userid table");

  $data = $res->fetch();

  $data["userid"] = (int) $data["userid"];

  $openid = new LightOpenID;

  $openid->identity = $data["openid"];
  $openid->realm = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
  $openid->returnUrl = $openid->realm . $cfg['docroot'] . '/action/login_finish.php';

  $expire = time() + 60 * 5 * 1;
  setcookie('openid[userid]', $data["userid"], $expire, '/');
  setcookie('openid[status]', 'outbound',      $expire, '/');

  echo json_response('success', null, $openid->authUrl());

} catch (Exception $e) {
  echo json_response('error', $e->getMessage(), null);
}

?>
