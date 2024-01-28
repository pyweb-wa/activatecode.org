<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

if(!isset($_GET) || $_GET['action'] != 'STATS_NUMBER'){
    echo json_encode(array("data"=> null));
    die();
}


 $url = "https://activatecode.org/backend/out_interface.php?api_key=".$_SESSION['token']."&action=STATS_NUMBER&adm=1";
//$url = "https://sms.goonline.company/backend/out_interface.php?api_key=d08b3d361805620a9532af498af2ee9af4d5240c1a30ad5c62a576303a7119cb&action=get_available";
//  echo $url;
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

// curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
curl_close($curl);
$resp = json_decode($resp, true);

if ($resp['Msg'] == 'OK') {
    $result = array_values($resp['Result']['stats']);
    echo json_encode( $result);
}else{
    echo 'null';
}
