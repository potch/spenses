<?php
  $q = $_GET["q"];

  if (!($db = new SQLite3('./db/spenses.db', SQLITE3_OPEN_READONLY))
    die($db->lastErrorMsg());
      
  $sql = "SELECT * FROM location WHERE name LIKE '$q%'"; echo "<p>$sql</p>";
  
  $res = $db->query($sql);
  // $res   = sqlite_query($db, $sql);
  // $locations = sqlite_fetch_all($res, SQLITE_ASSOC);
  
  echo "<table border='1'>
  <tr>
  <th>ID</th>
  <th>Name</th>
  <th>Address</th>
  </tr>";

  while ($location = $res->fetchArray(SQLITE3_ASSOC)) {
  // foreach ($locations as $location) {
    echo "<tr>";
    echo "<td>" . $location['id'] . "</td>";
    echo "<td>" . $location['name'] . "</td>";
    echo "<td>" . $location['addr'] . "</td>";
    echo "</tr>";
  }
  echo "</table>";
?> 