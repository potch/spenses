<?php

$expire = time() - 3600;
setcookie('user[name]'  , '', $expire, '/');
setcookie('user[userid]', '', $expire, '/');
setcookie('user[nick]'  , '', $expire, '/');
setcookie('user[email]' , '', $expire, '/');

unset($_COOKIE['user']);

echo json_response('success', null, null);

?>
