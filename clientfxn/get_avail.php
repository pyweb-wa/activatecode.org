<?php

session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

$url = "https://activatecode.org/backend/out_interface.php?api_key=".$_SESSION['api_key']."&action=get_available";

// $url = "https://smsmarket.goonline.company/backend/out_interface.php?api_key=b62c8f70a6ab62f7d72f4b33bbeb1b87cb4252663f62b1cf2fc76e4f3ab3b51e&action=get_available";
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
    $result = $resp['Result'];
    foreach ($result as $key => $value) {
        if(file_exists('../assets/img/apps_icons/'. ucfirst($value['application']).'.png'))
        {
            $result[$key]['img'] = '<img src="assets/img/apps_icons/' . ucfirst($value['application']) . '.png"  width="40"/>'; 
        }else{
            $result[$key]['img'] = '<img src="assets/img/placeholder.png"   width="40"/>';     
        }
    }
    echo json_encode( $result);
}
