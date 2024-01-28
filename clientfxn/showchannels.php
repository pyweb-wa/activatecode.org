<?php
 if(!isset($_SESSION)) 
 { 
     session_start(); 
 } 
if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}



require 'channel-download.php';
$output = ob_get_clean();
echo $output;
