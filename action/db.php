<?php

$cfg = array();
require_once('config.php');

function print_sql($sql) {
  global $cfg;
  if ($cfg['print_sql']) {
    echo "<script>console.log(".$sql.");</script>";
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

  if (($res = $dbh->query("select * from cohortuser natural join user where cohortid=$cohortid", PDO::FETCH_ASSOC)) == false)
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

  if (($res = $dbh->query("select * from cohortuser natural join cohort where userid=1;", PDO::FETCH_ASSOC)) == false)
    die("Could not query database");

  foreach ($res as $row) {
    $row['userid']   = (int) $row['userid'];
    $row['cohortid'] = (int) $row['cohortid'];
    array_push($cohorts, $row);
  }

  return $cohorts;
}

function user_dropdown($cohortid) {
  $userlist = get_user_list($cohort);

  echo "<select name='whopaid' id='whopaid'>\n";
  foreach ($userlist as $user) {
    echo "<option " . ($user['userid'] == $userid ? "selected" : "") . " value='${user['userid']}'>${user['name']}</option>\n";
  }
  echo "</select>\n";
}

function cohort_dropdown($userid) {
  $cohortlist = get_cohort_list($userid);

  echo "<select name='cohort' id='cohort'>\n";
  foreach ($cohortlist as $cohort) {
    echo "<option " . (isset($_COOKIE['user']) && isset($_COOKIE['user']['last_cohort_id']) && $_COOKIE['user']['last_cohort_id'] == $cohort['id'] ? "selected" : "") . " value='${cohort['cohortid']}'>${cohort['name']}</option>\n";
  }
  echo "</select>\n";
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
