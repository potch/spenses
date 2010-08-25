<?php
require_once "./user.php";
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
    <!--
    <link rel="apple-touch-icon" href="/gordon/apple-touch-icon.png" />
    <link rel="apple-touch-startup-image" href="/gordon/mobilesplash.png" />
    -->
  </head>
  <?php
    $username   = $_COOKIE['user']['name'];
    $usernick   = $_COOKIE['user']['nick'];
    $userid     = $_COOKIE['user']['userid'];
    $datestring = date("Y-m-d");
  ?>
  <body data-id='$userid'>
    <div id="modal" class="hidden">
      <div>
        <p id='modal-msg'></p>
        <a id="modal-ok" class="button" href="#">Ok</a>
      </div>
    </div>
    <div id='login' class='<?php echo $needslogin ? '' : 'hidden'; ?>'>
      <h1>SPENSES LOGO HERE!!!!</h1>
      <h2>Please login</h2>
      <form id='login-form' action='action/login_start.php' method='post'>
        <fieldset>
          <div class='row'><label for="">Email</label><input id='login-email' type='text' name='email'/></div>
        </fieldset>
        <input type='submit' />
      </form>
    </div>
    <nav id="nav">
      <ul>
        <li pane="balances" class="selected">balance</li>
        <li pane="purchases">purchase</li>
        <li>location</li>
      </ul>
    </nav>
    <div id="debug" class="hidden"></div>
    <div id="content">
      <div id="purchases" class="pane">
      <h2>Add a Purchase</h2>
      <form id="purchases-form" action='action/add_purchase.php' method='POST'>
        <fieldset>
          <div class="row"><label for="">Cohort</label><select id='cohorts' name='cohortid'><option selected name='None'>Select cohort...</option></select></div>
          <!-- need to add "cohort"=cohortId field in post data -->
        </fieldset>
        <fieldset>
          <div class="row"><label for="purchasedate">Date</label><input id="purchasedate" type='date' name='date' value='<?php echo $datestring; ?>' /></div>
          <div class="row"><label for="whopaid">Who's Paying</label><select id='whopaid' name='whopaid'><option selected name='<?php echo $userid; ?>'><?php echo $usernick; ?></option></select></div>
          <input type='hidden' id='location' value="" name='location'/>
          <!-- <div class="row"><label for="location">Location</label><input type='text' id='location' name='location'/></div> -->
          <div class="row"><label for="desc">Description</label><input type='text' id='desc' name='desc'    /></div>
          <div class="row"><label for="amount">Amount (in $)</label><input type='tel' id='amount' name='amount'  /></div>
        </fieldset>
        <fieldset id="purchaseamounts">
           <!-- <div class="row"><label for="">To Andrew</label><input type='tel'  name='iou[0][amount]' /><input type="hidden" name='iou[0][userid]' value='' /></div> -->
        </fieldset>
        <input type='submit' />
      </form>
      </div>
      <div id="balances" class="pane selected">
        <div id="debtfree" class="hidden">
          You have no outstanding balances.
        </div>
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
        <div id="purchases">
          <h2>Recent purchases</h2>
          <section>
            <ul id="purchaselist"></ul>
          </section>
        </div>
      </div>
    </div>
    <pre><xmp id="out"></xmp></pre>
    <script src="js/jquery.js"></script>
    <script src="js/spenses.js"></script>
  </body>
</html>