<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

 $url = "https://activatecode.org/backend/out_interface.php?api_key=".$_SESSION['token']."&action=get_available&adm=1";
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
