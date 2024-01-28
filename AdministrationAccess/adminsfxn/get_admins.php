<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

require_once './../../backend/config.php';

$query = "SELECT `Id_User`, `name`,`email`,`is_super`,`Passwd`,`token`,  `Is_Activated`,balance
from cms_users where is_deleted=0 ";

$stmt = $pdo->prepare($query);

$stmt->execute();
$logs = $stmt->fetchall();
$jarray = [];
foreach ($logs as $json) {
    if ($json["is_super"] == "1") {
        $json["is_super"] = true;
    } else {
        $json["is_super"] = false;
    }
    if ($json["Is_Activated"] == "1") {
        $json["Is_Activated"] = true;
    } else {
        $json["Is_Activated"] = false;
    }
    array_push($jarray, $json);
}

echo (json_encode($jarray));
