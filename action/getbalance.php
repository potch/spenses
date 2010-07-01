<?php

require "./db.php";

if (isset($_COOKIE) && isset($_COOKIE['user'])) {

  $dbh = open_db();
    
  $uid = $_COOKIE['user']['id'];
  
  $sql = "SELECT bal.amount AS amount, bal.idfrom AS idfrom, bal.idto AS idto, u1.name AS namefrom, u2.name AS nameto ".
         "FROM balance AS bal, user AS u1, user AS u2 ".
         "WHERE (bal.idfrom = $uid OR bal.idto = $uid) AND bal.amount <> 0 AND bal.idfrom = u1.id AND bal.idto = u2.id";
  
  $return = array('owe'  => array(),
                  'owed' => array());

  foreach ($dbh->query($sql, PDO::FETCH_ASSOC) as $element) {
    if ($element['idto'] == $uid)
      if ($element['amount'] > 0)
        $return['owed'][] = array('name' => $element['namefrom'], 'id' => (int)$element['idfrom'], 'amount' => abs($element['amount']));
      else
        $return['owe'][]  = array('name' => $element['namefrom'], 'id' => (int)$element['idfrom'], 'amount' => abs($element['amount']));
    else
      if ($element['amount'] > 0)
        $return['owe'][]  = array('name' => $element['nameto'], 'id' => (int)$element['idto'], 'amount' => abs($element['amount']));
      else        
        $return['owed'][] = array('name' => $element['nameto'], 'id' => (int)$element['idto'], 'amount' => abs($element['amount']));
  }

  echo(json_encode($return));
}
?>
