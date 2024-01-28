<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
} 
if (!isset($_POST['user_id']) ){ echo "something went wrong";die();}
if (empty($_POST['user_id']) ) {echo "something went wrong";die();}
$user_id = $_POST['user_id'];
if (isset($_POST['new_pass'])) 
{
    if( !empty($_POST['new_pass'])) 
    { 
        $new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT); 
        if(strlen($new_pass)>7)
        { 
            require_once './../../backend/config.php';
            $stmt = $pdo->prepare("UPDATE users set `passwd`=? where Id=? ");   
            $stmt->execute([$new_pass,$user_id]);  
            echo "Success";
            die();
        }
     }
}  
echo "something went wrong";
