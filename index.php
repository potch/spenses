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
        <div id="nav">
            <ul>
                <li>purchase</li>
                <li class="spacer"></li>
                <li class="active">balance</li>
                <li class="spacer"></li>
                <li>location</li>
            </ul>
        </div>
        <div id="content">
            <div id="purchases" class="pane">
                <form action='addpurchase.php' method='post'>
                    <table>
                        <tr><td>Date:         </td><td><input type='date' name='date'     value='<?php echo $datestring; ?>' /> </td></tr>
                        <tr><td>Who's Paying: </td><td><?php user_dropdown($userid); ?></td>
                        <tr><td>Location:     </td><td><input type='text' name='location'/> </td></tr>
                        <tr><td>Description:  </td><td><input type='text' name='desc'    /> </td></tr>         
                        <tr><td>Amount (in $):</td><td><input type='tel'  name='amount'  /> </td></tr>
                        <tr><td>To Andrew:    </td><td><input type='tel'  name='amountA' /> </td></tr>
                        <tr><td>To Becky:     </td><td><input type='tel'  name='amountB' /> </td></tr>
                        <tr><td>To Nick:      </td><td><input type='tel'  name='amountN' /> </td></tr>
                        <tr><td>To Potch:     </td><td><input type='tel'  name='amountP' /> </td></tr>
                        <tr><td colspan=2 align='center'><input type='submit' /></td></tr>
                    </table>
                </form>
            </div>
            <div id="balances" class="pane selected">
                <div id="owe">
                    <h2>People You Owe</h2>
                    <ul id="owelist"></ul>
                </div>
                <div id="owed">
                    <h2>People Who Owe You</h2>
                    <ul id="owedlist"></ul>
                </div>
                <script>
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
                </script>
            </div>
        </div>

        
	</body>
</html>