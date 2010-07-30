<?php

try {
  require "./db.php";

  $dbh = open_db();

  if ($cfg['use_get']) $REQUEST = $_GET;
  else                 $REQUEST = $_POST;

  $dbh->beginTransaction();

  $valueString = "";
  if (!array_key_exists('id', $REQUEST))
    throw new Exception("You did not supply the location id");

  if (($res = $dbh->query("SELECT count(*) FROM location WHERE id=${_GET["id"]}")) == false)
    throw new Exception("Could not connect to location table");

  if (($nrows = $res->fetchColumn()) != 1)
    throw new Exception("Found $nrows locations with id ${_GET["id"]}, expecting 1...");
    
  if (array_key_exists('lat',  $REQUEST)) $valueString .= "lat=${_GET["lat"]} ";
  if (array_key_exists('lon',  $REQUEST)) $valueString .= "lon=${_GET["lon"]} ";
  if (array_key_exists('addr', $REQUEST)) $valueString .= "addr=\"${_GET["addr"]}\" ";

  if ($valueString == "")
    throw new Exception("No values were supplied to update the database");

  print_sql($sql = "UPDATE location SET $valueString WHERE id=${_GET["id"]}");

  if (($nrows = $dbh->exec($sql)) > 1)
    throw new Exception("Updated $nrows locations; expected 0 or 1...");
  elseif ($nrows == 0)
    throw new Exception("Updated $nrows locations; the supplied data were already in the database");

  $dbh->commit();

  echo json_encode(array('status' => 'success', 'message' => null, 'data' => null));

} catch (Exception $e) {
  if ($dbh)
    $dbh->rollBack();

  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));
}

?>