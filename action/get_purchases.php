<?php

try {
  require "./db.php";

  if ($cfg['use_get']) $REQUEST = $_GET;
  else                 $REQUEST = $_POST;

  $dbh = open_db();

  if (!array_key_exists('cohortid', $REQUEST) || !array_key_exists('userid', $REQUEST))
    throw new Exception('Did not supply cohortid and userid');

  if (array_key_exists('number', $REQUEST)) {
    $number = $REQUEST['number'];
  } else {
    $number = 20;
  }

  $sql = "SELECT DISTINCT purchaseid FROM iou WHERE cohortid=${REQUEST["cohortid"]} AND (userid_payer=${REQUEST["userid"]} OR userid_payee=${REQUEST["userid"]}) ORDER BY date_updated DESC LIMIT $number"; if ($cfg['print_sql']) echo "<p>$sql</p>";

  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
      throw new Exception("Could not select recent purchase ids");

  $purchaseids = $res->fetchAll();

  $allpurchasedata = array();

  foreach ($purchaseids as $row) {

    $sql = "SELECT purchase.*, payer.name AS payer_name, payer.nick AS payer_nick, location.name AS location_name FROM purchase LEFT JOIN user AS payer ON userid_payer=payer.userid LEFT JOIN location USING(locationid) WHERE purchaseid=${row["purchaseid"]}"; if ($cfg['print_sql']) echo "<p>$sql</p>";
    
    if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
      throw new Exception("Could not select purchaseid $purchaseid");

    $purchasedata = $res->fetch();

    array_push($allpurchasedata, $purchasedata);

  }

  echo json_encode(array('status' => 'success', 'message' => null, 'data' => $purchasedata));

} catch (Exception $e) {

  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));

}

?>
