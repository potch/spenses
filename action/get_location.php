<?php

try {
  require "./db.php";

  if ($cfg['use_get']) $REQUEST = $_GET;
  else                 $REQUEST = $_POST;

  $dbh = open_db();

  if (!array_key_exists('name', $REQUEST))
    throw new Exception('Did not supply name');

  $sql = "SELECT * FROM location WHERE name LIKE '${REQUEST["name"]}%'"; if ($cvg['print_sql']) echo "<p>$sql</p>";
  
  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not select recent purchase ids");

  $locations = $res->fetchAll();
  
  if (!$locations)
    echo json_encode(array('status' => 'success', 'message' => 'No locations found', 'data' => null));
  else 
    echo json_encode(array('status' => 'success', 'message' => null, 'data' => $locations));
} catch (Exception $e) {
  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));
  }

?> 