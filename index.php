<?php
require_once "./user.php";
require "./action/db.php";
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <title>'Spenses</title>

    <link rel="shortcut icon" href="./favico.png" type="image/png" />
    <link rel="stylesheet" href="css/reset.css" type="text/css" />
    <link rel="stylesheet" href="css/spenses.css" type="text/css" />
    <script src="js/jquery.js" type="text/javascript"></script>
    <!--
    <link rel="apple-touch-icon" href="/gordon/apple-touch-icon.png" />
    <link rel="apple-touch-startup-image" href="/gordon/mobilesplash.png" />
    -->
  </head>
  <?php
    $username   = $_COOKIE['user']['name'];
    $userid     = $_COOKIE['user']['id'];
    $datestring = date("Y-m-d");
  ?>
  <body>
    <nav id="nav">
      <ul>
	<li pane="balances" class="selected">balance</li>
	<li pane="purchases">purchase</li>
	<li>location</li>
      </ul>
    </nav>
    <div id="content">
      <div id="purchases" class="pane">
	<h2>Add a Purchase</h2>
	<form action='action/addpurchase.php' method='post'>
	  <fieldset>
	    <div class="row"><label for="purchasedate">Date</label><input id="purchasedate" type='date' name='date' value='<?php echo $datestring; ?>' /></div>
	    <div class="row"><label for="">Who's Paying</label><?php user_dropdown($userid); ?></div>
	    <div class="row"><label for="">Location</label><input type='text' name='location'/></div>
	    <div class="row"><label for="">Description</label><input type='text' name='desc'    /></div>
	    <div class="row"><label for="">Amount (in $)</label><input type='tel'  name='amount'  /></div>
	    <div class="row"><label for="">To Andrew</label><input type='tel'  name='amountA' /></div>
	    <div class="row"><label for="">To Becky</label><input type='tel'  name='amountB' /></div>
	    <div class="row"><label for="">To Nick</label><input type='tel'  name='amountN' /></div>
	    <div class="row"><label for="">To Potch</label><input type='tel'  name='amountP' /></div>
	  </fieldset>
	  <input type='submit' />
	</form>
      </div>
      <div id="balances" class="pane selected">
	<div id="owe">
	  <h2>People You Owe</h2>
	  <section>
	    <ul id="owelist"></ul>
	  </section>
	</div>
	<div id="owed">
	  <h2>People Who Owe You</h2>
	  <section>
	    <ul id="owedlist"></ul>
	  </section>
	</div>
      </div>
    </div>
    <pre><xmp id="out"></xmp></pre>
    
    <script src="js/spenses.js" type="text/javascript"></script>
    
  </body>
</html>