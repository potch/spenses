<?php

try {

  require "./db.php";

  $REQUEST = get_request_data();

  $dbh = open_db();

  ////////////////////////////////////////////////////////////////////////////////
  // begin a transaction -- we'll commit only if everything works properly
  $dbh->beginTransaction();

  ////////////////////////////////////////////////////////////////////////////////
  // rudimentary error checking -- improve this section (or do it in javascript?)

  if (!array_keys_exist(array('date', 'cohortid', 'whopaid', 'location', 'desc', 'amount', 'iou'), $REQUEST))
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
    elseif ($iou['amount'] != "" && !is_numeric($iou['amount']))
      throw new Exception("Invalid iou amount ${iou["amount"]} for id ${iou["userid"]}");
  }

  // if ($debug) echo("<p>Updating the database:</p>");

  ////////////////////////////////////////////////////////////////////////////////
  // find the location specified by the user


  $locationId = 1;

  // Location is off for now, save all as the empty location.

  if (false) { //Remove this to re-enable locations!

    print_sql($sql = "SELECT count(*) FROM location WHERE name LIKE \"%${REQUEST["location"]}%\"");

    if (($res = $dbh->query($sql)) == false)
      throw new Exception("Could not select from location table");

    $numLocations = $res->fetchColumn();

    if ($numLocations > 1) {

      throw new Exception("Location matches $numLocations database entries -- we only support one match for now!");

    } elseif ($numLocations == 0) {

      // Need to add spell checking here, to avoid off-by-one-lettering!

      // if ($debug) echo "<p>Adding new location \"${REQUEST["location"]}\" to the database!</p>";

      print_sql($sql = "INSERT INTO location SET name=\"${REQUEST["location"]}\", date_created=NOW()");

      if (($nrows = $dbh->exec($sql)) != 1)
        throw new Exception("Inserted $nrows rows into location table, expected 1...");

      $locationId = $dbh->lastInsertId();
      // if ($debug) echo "<p>Added new location with id $locationId</p>";

    } else {
      print_sql($sql = "SELECT locationid FROM location WHERE name=\"${REQUEST["location"]}\"");

      $res = $dbh->query($sql, PDO::FETCH_ASSOC);
      $locationId = $res->fetchColumn();
      // if ($debug) echo "<p>Found location with id $locationId in the database</p>";

    }

  }

  ////////////////////////////////////////////////////////////////////////////////
  // update the purchase table

  $datestring = date("Y-m-d", $date);
  $useridEnterer = $_COOKIE['user']['userid'];

  print_sql($sql = "INSERT INTO purchase SET description=\"${REQUEST["desc"]}\", amount=${REQUEST["amount"]}, userid=$useridEnterer, userid_payer=${REQUEST["whopaid"]}, locationid=$locationId, date_of=\"$datestring\", date_created=NOW()");

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

    if (($iou['amount']) == "") continue;

    print_sql($sql = "INSERT INTO iou SET purchaseid=$purchaseId, cohortid=${REQUEST["cohortid"]}, userid_payer=${REQUEST["whopaid"]}, userid_payee=${iou["userid"]}, amount=${iou["amount"]}, date_updated=NOW()");

    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("Inserted $nrows rows into purchasedetail table, expected 1...");

    if ($REQUEST['whopaid'] < $iou['userid']) {
      print_sql($sql = "UPDATE balance SET amount=amount-${iou["amount"]} WHERE userid_from=${REQUEST["whopaid"]} AND userid_to=${iou["userid"]} AND cohortid=${REQUEST["cohortid"]}");
    } else {
      print_sql($sql = "UPDATE balance SET amount=amount+${iou["amount"]} WHERE userid_to=${REQUEST["whopaid"]} AND userid_from=${iou["userid"]} AND cohortid=${REQUEST["cohortid"]}");
    }

    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("Updated $nrows rows in balance table, expected 1...");

  }

  ////////////////////////////////////////////////////////////////////////////////
  // commit the transaction on success

  // if ($debug)   echo "<p>Everything was successful -- committing the transaction!</p>";
  $dbh->commit();

  echo json_response('success', null, null);

} catch (Exception $e) {
  ////////////////////////////////////////////////////////////////////////////////
  // roll back the transaction on any error

  echo json_response('error', $e->getMessage(), null);

  $dbh->rollBack();
}

?>
