<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php require "./action/db.php"; ?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <title>'Spenses</title>

    <link rel="shortcut icon" href="../favico.png" type="image/png" />
    <link rel="stylesheet" href="../css/spenses.css" type="text/css" />
  </head>
  <body>
    <?php
      $dbh = open_db();

      if (isset($_COOKIE['user'])) {
        echo "<p>Welcome back, " . $_COOKIE['user']['name'] . "</p>";
      } else {
        echo "Select your user name:";
	echo "<ul>";
	foreach ($dbh->query('SELECT * FROM user', PDO::FETCH_ASSOC) as $row) {
	  echo "<li class='entry'><a href='./action/dologin.php?uid=".$row['id']."'>".$row['name']."</a></li>";
	}
	echo "</ul>";
      }

      echo "These are the contents of your cookie:";
      print_r($_COOKIE);
    ?>
  </body>
</html>
