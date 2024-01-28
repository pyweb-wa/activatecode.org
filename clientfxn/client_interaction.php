<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_email'])) {
    header('Location:login.php');
    die();
}

require_once './backend/config.php';

$GLOBALS['pdo'] = $pdo;
function getcountry()
{
    $stmt = $GLOBALS['pdo']->prepare("SELECT `country_name`,`country` FROM `foreignapiservice` WHERE `is_deleted` = 0 GROUP by `country_name`,`country`");
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}
function getcarriers($country)
{
    $stmt = $GLOBALS['pdo']->prepare("SELECT `carrier` FROM `foreignapiservice` WHERE `is_deleted` = 0 and country_name=? GROUP by `carrier`");
    $stmt->execute([$country]);
    $logs = $stmt->fetchall();
    return $logs;
}
function getBalance()
{
    $stmt = $GLOBALS['pdo']->prepare("SELECT Balance from users where Id=?");
    $stmt->execute([$_SESSION['id']]);
    $logs = $stmt->fetchall();
    $_SESSION['balance'] = $logs[0]['Balance'];
}

function getavailablenumber()
{
    $url = "https://activatecode.org/backend/out_interface.php?api_key=" . $_SESSION["api_key"] . "&action=get_available";
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    // curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $time = time();
    $auth = md5($time . "banana-api-passwordCode");
    $headers = array(
        "code: " . $time,
        "Authorization: " . $auth,
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);

    curl_close($curl);
    $resp = json_decode($resp, true);
    if ($resp['Msg'] == 'OK') {
        $countries = array_column($resp['Result'], 'country');
        return array_filter(array_unique($countries), function ($value) {
            return $value !== null;
        });
    }
}
getBalance();
