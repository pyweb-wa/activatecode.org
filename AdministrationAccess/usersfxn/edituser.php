<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}
include_once "/var/www/smsmarket/html/backend/redisconfig.php";

function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

if (
    isset($_POST['Id']) 
    && isset($_POST['email']) 
    && isset($_POST['name']) 
    && isset($_POST['Valid'])
     && isset($_POST['Is_Activated']) 
     && isset($_POST['access_Token'])) {

    if (
        !empty($_POST['Id']) 
        && !empty($_POST['name']) 
        && !empty($_POST['email']) 
        && !empty($_POST['Valid']) 
        && !empty($_POST['Is_Activated'])) {





        $Id = $_POST['Id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        ($_POST['Valid'] == "true" ? $Valid = 1 : $Valid = 0);
        ($_POST['Is_Activated'] == "true" ? $Is_Activated = 1 : $Is_Activated = 0);
        $access_token = $_POST['access_Token'];


        
        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("UPDATE `users` SET `email`=?, `name`=?, `Is_Activated`=?  WHERE  `Id`=?");
        $stmt->execute([$email,$name, $Is_Activated, $Id]);
        
        if ($access_token == "empty") {
            $stmt3 = $pdo->prepare("update `tokens` set `access_Token`='', `Valid`=? where  `userID`=?");
            $stmt3->execute([$Valid, $Id]);
        } else if ($access_token == "generate new") {
            $access_token = hash('sha256', generateRandomString() . time());
            $stmt3 = $pdo->prepare("update `tokens` set `access_Token`=?, `Valid`=? where  `userID`=?");
            $stmt3->execute([$access_token, $Valid, $Id]);
        } else if ($access_token == "") {
            $stmt3 = $pdo->prepare("update  `tokens` set `Valid`=? where  `userID`=?");
            $stmt3->execute([$Valid, $Id]);
        }

        
        
        $stmt4 = $pdo->prepare("SELECT `Id`, `name`, `email`, `registration_date`, `activation_key`, `Is_Activated`, `Balance`
        , `access_Token`, `expiry_date`, `Valid`, `Tag`
        from users left join tokens   on users.Id = tokens.userID where users.Id=? ");
        $stmt4->execute([$Id]);
        $newuser = $stmt4->fetch();

        $key = "check_api:".$newuser["access_Token"];
        $redis->set($key,$Valid); 

        if ($newuser["Valid"] == "1") {
            $newuser["Valid"] = true;
        } else {
            $newuser["Valid"] = false;
        }
        if ($newuser["Is_Activated"] == "1") {
            $newuser["Is_Activated"] = true;
        } else {
            $newuser["Is_Activated"] = false;
        }
        echo (json_encode($newuser));
        die();
    }
}

echo "something went wrong";
