<?php

try {
  require "./db.php";

  $REQUEST = get_request_data();

  if (!array_key_exists('cohortid', $REQUEST))
    throw new Exception('Did not supply cohortid');

  echo json_response('success', null, get_user_list($REQUEST['cohortid']));

} catch (Exception $e) {
  echo json_response('error', $e->getMessage(), null);
}

?>
