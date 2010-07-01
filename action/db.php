<?php

//print_r($cfg);

function open_db() {
  $cfg = array();
  require('db.inc.php');

  try {
    // Open a persistent connection to mysql database
    $dbh = new PDO("mysql:dbname=${cfg['database']};host=${cfg['hostname']}", $cfg['username'], $cfg['password'], array(PDO::ATTR_PERSISTENT => true));
  } catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
  }
  return $dbh;
}


function get_user_list($cohortid = null) {
  $dbh = open_db();

  $users = array();

  if (is_null($cohortid)) {
    if (($res = $dbh->query('select * from user', PDO::FETCH_ASSOC)) == false)
      die("Could not select from user table");

    foreach ($res as $row) {
      $row['id'] = (int) $row['id'];
      array_push($users, $row);
    }
  } else {
    if (($res = $dbh->query("SELECT user.id AS id, user.name AS name FROM cohortuser, user WHERE cohortuser.userid=user.id AND cohortuser.cohortid=$cohortid", PDO::FETCH_ASSOC)) == false)
      die("Could not join on cohortuser and user tables");

    foreach ($res as $row) {
      $row['id'] = (int) $row['id'];
      array_push($users, $row);
    }
  }
    
  return $users;
}

function user_dropdown($id) {
  $userlist = get_user_list();

  echo "<select name='whopaid' id='whopaid'>\n";
  foreach ($userlist as $user) {
    echo "<option " . ($user['id'] == $id ? "selected" : "") . " value='${user['id']}'>${user['name']}</option>\n";
  }
  echo "</select>\n";
}

?>
