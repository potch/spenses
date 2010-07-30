<?php

try {
  require "./db.php";

  if ($USE_GET) $REQUEST = $_GET;
  else          $REQUEST = $_POST;

  if (!array_key_exists('userid', $REQUEST))
    throw new Exception('Did not supply userid');
    
  echo json_encode(array('status' => 'success', 'message' => null, 'data' => get_cohort_list($REQUEST['userid'])));

} catch (Exception $e) {
  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));
}

?>
