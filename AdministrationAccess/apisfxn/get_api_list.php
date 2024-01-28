
<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


require_once './../../backend/config.php';

$stmt = $pdo->prepare("SELECT `Id_Api` , `Name` , `Description`, `Access_Token`, `Refresh_Token`, `Valid`, 
`account_balance`  FROM `foreignapi`  left join finance_accounts on Id_Api=reference_api_id 
 WHERE is_deleted=0");

$stmt->execute();
$logs = $stmt->fetchall();
$jarray = [];
foreach ($logs as $json) {
    if ($json["Valid"] == "1") {
        $json["Valid"] = true;
    } else {
        $json["Valid"] = false;
    }
    array_push($jarray, $json);
}

echo (json_encode($jarray));
