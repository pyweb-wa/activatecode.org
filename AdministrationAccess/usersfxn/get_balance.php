<?php

session_start();
if (!isset($_POST['action']) || $_POST['action'] != 'get_balance' || !isset($_SESSION['id'])) {
    echo "something went wrong";
    die();
}
 
 
require_once './../../backend/config.php';
if ($_SESSION["is_super"] != 1) {
    $sql = "SELECT balance from cms_users WHERE Id_User = ?";
    $stmt_b = $pdo->prepare($sql);
    $stmt_b->execute([$_SESSION['id']]);
    $res = $stmt_b->fetchAll();
    if (count($res) >= 1) {
        $admin_balance = $res[0]['balance'];
        echo $admin_balance ;
    } else {
        echo "unkown";
    }
}
die();
?>