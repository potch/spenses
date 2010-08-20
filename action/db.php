<?php

$cfg = array();
require_once('config.php');

function print_sql($sql) {
  global $cfg;
  if ($cfg['print_sql']) {
    echo "console.log('".addslashes($sql)."');";
  }
}

function open_db() {
  global $cfg;
  try {
    // Open a persistent connection to mysql database
    $dbh = new PDO("mysql:dbname=${cfg['database']};host=${cfg['hostname']}", $cfg['username'], $cfg['password'], array(PDO::ATTR_PERSISTENT => true));
  } catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
  }
  return $dbh;
}


function get_user_list($cohortid) {
  $dbh = open_db();

  $users = array();

  if (($res = $dbh->query("SELECT * FROM cohortuser NATURAL JOIN user WHERE cohortid=$cohortid", PDO::FETCH_ASSOC)) == false)
    die("Could not query database");

  foreach ($res as $row) {
    $row['userid']   = (int) $row['userid'];
    $row['cohortid'] = (int) $row['cohortid'];
    array_push($users, $row);
  }

  return $users;
}

function get_cohort_list($userid) {
  $dbh = open_db();

  $cohorts = array();

  if (($res = $dbh->query("SELECT * FROM cohortuser NATURAL JOIN cohort WHERE userid=$userid;", PDO::FETCH_ASSOC)) == false)
    die("Could not query database");

  foreach ($res as $row) {
    $row['userid']   = (int) $row['userid'];
    $row['cohortid'] = (int) $row['cohortid'];
    array_push($cohorts, $row);
  }

  return $cohorts;
}

// function to verify existence of multiple array keys (compare to array_key_exists(...))
function array_keys_exist($array, $keys) {
  foreach($keys as $k) {
    if(!isset($array[$k])) {
      return false;
    }
  }
  return true;
}

?>
