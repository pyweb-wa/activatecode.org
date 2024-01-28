<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


require_once './../backend/config.php';

if (isset($_POST["get_token"]) && isset($_SESSION["id"])) {
    $query = "SELECT access_Token From tokens WHERE userID =?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION["id"]]);
    $logs = $stmt->fetchall();
    //var_dump($logs);
    $json = json_encode($logs[0]);
    echo ($json);
    die();
} else if (isset($_POST["renew_token"]) && isset($_SESSION["id"])) {
    $token =   hash('sha256', generateRandomString() . time());
    $query = "UPDATE `tokens` SET `access_Token`=? WHERE userID =?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$token, $_SESSION["id"]]);
    echo ('{"msg":"success","token":"' . $token . '"}');
    die();
} else if (
    isset($_POST["change_password"])
    && isset($_POST["old_pass"])
    && isset($_POST["new_pass"])
    && isset($_SESSION["id"])
) {

    $stmt = $pdo->prepare("SELECT `Passwd` FROM `users`  WHERE Id =? and email=?");
    $stmt->execute([$_SESSION["id"], $_SESSION["user_email"]]);
    $json = $stmt->fetch();

    if ($json["Passwd"]) {
        if (password_verify($_POST['old_pass'], $json["Passwd"])) {
            $new_passwd = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);

            $stmt2 = $pdo->prepare("update  `users` set `Passwd`=? WHERE `Id` =?  and email=?");
            $stmt2->execute([$new_passwd, $_SESSION["id"], $_SESSION["user_email"]]);
            #$json = $stmt2->fetch();
            echo ('{"msg":"success"}');


            die();
        } else {
            echo ('{"msg":"wrong_pass"}');
            die();
        }
    }
    echo ('{"msg":"db_error"}');

    die();
} else if (isset($_POST["call_back_url"]) && isset($_SESSION["id"])) {

    $decodedUrl = urldecode($_POST["call_back_url"]);
    $ch = curl_init();
    $object = '{ "id": 15652659, "phone_number": 212681075870, "sms": "this is a test sms for check the callbackurl", "application": "whatsapp", "code": "128961"}';
    curl_setopt($ch, CURLOPT_URL, $decodedUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $object);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
        )
    );
    $response = curl_exec($ch);

    if ($response === false) {
        echo ('{"error":"true","msg":"cURL error:' . curl_error($ch) . ' "}');
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode === 200) {
            
          
            $jsonResponse = json_decode($response, true);
            // Check if the status is OK
            if ($jsonResponse && isset($jsonResponse['status']) && strtolower($jsonResponse['status']) === 'ok') {
                $stmt = $pdo->prepare("UPDATE `users` SET `callback_url` =?  WHERE Id =?");
                $stmt->execute([$_POST["call_back_url"], $_SESSION["id"]]);    
                echo ('{"error":"false","msg":"Status is OK!"}');
            } else {
                echo ('{"error":"true","msg":"Request was not successful.response :' . $response . ' "}');
            }
           
        } else {
            echo ('{"error":"true","msg":"Request was not successful. Status code:' . $httpCode . ' "}');
        }
    }
    curl_close($ch);
}


else if (isset($_POST["clear_call_back_url"]) && isset($_SESSION["id"])) {
    $stmt = $pdo->prepare("UPDATE `users` SET `callback_url` =?  WHERE Id =?");
    $stmt->execute(["", $_SESSION["id"]]);
    echo ('{"error":"false","msg":"success"}');
} else if (isset($_POST["get_call_back_url"]) && isset($_SESSION["id"])) {
    $stmt = $pdo->prepare("SELECT `callback_url`,`callback_status` FROM `users`  WHERE Id =?");
    $stmt->execute([$_SESSION["id"]]);
    $json = $stmt->fetch();
    echo ('{"error":"false","callback_url":"' . $json["callback_url"] . '","callback_status":"' . $json["callback_status"] . '"}');
} else if (isset($_POST["set_callback_state"]) && isset($_SESSION["id"])) {
    $callback_stat= 0;
    if ($_POST["set_callback_state"] == 'true')
        $callback_stat= 1;
    $stmt = $pdo->prepare("UPDATE `users` SET `callback_status` =?  WHERE Id =?");
    $stmt->execute([$callback_stat,$_SESSION["id"]]);
    echo ('{"error":"false","msg":"done"}');
}





function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}
