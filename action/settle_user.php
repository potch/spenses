<?php

try {

  require "./db.php";

  if ($cfg['use_get']) $REQUEST = $_GET;
  else                 $REQUEST = $_POST;

  $dbh = open_db();

  $dbh->beginTransaction();

  if (!array_keys_exist($REQUEST, array('cohortid', 'userid1', 'userid2', 'desc')))
    throw new Exception("Insufficient \$REQUEST arguments");

  $datestring = date("Y-m-d");

  $cohortid = $REQUEST['cohortid'];
  $userid1 = $REQUEST['userid1'];
  $userid2 = $REQUEST['userid2'];

  $useridEnterer = $_COOKIE['user']['userid'];

  if ($userid1 < $userid2)
    print_sql($sql = "SELECT * FROM balance WHERE userid_from=$userid1 AND userid_to=$userid2 AND cohortid=$cohortid");
  elseif ($userid2 < $userid1)
    print_sql($sql = "SELECT * FROM balance WHERE userid_from=$userid1 AND userid_to=$userid2 AND cohortid=$cohortid");
  else
    throw new Exception('Cannot settle with yourself');

  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not select balance");

  $balance = $res->fetch();

  $amount = $balance['amount'];

  if ($amount == 0) {
    // do something here
  } elseif ($amount < 0) {
    print_sql($sql = "INSERT INTO purchase SET description=\"${REQUEST["desc"]}\", amount=$amount, userid=$useridEnterer, userid_payer=${balance["userid_from"]}, date_of=\"$datestring\", date_created=NOW()");
  } else {
    print_sql($sql = "INSERT INTO purchase SET description=\"${REQUEST["desc"]}\", amount=$amount, userid=$useridEnterer, userid_payer=${balance["userid_to"]}, date_of=\"$datestring\", date_created=NOW()");
  }
  $settle_payer =


  print_sql($sql = "INSERT INTO purchase SET description=\"${REQUEST["desc"]}\", amount=${REQUEST["amount"]}, userid=$useridEnterer, userid_payer=${REQUEST["whopaid"]}, locationid=$locationId, date_of=\"$datestring\", date_created=NOW()");

  if (($nrows = $dbh->exec($sql)) != 1)
    throw new Exception("Inserted $nrows rows into purchase table, expected 1...");

  $purchaseId = $dbh->lastInsertId();

  foreach ($REQUEST['iou'] as $iou) {

    if ($REQUEST['whopaid'] == $iou['userid']) continue;

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

  echo json_encode(array('status' => 'success', 'message' => null, 'data' => null));

} catch (Exception $e) {
  ////////////////////////////////////////////////////////////////////////////////
  // roll back the transaction on any error

  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));

  $dbh->rollBack();
}

?>
