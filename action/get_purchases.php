<?php

try {
  require "./db.php";

  $REQUEST = get_request_data();

  $dbh = open_db();

  if (!array_key_exists('cohortid', $REQUEST) || !array_key_exists('userid', $REQUEST))
    throw new Exception('Did not supply cohortid and userid');

  if (array_key_exists('number', $REQUEST)) {
    $number = $REQUEST['number'];
  } else {
    $number = 20;
  }

   print_sql($sql = "SELECT purchaseid FROM iou WHERE cohortid=${REQUEST["cohortid"]} AND (userid_payer=${REQUEST["userid"]} OR userid_payee=${REQUEST["userid"]}) GROUP BY purchaseid ORDER BY date_updated DESC LIMIT $number");

  if (($res = $dbh->query($sql, PDO::FETCH_COLUMN, 0)) == false)
      throw new Exception("Could not select recent purchase ids");

  $purchaseids = $res->fetchAll();

  $purchasedata = array();

  if (count($purchaseids) > 0) {

    $pid_set = "(".join(",", $purchaseids).")";

    $sql = <<<EOF
SELECT
 purchase.purchaseid,
 purchase.description,
 purchase.amount AS purchase_amount,
 purchase.date_updated AS date_updated,
 purchase.date_created AS date_created,
 purchase.date_of AS date_of,
 is_settle,
 purchase.userid AS creator_userid,
 creator.nick AS creator_nick,
 purchase.userid_payer AS payer_userid,
 payer.nick AS payer_nick,
 payee.userid AS payee_userid,
 payee.nick AS payee_nick,
 iou.amount AS iou_amount
FROM purchase
LEFT JOIN user AS creator ON creator.userid=purchase.userid
LEFT JOIN user AS payer ON payer.userid=userid_payer
LEFT JOIN iou USING(purchaseid)
LEFT JOIN user AS payee ON payee.userid=userid_payee
WHERE purchaseid IN $pid_set
EOF;

    print_sql($sql);

    if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
      throw new Exception("Could not select purchase data");

    $all_iou = $res->fetchAll();

    foreach ($all_iou as $iou) {

      if (!array_key_exists((int)$iou['purchaseid'], $purchasedata)) {

        $purchasedata[(int)$iou['purchaseid']]
          = array('purchaseid'   => (int)$iou['purchaseid'],
                  'description'  => $iou['description'],
                  'amount'       => (float)$iou['purchase_amount'],
                  'date_created' => $iou['date_created'],
                  'date_updated' => $iou['date_updated'],
                  'date_of'      => $iou['date_of'],
                  'is_settle'    => (bool)$iou['is_settle'],
                  'creator'      => array('userid' => (int)$iou['creator_userid'],
                                          'nick'   => $iou['creator_nick']),
                  'payer'        => array('userid' => (int)$iou['payer_userid'],
                                          'nick'   => $iou['payer_nick']),
                  'payees'       => array());

      }

      array_push($purchasedata[(int)$iou['purchaseid']]['payees'],
                 array('userid' => (int)$iou['payee_userid'],
                       'nick'   => $iou['payee_nick'],
                       'amount' => $iou['iou_amount']));

    }

    $ordered = array();

    foreach ($purchaseids as $purchaseid)
      array_push($ordered, $purchasedata[(int)$purchaseid]);

    $purchasedata = $ordered;

  }

  echo json_response('success', null, $purchasedata);

} catch (Exception $e) {

  echo json_response('error', $e->getMessage(), null);

}

?>
