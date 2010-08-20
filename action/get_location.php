<?php

try {
  require "./db.php";

  $REQUEST = get_request_data();

  $dbh = open_db();

  if (!array_key_exists('name', $REQUEST))
    throw new Exception('Did not supply name');

  print_sql($sql = "SELECT * FROM location WHERE name LIKE '${REQUEST["name"]}%'");

  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not select recent purchase ids");

  $locations = $res->fetchAll();

  if (!$locations)
    echo json_encode(array('status' => 'success', 'message' => 'No locations found', 'data' => null));
  else
    echo json_response('success', null, $locations);
} catch (Exception $e) {
  echo json_response('error', $e->getMessage(), null);
}

?>