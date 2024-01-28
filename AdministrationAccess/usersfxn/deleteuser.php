<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (isset($_POST['Id'])) {
    if (!empty($_POST['Id'])) {
        $Id = $_POST['Id'];

        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("UPDATE `users` SET `is_deleted`=1 ,`Is_Activated`=0 where `Id`=?");
        $stmt->execute([$Id]);

        $stmt3 = $pdo->prepare("update `tokens` set  `Valid`=0 where  `userID`=?");
        $stmt3->execute([$Id]);
        echo ("{Id:'" . $Id . "'}");
        die();
    }
}

echo "something went wrong";
