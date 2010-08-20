<?php

$cfg = array();
require_once('config.php');

$debug_sql = array();

function print_sql($sql) {
  global $debug_sql;
  array_push($debug_sql, $sql);
}

function json_response($status, $message, $data) {
  global $cfg, $debug_sql;
  header('Content-type: application/json');
  $response = array('status' => $status, 'message' => $message, 'data' => $data);
  if ($cfg['print_sql']) $response['sql'] = $debug_sql;
  return json_encode($response);
}

function get_request_data() {
  global $cfg;

  $REQUEST = $_POST;

  if (!count($_POST) && $_COOKIE['godmode'] == $cfg['godmode'] && count($_GET))
    $REQUEST = $_GET;

  return $REQUEST;
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
