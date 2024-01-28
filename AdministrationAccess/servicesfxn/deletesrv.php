<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (isset($_POST['Id_Service_Api'])) {
    if (!empty($_POST['Id_Service_Api'])) {
        $Id = $_POST['Id_Service_Api'];

        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("UPDATE `foreignapiservice` SET `is_deleted`=1   where `Id_Service_Api`=?");
        $stmt->execute([$Id]);


        echo ("{Id_Service_Api:'" . $Id . "'}");
        die();
    }
}

echo "something went wrong";
