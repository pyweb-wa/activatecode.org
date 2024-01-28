<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (isset($_POST['Id_User'])) {
    if (!empty($_POST['Id_User'])) {
        $Id = $_POST['Id_User'];

        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("UPDATE `cms_users` SET `is_deleted`=1 ,`Is_Activated`=0 where `Id_User`=?");
        $stmt->execute([$Id]);

        
        echo ("{Id:'" . $Id . "'}");
        die();
    }
}

echo "something went wrong";
