<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

    if (
        !empty($_POST['Id_User']) 
        && !empty($_POST['name']) 
        && !empty($_POST['email']) 
        && !empty($_POST['is_super']) 
        && !empty($_POST['Is_Activated'])) {

        $Id = $_POST['Id_User'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        ($_POST['is_super'] == "true" ? $is_super = 1 : $is_super = 0);
        ($_POST['Is_Activated'] == "true" ? $Is_Activated = 1 : $Is_Activated = 0);
        
        require_once './../../backend/config.php';
        
        $stmt = $pdo->prepare("UPDATE `cms_users` SET `email`=?, `name`=?, `is_super`=?, `Is_Activated`=?  WHERE  `Id_User`=?");
        $stmt->execute([$email,$name,$is_super,$Is_Activated, $Id]);
        
        
        $stmt4 = $pdo->prepare("SELECT `Id_User`, `name`,`email`,`is_super`,`Passwd`,   `Is_Activated`
        from cms_users where is_deleted=0  and  `Id_User`=?");
        $stmt4->execute([ $Id]);
        $newuser = $stmt4->fetch();
        if ($newuser["is_super"] == "1") {
            $newuser["is_super"] = true;
        } else {
            $newuser["is_super"] = false;
        }
        if ($newuser["Is_Activated"] == "1") {
            $newuser["Is_Activated"] = true;
        } else {
            $newuser["Is_Activated"] = false;
        }
        echo (json_encode($newuser));
        die();
    }

echo "something went wrong";
