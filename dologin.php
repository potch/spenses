<?php
  if (!($db = sqlite_open('spenses.db', 0666, $sqliteerror)))
    die($sqliteerror); 
  
  if (array_key_exists("uid",$_GET)) {
    $uid = $_GET['uid'];
    $sql = "SELECT * FROM user WHERE id=$uid"; if ($printSQL) echo "<p>$sql</p>";
    $res = sqlite_array_query($db, $sql);
      
    if (count($res) == 1) {
      $expire = time() + 60 * 60 * 24;
      setcookie('user[name]', $res[0]['name'], $expire);
      setcookie('user[id]',   $uid,  $expire);
      
      // We have our cookie, return to the index
      header( 'Location:' . 'index.php' ) ;
    } else {
      $expire = time() - 3600;
      setcookie('user[name]', '', $expire);
      setcookie('user[id]',   '', $expire);

      header( 'Location:' . 'userset.php' ) ;
    }
  } else {
    header( 'Location:' . 'userset.php' ) ;
  }
?>