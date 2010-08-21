<?php

try {
  require "./db.php";

  $REQUEST = get_request_data();

  $dbh = open_db();

  $dbh->beginTransaction();

  $valueString = "";
  if (!array_key_exists('locationid', $REQUEST))
    throw new Exception("You did not supply the location id");

  print_sql($sql = "SELECT count(*) FROM location WHERE locationid=${REQUEST["locationid"]}");

  if (($res = $dbh->query($sql)) == false)
    throw new Exception("Could not connect to location table");

  if (($nrows = $res->fetchColumn()) != 1)
    throw new Exception("Found $nrows locations with locationid ${REQUEST["locationid"]}, expecting 1...");

  if (array_key_exists('lat',  $REQUEST)) $valueString .= "lat=${REQUEST["lat"]} ";
  if (array_key_exists('lon',  $REQUEST)) $valueString .= "lon=${REQUEST["lon"]} ";
  if (array_key_exists('addr', $REQUEST)) $valueString .= "addr=\"${REQUEST["addr"]}\" ";

  if ($valueString == "")
    throw new Exception("No values were supplied to update the database");

  print_sql($sql = "UPDATE location SET $valueString WHERE locationid=${REQUEST["locationid"]}");

  if (($nrows = $dbh->exec($sql)) > 1)
    throw new Exception("Updated $nrows locations; expected 0 or 1...");
  elseif ($nrows == 0)
    throw new Exception("Updated $nrows locations; the supplied data were already in the database");

  $dbh->commit();

  echo json_response('success', null, null);

} catch (Exception $e) {
  if ($dbh)
    $dbh->rollBack();

  echo json_response('error', $e->getMessage(), null);
}

?>