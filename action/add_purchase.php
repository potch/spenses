<?php

try {

  $debug    = false;
  $printSQL = false;

  require "./db.php";

  if ($USE_GET) $REQUEST = $_GET;
  else          $REQUEST = $_POST;

  $dbh = open_db();

  ////////////////////////////////////////////////////////////////////////////////
  // begin a transaction -- we'll commit only if everything works properly
  $dbh->beginTransaction();

  ////////////////////////////////////////////////////////////////////////////////
  // rudimentary error checking -- improve this section (or do it in javascript?)

  if (!array_keys_exist($REQUEST, array('date', 'cohortid', 'whopaid', 'location', 'desc', 'amount', 'iou')))
    throw new Exception("Insufficient \$REQUEST arguments");

  if (($date = strtotime($REQUEST["date"])) === false)
    throw new Exception("Invalid date ${REQUEST["date"]}");

  if (!empty($REQUEST["amount"]) && !is_numeric($REQUEST["amount"]))
    throw new Exception("Invalid amount ${REQUEST["amount"]}");
      
  if (!is_array($REQUEST['iou']) || count($REQUEST['iou']) == 0)
    throw new Exception("Invalid list of IOUs ${REQUEST["iou"]}");

  foreach ($REQUEST['iou'] as $iou) {
    if (!is_array($iou) || !array_keys_exist($iou, array('amount','userid')))
      throw new Exception("Invalid iou entry $iou");
    elseif (!is_numeric($iou['amount']))
      throw new Exception("Invalid iou amount ${iou["amount"]}");
  }
    
  if ($debug) echo("<p>Updating the database:</p>");
  
  ////////////////////////////////////////////////////////////////////////////////
  // find the location specified by the user

  $sql = "SELECT count(*) FROM location WHERE name LIKE \"%${REQUEST["location"]}%\""; if ($printSQL) echo "<p>$sql</p>";

  if (($res = $dbh->query($sql)) == false)
    throw new Exception("Could not select from location table");

  $numLocations = $res->fetchColumn();

  if ($numLocations > 1) {

    throw new Exception("Location matches $numLocations database entries -- we only support one match for now!");

  } elseif ($numLocations == 0) {

    // Need to add spell checking here, to avoid off-by-one-lettering!

    if ($debug) echo "<p>Adding new location \"${REQUEST["location"]}\" to the database!</p>";

    $sql = "INSERT INTO location SET name=\"${REQUEST["location"]}\", date_created=NOW()"; if ($printSQL) echo "<p>$sql</p>";

    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("Inserted $nrows rows into location table, expected 1...");

    $locationId = $dbh->lastInsertId();
    if ($debug) echo "<p>Added new location with id $locationId</p>";

  } else {
    $sql = "SELECT locationid FROM location WHERE name=\"${REQUEST["location"]}\""; if ($printSQL) echo "<p>$sql</p>";

    $res = $dbh->query($sql, PDO::FETCH_ASSOC);
    $locationId = $res->fetchColumn();
    if ($debug) echo "<p>Found location with id $locationId in the database</p>";

  }

  ////////////////////////////////////////////////////////////////////////////////
  // update the purchase table

  $datestring = date("Y-m-d", $date);
  $useridEnterer = $_COOKIE['user']['userid'];

  $sql = "INSERT INTO purchase SET description=\"${REQUEST["desc"]}\", amount=${REQUEST["amount"]}, userid=$useridEnterer, userid_payer=${REQUEST["whopaid"]}, locationid=$locationId, date_of=\"$datestring\", date_created=NOW()"; if ($printSQL) echo "<p>$sql</p>";
  
  if (($nrows = $dbh->exec($sql)) != 1)
    throw new Exception("Inserted $nrows rows into purchase table, expected 1...");
    
  $purchaseId = $dbh->lastInsertId();

  ////////////////////////////////////////////////////////////////////////////////
  // update the iou     table
  // update the balance table

  // ****** PICK UP FROM HERE

  foreach ($REQUEST['iou'] as $iou) {

    // We don't store self-ious
    if ($REQUEST['whopaid'] == $iou['userid']) continue;

    $sql = "INSERT INTO iou SET purchaseid=$purchaseId, cohortid=${REQUEST["cohortid"]}, userid_payer=${REQUEST["whopaid"]}, userid_payee=${iou["userid"]}, amount=${iou["amount"]}, date_updated=NOW()"; if ($printSQL) echo "<p>$sql</p>";

    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("Inserted $nrows rows into purchasedetail table, expected 1...");

    if ($REQUEST['whopaid'] < $iou['userid']) {
      $sql = "UPDATE balance SET amount=amount-${iou["amount"]} WHERE userid_from=${REQUEST["whopaid"]} AND userid_to=${iou["userid"]} AND cohortid=${REQUEST["cohortid"]}";
    } else {
      $sql = "UPDATE balance SET amount=amount+${iou["amount"]} WHERE userid_to=${REQUEST["whopaid"]} AND userid_from=${iou["userid"]} AND cohortid=${REQUEST["cohortid"]}";
    }

    if ($printSQL) echo "<p>$sql</p>";
      
    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("Updated $nrows rows in balance table, expected 1...");

  }  

  ////////////////////////////////////////////////////////////////////////////////
  // commit the transaction on success

  if ($debug)   echo "<p>Everything was successful -- committing the transaction!</p>";
  if ($nowrite) throw new Exception("The 'nowrite' flag was set -- will not commit the transaction!");
  $dbh->commit();

  echo json_encode(array('result' => 'success', 'message' => null, 'data' => null));

} catch (Exception $e) {
  ////////////////////////////////////////////////////////////////////////////////
  // roll back the transaction on any error

  echo json_encode(array('result' => 'error', 'message' => $e->getMessage(), 'data' => null));

  $dbh->rollBack();
}

?>
