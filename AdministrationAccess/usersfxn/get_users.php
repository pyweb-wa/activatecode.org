<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

require_once './../../backend/config.php';

$query = "SELECT `Id`, `name`,`email`,  `registration_date`, `activation_key`, `Is_Activated`, `Balance`
, `access_Token`, `expiry_date`, `Valid`, `Tag`
from users left join tokens   on users.Id = tokens.userID where is_deleted=0 ";
if($_SESSION["is_super"] == 0){
$query = $query . " && admin_id= " . $_SESSION["id"];
}

$stmt = $pdo->prepare($query);

$stmt->execute();
$logs = $stmt->fetchall();
$jarray = [];
foreach ($logs as $json) {
    if ($json["Valid"] == "1") {
        $json["Valid"] = true;
    } else {
        $json["Valid"] = false;
    }
    if ($json["Is_Activated"] == "1") {
        $json["Is_Activated"] = true;
    } else {
        $json["Is_Activated"] = false;
    }
    array_push($jarray, $json);
}

echo (json_encode($jarray));
