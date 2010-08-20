<?php

try {
  require "./db.php";

  $REQUEST = get_request_data();

  if (!array_key_exists('userid', $REQUEST))
    throw new Exception('Did not supply userid');

  echo json_response('success', null, get_cohort_list($REQUEST['userid']));

} catch (Exception $e) {
  echo json_response('error', $e->getMessage(), null);
}

?>
