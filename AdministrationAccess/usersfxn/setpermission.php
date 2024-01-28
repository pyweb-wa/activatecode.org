<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

if (isset($_POST['user_id']) && isset($_POST['api_id']) && isset($_POST['check'])) {
    if (!empty($_POST['user_id']) && !empty($_POST['api_id']) && !empty($_POST['check'])) {
        $user_id = $_POST['user_id'];
        $api_id = $_POST['api_id'];
        $check = $_POST['check'];
        require_once './../../backend/config.php';
        if ($check == 2) {
            $stmt = $pdo->prepare("DELETE FROM user_allowed_api WHERE api_id=? and user_id=? ");
        } else if ($check == 1) {
            $stmt = $pdo->prepare("INSERT INTO user_allowed_api ( api_id,user_id)values(?,?) ");
        }

        $stmt->execute([$api_id, $user_id]);
        echo  "{res:'success'}";
        die();
    }
} 

echo "something went wrong";
