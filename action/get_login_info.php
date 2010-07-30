<?php

try {
  require "./db.php";

  $dbh = open_db();

  if ($USE_GET) $REQUEST = $_GET;
  else          $REQUEST = $_POST;

  if (!array_key_exists('email', $REQUEST))
    throw new Exception('No email provided');

  $sql = "SELECT openid, userid FROM user WHERE email='${REQUEST["email"]}'"; if ($printSQL) echo "<p>$sql</p>";

  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not select from userid table");

  $data = $res->fetch();

  $data["userid"] = (int) $data["userid"];

  echo json_encode(array('status' => 'success', 'message' => null, 'data' => $data));

} catch (Exception $e) {
  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));
}

?>
