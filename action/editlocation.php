<?php

$printSQL = true;

require "./db.php";

$dbh = open_db();

$dbh->beginTransaction();

try {
  $valueString = "";
  if (!array_key_exists('id', $_GET))
    throw new Exception("<p>You did not supply the location id</p>");

  if (($res = $dbh->query("SELECT count(*) FROM location WHERE id=${_GET["id"]}")) == false)
    throw new Exception("<p>Could not connect to location table</p>");

  if (($nrows = $res->fetchColumn()) != 1)
    throw new Exception("<p>Found $nrows locations with id ${_GET["id"]}, expecting 1...</p>");
    
  if (array_key_exists('lat',  $_GET)) $valueString .=  "lat=${_GET["lat"]} ";
  if (array_key_exists('lon',  $_GET)) $valueString .=  "lon=${_GET["lon"]} ";
  if (array_key_exists('addr', $_GET)) $valueString .= "addr=\"${_GET["addr"]}\" ";

  if ($valueString == "")
    throw new Exception("<p>No values were supplied to update the database</p>");

  $sql = "UPDATE location SET $valueString WHERE id=${_GET["id"]}"; if ($printSQL) echo "<p>$sql</p>";

  if (($nrows = $dbh->exec($sql)) > 1)
    throw new Exception("<p>Updated $nrows locations; expected 0 or 1...</p>");
  elseif ($nrows == 0)
    throw new Exception("<p>Updated $nrows locations; the supplied data were already in the database</p>");

  echo "<p>Committing changes!</p>";
  $dbh->commit();

} catch (Exception $e) {
  echo "<p>Rolling back database changes due to exception: ",  $e->getMessage(), "</p>";
  $dbh->rollBack();
}

?>