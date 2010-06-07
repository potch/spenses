<?php
  if ($db = sqlite_open('../db/spenses.db', 0666, $sqliteerror)) {
    
  $sql = "SELECT * FROM user";
  
  $res = sqlite_query($db, $sql);
  $usr = sqlite_fetch_all($res, SQLITE_ASSOC);
  
  for ($i = 0; $i < count($usr); $i++)
    $usr[$i]['id'] = (int) $usr[$i]['id'];

  echo(json_encode($usr));
  }
?>
