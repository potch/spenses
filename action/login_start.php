<?php

try {
  require "./db.php";
  require "../lib/openid.php";

  $dbh = open_db();

  if ($cfg['use_get']) $REQUEST = $_GET;
  else                 $REQUEST = $_POST;

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
  
  echo json_encode(array('status' => 'success', 'message' => null, 'data' => $openid->authUrl()));

} catch (Exception $e) {
  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));
}

?>
