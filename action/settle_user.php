<?php

try {

  require "./db.php";

  $REQUEST = get_request_data();

  $dbh = open_db();

  $dbh->beginTransaction();

  if (!array_keys_exist($REQUEST, array('cohortid', 'userid1', 'userid2', 'desc')))
    throw new Exception("Insufficient \$REQUEST arguments");

  $datestring = date("Y-m-d");

  $cohortid = $REQUEST['cohortid'];

  $userid1 = min($REQUEST['userid1'], $REQUEST['userid2']);
  $userid2 = max($REQUEST['userid1'], $REQUEST['userid2']);

  if ($userid1 == $userid2)
    throw new Exception('Cannot settle with yourself');

  $userid_enterer = $_COOKIE['user']['userid'];

  print_sql($sql = "SELECT * FROM balance WHERE userid_from=$userid1 AND userid_to=$userid2 AND cohortid=$cohortid");

  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not select balance");

  $balance = $res->fetch();

  $amount = $balance['amount'];

  if ($amount < 0.0) {
    $userid_payer = $balance['userid_from'];
    $userid_payee = $balance['userid_to'];
  } elseif ($amount > 0.0) {
    $userid_payer = $balance['userid_to'];
    $userid_payee = $balance['userid_from'];
  } else
    throw new Exception("No balance to settle");

  $amount = abs($amount);

  print_sql($balance);

  print_sql($sql = "INSERT INTO purchase SET description=\"${REQUEST["desc"]}\", amount=$amount, userid=$userid_enterer, userid_payer=$userid_payer, date_of=\"$datestring\", date_created=NOW()");

  if (($nrows = $dbh->exec($sql)) != 1)
    throw new Exception("Inserted $nrows rows into purchase table, expected 1...");

  $purchaseId = $dbh->lastInsertId();

  print_sql($sql = "INSERT INTO iou SET purchaseid=$purchaseId, cohortid=$cohortid, userid_payer=$userid_payer, userid_payee=$userid_payee, amount=$amount, date_updated=NOW()");

  if (($nrows = $dbh->exec($sql)) != 1)
    throw new Exception("Inserted $nrows rows into purchasedetail table, expected 1...");

  print_sql($sql = "UPDATE balance SET amount=0.00 WHERE userid_from=$userid1 AND userid_to=$userid2 AND cohortid=$cohortid");

  if (($nrows = $dbh->exec($sql)) != 1)
    throw new Exception("Updated $nrows rows in balance table, expected 1...");

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
