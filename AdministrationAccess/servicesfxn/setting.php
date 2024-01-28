<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:login.php');
    die();
}


 if (
    isset($_POST["change_password"])
    && isset($_POST["old_pass"])
    && isset($_POST["new_pass"])
    && isset($_SESSION["id"])
) {
   require_once './../../backend/config.php';
    $stmt = $pdo->prepare("SELECT `Passwd` FROM `cms_users`  WHERE Id_User =? and email=?");
    $stmt->execute([$_SESSION["id"], $_SESSION["email"]]);
    $json = $stmt->fetch();

    if ($json["Passwd"]) {
        if (password_verify($_POST['old_pass'], $json["Passwd"])) {
            $new_passwd = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);

            $stmt2 = $pdo->prepare("update  `cms_users` set `Passwd`=? WHERE `Id_User` =?  and email=?");
            $stmt2->execute([$new_passwd, $_SESSION["id"], $_SESSION["email"]]);
            #$json = $stmt2->fetch();
            echo ('{"msg":"success"}');
            

            die();
        }
        else{
            echo ('{"msg":"wrong_pass"}');
            die();
        }
    }
    echo ('{"msg":"db_error"}');

    die();
}
