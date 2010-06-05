<?php

  echo urlencode(json_encode(array('a' => 'name1', 'person' => 'i am testing')));
  //$inPOST = urlencode(serialize(array('a' => 'name1', 'person' => 'i am testing')));
  //echo $inPOST;
  
  //print_r(unserialize(urldecode($inPOST)));
  
?>