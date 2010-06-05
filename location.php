<?php
  $q = $_GET["q"];

  if (!($db = sqlite_open('spenses.db', 0666, $sqliteerror)))
    die($sqliteerror); 
  
  $sql = "SELECT * FROM location WHERE name LIKE '$q%'"; echo "<p>$sql</p>";
  
  $res   = sqlite_query($db, $sql);
  $locations = sqlite_fetch_all($res, SQLITE_ASSOC);
  
  echo "<table border='1'>
  <tr>
  <th>ID</th>
  <th>Name</th>
  <th>Address</th>
  </tr>";

  foreach ($locations as $location) {
    echo "<tr>";
    echo "<td>" . $location['id'] . "</td>";
    echo "<td>" . $location['name'] . "</td>";
    echo "<td>" . $location['addr'] . "</td>";
    echo "</tr>";
  }
  echo "</table>";

?> 