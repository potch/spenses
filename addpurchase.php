<?php
  
  // Exception code 1 = No Post Data
  // Exception code 2 = Invalid Input
  // Exception code 3 = Database Connection Error
  // Exception code 4 = Database Logic Error
  
  $printSQL = 0;
  
    if (!($db = sqlite_open('spenses.db', 0666, $sqliteerror)))
      throw new Exception($sqliteerror, 3);
    
    $sql   = "SELECT * FROM user"; if ($printSQL) echo "<p>$sql</p>";
    $res   = sqlite_query($db, $sql);
    $users = sqlite_fetch_all($res, SQLITE_ASSOC);
    
    if (!array_key_exists("date",     $_POST) || !array_key_exists("whopaid",  $_POST) ||
        !array_key_exists("location", $_POST) || !array_key_exists("amount",   $_POST) ||
        !array_key_exists("amountA",  $_POST) || !array_key_exists("amountB",  $_POST) ||
        !array_key_exists("amountN",  $_POST) || !array_key_exists("amountP",  $_POST) ||
        !array_key_exists("desc",     $_POST))
      throw new Exception("No Post Data", 1);
      
    $errorString = "";

    if (($date = strtotime($_POST["date"])) === false)
      $errorString .= "<p>Incorrect Date Format</p>";

    if (!empty($_POST["amount"]) && !is_numeric($_POST["amount"]))
      $errorString .= "<p>Invalid amount -- expected a number!</p>";
      
    $paysForSomeone = false;
    foreach (array("amountA","amountB","amountN","amountP") as $postKey)
      if (!empty($_POST[$postKey])) {
        if (!is_numeric($_POST[$postKey]))
          $errorString .= "<p>Invalid $postKey -- expected a number!</p>";
        else
          $paysForSomeone = true;
      }

    if (!$paysForSomeone)
      $errorString .= "<p>You need to pay for at least one person</p>";
      
    if (!empty($errorString))
      throw new Exception($errorString);
            
    echo("<p>Updating the database:</p>");
            
    $commitSuccess = true;
    
    $sql = "SELECT * FROM location WHERE name LIKE \"%${_POST["location"]}%\""; if ($printSQL) echo "<p>$sql</p>";
    $res = sqlite_query($db, $sql);
    $loc = sqlite_fetch_all($res);
        
    if (count($loc) == 0)
      $errorString .= "<p>Location does not match database!</p>";
    else if (count($loc) > 1)
      $errorString .= "<p>Location matches multiple database entries:</p><ul>";
      //foreach ($loc as $entry)
      //echo("<li>Did you mean '". $entry["name"] ."' with id='". $entry["id"] ."'?</li>");
      //echo("</ul>");
    else
      $locationid = $loc[0]["id"];
      // echo("<p>Single match --- '". $loc[0]["name"] ."' with id='". $loc[0]["id"] . "'</p>");
      
    if (!empty($errorString))
      throw new Exception($errorString, 4);

    $datestring = date("Y-m-d", $date);
    $payerID = $_POST["whopaid"];
    
    // -------- UPDATE purchase       TABLE ---------
        
    $sql = "INSERT INTO purchase VALUES (null, \"${_POST["desc"]}\", ${_POST["amount"]}, $payerID, $locationid, \"$datestring\")"; if ($printSQL) echo "<p>$sql</p>";
    $res = sqlite_query($db, $sql);

    if ($sqliterror = sqlite_last_error($db)) throw new Exception($errorString .= $sqliteerror, 4);

    $purchaseID = sqlite_last_insert_rowid($db);
      
    // -------- UPDATE purchasedetail TABLE ---------
    // -------- UPDATE balance        TABLE ---------
    
    foreach (array(1 => "amountA", 2 => "amountP", 3 => "amountN", 4 => "amountB",) as $recipID => $postKey) {
    
      if ($recipID == $payerID || empty($_POST[$postKey])) continue;
      
      $sql = "INSERT INTO purchasedetail VALUES ($purchaseID, $recipID, ${_POST[$postKey]})"; if ($printSQL) echo "<p>$sql</p>";
      $res = sqlite_query($db, $sql);
      
      if ($sqliterror = sqlite_last_error($db)) throw new Exception($errorString .= $sqliteerror, 4);
      
      if ($payerID > $recipID) { $payerStatus = "idfrom"; $recipStatus = "idto";   $addToBal = false; }
      else                     { $payerStatus = "idto"  ; $recipStatus = "idfrom"; $addToBal = true;  }
              
      $sql = "SELECT amount FROM balance WHERE $payerStatus=$payerID AND $recipStatus=$recipID"; if ($printSQL) echo "<p>$sql</p>";
      $res = sqlite_query($db, $sql);
      $bal = sqlite_fetch_single($res);
      
      echo "<p>The balance $payerStatus=$payerID $recipStatus=$recipID was $bal before changes to the database</p>";
      
      if ($addToBal) $bal += $_POST[$postKey];
      else           $bal -= $_POST[$postKey];
      
      echo "<p>The balance $payerStatus=$payerID $recipStatus=$recipID is $bal after changes to the database</p>";

      $sql = "UPDATE balance SET amount=$bal WHERE $payerStatus=$payerID AND $recipStatus=$recipID"; if ($printSQL) echo "<p>$sql</p>";
      $res = sqlite_query($db, $sql);
    }  
  
?>
