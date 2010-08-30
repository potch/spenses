<?php

$cfg = array();

require_once('config.php');

if ($cfg['dynamic_cache'] == true) {

  $cache_timestamp_file = $cfg['docroot'] . '/cache.timestamp';

  $cached = json_decode(file_get_contents($cache_timestamp_file), true);
  $tocache = $cfg['tocache'];

  $updated = false;

  // Check to see that we have the same cache.manifest sections

  foreach (array_keys($tocache) as $key) {
    if (!array_key_exists($key, $cached)) {
      $updated = true; break;
    }
  }

  if (!$updated) {
    foreach (array_keys($cached) as $key) {
      if ($key != '_TIMESTAMP_' && !array_key_exists($key, $tocache)) {
        $updated = true; break;
      }
    }
  }

  if (!$updated) {
    foreach (array_keys($tocache) as $section) {

      // Look at each file to cache within the section

      foreach ($tocache[$section] as $file) {

        // If we don't have a last modified timestamp, update cache

        if (!array_key_exists($file, $cached[$section])) {
          $updated = true; break;
        }

        // If we have a last modified timestamp

        else {

          // If the file does not exist locally, update cache

          if (!file_exists($file)) {
            $updated = true; break;
          }

          // If the file modification time is different from the cached time, update cache

          elseif (filemtime($file) > $cached[$section][$file]) {
            $updated = true; break;
          }
        }
      }
    }
  }

  if ($updated) {
    foreach (array_keys($cached[$section]) as $file) {

      if (!in_array($file, $tocache[$section])) {
        $updated = true; break;
      }
    }
  }

  ////////////////////////////////////////

  $timestamp = $cached['_TIMESTAMP_'];

  if ($updated) {

    // Change the timestamp file

    $new_cached = array();

    foreach (array_keys($tocache) as $section) {

      $new_cached[$section] = array();

      foreach ($tocache[$section] as $file)
        $new_cached[$section][$file] = filemtime($file);

    }

    $timestamp = time();

    $new_cached['_TIMESTAMP_'] = $timestamp;

    file_put_contents($cache_timestamp_file, json_encode($new_cached));

  }

  $MANIFEST = "CACHE MANIFEST\n# timestamp $timestamp\n";

  foreach (array_keys($tocache) as $section) {

    $MANIFEST .= "$section:\n";

    foreach ($tocache[$section] as $file)
      $MANIFEST .= substr($file, strlen($cfg['docroot'])) . "\n";

  }

  file_put_contents($cfg['docroot'].'/cache.manifest', $MANIFEST);

  # echo $MANIFEST;

}

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

function login_as($userid, $godmode) {
  $dbh = open_db();

  if (($res = $dbh->query("SELECT * FROM user WHERE userid=$userid", PDO::FETCH_ASSOC)) == false)
    throw new Exception("Could not query for user");

  $user = $res->fetch();

  if (!$user)
    throw new Exception("No user found");

  $expire = time() + 60 * 60 * 24;
  setcookie('user[name]'  , $user['name'],   $expire, '/');
  setcookie('user[userid]', $user['userid'], $expire, '/');
  setcookie('user[nick]'  , $user['nick'],   $expire, '/');
  setcookie('user[email]' , $user['email'],  $expire, '/');

  if ($godmode != null)
    setcookie('godmode', $godmode, $expire, '/');
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
function array_keys_exist($keys, $array) {
  foreach($keys as $k) {
    if(!array_key_exists($k,$array)) {
      return false;
    }
  }
  return true;
}

?>
