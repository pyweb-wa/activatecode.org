<?php
session_start(); 
 if (!isset($_SESSION['user_email']) ) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

if (!isset($_POST['country'])) {
    die();
}

require_once './../backend/config.php';
$country = $_POST['country'];

$stmt = $GLOBALS['pdo']->prepare("SELECT `carrier` FROM `foreignapiservice` WHERE `is_deleted` = 0 and country_name=? GROUP by `carrier`");
$stmt->execute([$country]);
$logs = $stmt->fetchall();
echo (json_encode($logs));
 