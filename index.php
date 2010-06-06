<?php
require_once "./user.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />

		<title>'Spenses</title>
		
		<link rel="shortcut icon" href="./favico.png" type="image/png" />
		<link rel="stylesheet" href="reset.css" type="text/css" />
		<link rel="stylesheet" href="spenses.css" type="text/css" />
		<script src="jquery.js" type="text/javascript"></script>
		<script src="spenses.js" type="text/javascript"></script>
		<!--
		<link rel="apple-touch-icon" href="/gordon/apple-touch-icon.png" />
		<link rel="apple-touch-startup-image" href="/gordon/mobilesplash.png" />
		-->
	</head>
        <?php
              function get_user_list() {
                if ($db = sqlite_open('spenses.db', 0666, $sqliteerror)) {
    
                  $sql = "SELECT * FROM user";
  
                  $res   = sqlite_query($db, $sql);
                  $users = sqlite_fetch_all($res, SQLITE_ASSOC);
  
                  for ($i = 0; $i < count($users); $i++)
                    $users[$i]['id'] = (int) $users[$i]['id'];

                  return $users;
                } else {
                  die('Could not fetch user list from database');
                }
              }

              function user_dropdown($id) {
                $userlist = get_user_list();
                echo "<select name='whopaid' id='whopaid'>\n";
                foreach ($userlist as $user) {
                  echo "<option " . ($user['id'] == $id ? "selected" : "") . " value='${user['id']}'>${user['name']}</option>\n";
                }
                echo "</select>\n";        
              }
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
                <form action='addpurchase.php' method='post'>
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


        <script>
        $(document).ready(function () {
            
            
            $('#nav ul').click(function(e) {
            	var li = $(e.target);
            	$('#nav ul li.selected, #content .pane.selected').removeClass("selected");
            	li.addClass("selected");
            	$("#" + li.attr('pane')).addClass('selected');
            });
            
            $.post('getbalance.php', function(data) {
                var result = $.parseJSON(data);
            
                document.getElementById("owelist").innerHTML="";
                document.getElementById("owedlist").innerHTML="";
            
                if (result.owe && result.owe.length) {
                    for (var i = 0; i < result.owe.length; i++) {
                        var item = result.owe[i];
                        document.getElementById("owelist").innerHTML += "<li>You owe " + item.name + " <span class='amount'>$" + item.amount + "</li>";
                    }
                } else {
                    $("#owe").addClass("hideMe");
                }
                if (result.owed && result.owed.length) {
                    for (var i=0; i < result.owed.length; i++) {
                        var item = result.owed[i];
                        document.getElementById("owedlist").innerHTML += "<li>" + item.name + " owes you <span class='amount'>$" + item.amount + "</li>";
                    }
                } else {
                    $("#owed").addClass("hideMe");
                }
            });
        });
        </script>

        
	</body>
</html>