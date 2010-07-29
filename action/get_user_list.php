<?php

try {
  require "./db.php";

  if ($USE_GET) $REQUEST = $_GET;
  else          $REQUEST = $_POST;

  if (!array_key_exists('cohortid', $REQUEST))
    throw new Exception('Did not supply cohortid');
    
  echo json_encode(array('status' => 'success', 'message' => null, 'data' => get_user_list($REQUEST['cohortid'])));

} catch (Exception $e) {
  echo json_encode(array('status' => 'error', 'message' => $e->getMessage(), 'data' => null));
}

?>
