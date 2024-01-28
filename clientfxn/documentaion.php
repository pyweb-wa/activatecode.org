<?php

session_start(); 
 if (!isset($_SESSION['user_email']) ) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


require_once './../backend/config.php';
$GLOBALS['pdo'] = $pdo;

if (isset($_POST["action"])) {

  if ($_POST["action"] == "getcountriesapi") {
        echo (json_encode(getcountriesapi()));
        die();
    }
    else if ($_POST["action"] == "getappsapi") {
        echo (json_encode(getappsapi()));
        die();
    }


    echo (json_encode('{"msg":"error"}'));
}


function getcountriesapi(){

    $stmt = $GLOBALS['pdo']->prepare("SELECT country,country_name FROM `foreignapiservice` GROUP BY country,country_name;");

    $stmt->execute([]);
    $logs = $stmt->fetchall();

    return $logs;

}
function getappsapi(){

    $stmt = $GLOBALS['pdo']->prepare("SELECT Name,code FROM `foreignapiservice` GROUP BY Name,code;");

    $stmt->execute([]);
    $logs = $stmt->fetchall();

    return $logs;

}
?>