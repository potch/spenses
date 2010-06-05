<?php
    if (!isset($_COOKIE))         header('Location:' . 'userset.php');    
elseif (!isset($_COOKIE['user'])) header('Location:' . 'userset.php');    
?>