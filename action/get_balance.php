<?php

try {
  require "./db.php";

  if ($cfg['use_get']) $REQUEST = $_GET;
  else                 $REQUEST = $_POST;

  $uid = null;

  if (array_key_exists('userid', $REQUEST))
    $uid = $REQUEST['userid'];
  elseif (isset($_COOKIE) && isset($_COOKIE['user']))
    $uid = $_COOKIE['user']['id'];
  else
    throw new Exception('Must supply userid');

  $dbh = open_db();

  $cohortstring = '';
  if (array_key_exists('cohortid', $REQUEST))
    $cohortstring = "AND cohortid=${REQUEST["cohortid"]}";

  print_sql($sql = "SELECT balance.*, cohort.name AS cohort_name, ufrom.name AS from_name, ufrom.nick AS from_nick, uto.name AS to_name, uto.nick AS to_nick FROM balance LEFT JOIN user AS ufrom ON userid_from=ufrom.userid LEFT JOIN user AS uto ON userid_to=uto.userid LEFT JOIN cohort USING(cohortid) WHERE (userid_from=$uid or userid_to=$uid) $cohortstring");

  if (($res = $dbh->query($sql, PDO::FETCH_ASSOC)) == false)
    throw new Exception('Error selecting from balance table:' . implode($dbh->errorInfo(), ' '));

  $data = array();

  foreach ($res as $row) {
    array_push($data, $row);
  }
  
  echo json_encode(array('status' => 'success', 'message' => null, 'data' => $data));
} catch (Exception $e) {
  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));
  }
?>
