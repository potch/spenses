<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />

		<title>'Spenses</title>

		<link rel="shortcut icon" href="./favico.png" type="image/png" />
    <link rel="stylesheet" href="./spenses.css" type="text/css" />
	</head>
	<body>
	<?php
	  if (!($db = sqlite_open('spenses.db', 0666, $sqliteerror)))
      die($sqliteerror); 

 	  if (isset($_COOKIE['user']))
	    echo "<p>Welcome back, " . $_COOKIE['user']['name'] . "</p>";
	  else {
	    echo "Select your user name:";
	    echo "<ul>";
	    $res = sqlite_array_query($db, "SELECT * FROM user");
	    foreach ($res as $entry)
	      echo "<li class='entry'><a href='dologin.php?uid=".$entry['id']."'>".$entry["name"]."</a></li>";
	    echo "</ul>";
	  }

	  echo "These are the contents of your cookie:";
	  print_r($_COOKIE);
  ?>
	</body>
</html>
