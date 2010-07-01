<?php

$debug    = true;
$printSQL = true;

require "./db.php";

$dbh = open_db();

////////////////////////////////////////////////////////////////////////////////
// begin a transaction -- we'll commit only if everything works properly
$dbh->beginTransaction();

try {

  if (!array_key_exists("date",     $_POST) || !array_key_exists("whopaid",  $_POST) ||
      !array_key_exists("location", $_POST) || !array_key_exists("amount",   $_POST) ||
      !array_key_exists("amountA",  $_POST) || !array_key_exists("amountB",  $_POST) ||
      !array_key_exists("amountN",  $_POST) || !array_key_exists("amountP",  $_POST) ||
      !array_key_exists("desc",     $_POST)) {
    throw new Exception("<p>Insufficient _POST arguments</p>");
  }

  ////////////////////////////////////////////////////////////////////////////////
  // rudimentary error checking -- improve this section (or do it in javascript?)

  if (($date = strtotime($_POST["date"])) === false)
    throw new Exception("<p>Incorrect date format -- ${_POST["date"]}</p>");

  if (!empty($_POST["amount"]) && !is_numeric($_POST["amount"]))
    throw new Exception("<p>Invalid amount ${_POST["amount"]}-- expected a number!</p>");
      
  $paysForSomeone = false;
  foreach (array("amountA","amountB","amountN","amountP") as $postKey)
    if (!empty($_POST[$postKey])) {
      if (!is_numeric($_POST[$postKey]))
	throw new Exception("<p>Invalid $postKey -- expected a number!</p>");
      else
	$paysForSomeone = true;
    }

  if (!$paysForSomeone)
    throw new Exception("<p>You need to pay for at least one person</p>");
  
  if ($debug) echo("<p>Updating the database:</p>");
  
  ////////////////////////////////////////////////////////////////////////////////
  // find the location specified by the user

  $sql = "SELECT count(*) FROM location WHERE name LIKE \"%${_POST["location"]}%\""; if ($printSQL) echo "<p>$sql</p>";

  if (($res = $dbh->query($sql)) == false)
    throw new Exception("<p>Could not select from location table</p>");

  $numLocations = $res->fetchColumn();

  if ($numLocations > 1) {

    throw new Exception("<p>Location matches $numLocations database entries -- we only support one match for now!</p>");

  } elseif ($numLocations == 0) {

    if ($debug) echo "<p>Adding new location \"${_POST["location"]}\" to the database!</p>";

    $sql = "INSERT INTO location VALUES (null, \"${_POST["location"]}\",null,null,null)"; if ($printSQL) echo "<p>$sql</p>";

    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("<p>Inserted $nrows rows into location table, expected 1...</p>");

    $locationId = $dbh->lastInsertId();
    if ($debug) echo "<p>Added new location with id $locationId</p>";

  } else {
    $sql = "SELECT id FROM location WHERE name=\"${_POST["location"]}\""; if ($printSQL) echo "<p>$sql</p>";

    $res = $dbh->query($sql, PDO::FETCH_ASSOC);
    $locationId = $res->fetchColumn();
    if ($debug) echo "<p>Found location with id $locationId in the database</p>";

  }

  ////////////////////////////////////////////////////////////////////////////////
  // update the purchase table

  $datestring = date("Y-m-d", $date);
  $payerID = $_POST["whopaid"];

  $sql = "INSERT INTO purchase VALUES (null, \"${_POST["desc"]}\", ${_POST["amount"]}, $payerID, $locationId, \"$datestring\")"; if ($printSQL) echo "<p>$sql</p>";
  
  if (($nrows = $dbh->exec($sql)) != 1)
    throw new Exception("<p>Inserted $nrows rows into purchase table, expected 1...</p>");
    
  $purchaseID = $dbh->lastInsertId();

  ////////////////////////////////////////////////////////////////////////////////
  // update the purchasedetail table
  // update the balance        table

  foreach (array(1 => "amountA", 2 => "amountP", 3 => "amountN", 4 => "amountB",) as $recipID => $postKey) {
    
    if ($recipID == $payerID || empty($_POST[$postKey])) continue;
    
    $sql = "INSERT INTO purchasedetail VALUES ($purchaseID, $recipID, ${_POST[$postKey]})"; if ($printSQL) echo "<p>$sql</p>";

    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("<p>Inserted $nrows rows into purchasedetail table, expected 1...</p>");

    if ($payerID > $recipID) { $payerStatus = "idfrom"; $recipStatus = "idto";   $addToBal = false; }
    else                     { $payerStatus = "idto"  ; $recipStatus = "idfrom"; $addToBal = true;  }
    
    $sql = "SELECT amount FROM balance WHERE $payerStatus=$payerID AND $recipStatus=$recipID"; if ($printSQL) echo "<p>$sql</p>";
    $bal = $dbh->query($sql)->fetchColumn();
    
    if ($debug) echo "<p>The balance $payerStatus=$payerID $recipStatus=$recipID was $bal before changes to the database</p>";
    
    if ($addToBal) $bal += $_POST[$postKey];
    else           $bal -= $_POST[$postKey];
    
    if ($debug) echo "<p>The balance $payerStatus=$payerID $recipStatus=$recipID is $bal after changes to the database</p>";
    
    $sql = "UPDATE balance SET amount=$bal WHERE $payerStatus=$payerID AND $recipStatus=$recipID"; if ($printSQL) echo "<p>$sql</p>";

    if (($nrows = $dbh->exec($sql)) != 1)
      throw new Exception("<p>Updated $nrows rows in balance table, expected 1...</p>");
  }  

  ////////////////////////////////////////////////////////////////////////////////
  // commit the transaction on success

  if ($debug) echo "<p>Everything was successful -- committing the transaction!</p>";
  $dbh->commit();

} catch (Exception $e) {
  ////////////////////////////////////////////////////////////////////////////////
  // roll back the transaction on any error

  echo "<p>Rolling back database changes due to exception: ",  $e->getMessage(), "</p>";
  $dbh->rollBack();
}

?>
