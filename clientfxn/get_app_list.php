
<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

// if (!isset($_POST['country'])) {
//     die();
// }

require_once './../backend/config.php';
(isset($_POST['country']))? $country = $_POST['country']: null;
// //<img src='assets/img/apps_icons/wa.png' width='24'></img>whatsapp
// //$query = "SELECT `Id_Service_Api` as 'id'  , price_out ,code,country,carrier, `Name`  FROM `foreignapiservice`
// //WHERE is_deleted=0  and country_name=?";
// $query = "SELECT   price_out ,code,country,carrier, `Name`  FROM `foreignapiservice`
// WHERE is_deleted=0  and country_name=?  ";
// $q_tail1=" group by  price_out ,code,country, `Name` ";
// $q_tail2=" and carrier=? group by  price_out ,code,country,carrier, `Name` ";
// $tail_flag=0;
// $arrayParams = [];
// array_push($arrayParams, $country);
// if (isset($_POST['carrier'])) { //add carrier to request
//     if ($_POST['carrier'] != 'any' && $_POST['carrier'] != '') {
//         $query = $query . $q_tail2;
//         $tail_flag=1;
//         array_push($arrayParams, $_POST['carrier']);
//     }
// }

// if ($tail_flag==0){ $query = "SELECT   price_out ,code,country, `Name`  FROM `foreignapiservice` WHERE is_deleted=0  and country_name=?  "  . $q_tail1;}// no carrier set in request
// $stmt = $pdo->prepare($query);
// $stmt->execute($arrayParams);
// $logs = $stmt->fetchall();
$jarray = [];

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
if (is_array($resp) && isset($resp['Msg']) && $resp['Msg'] == 'OK') {
    $logs = $resp['Result'];
    foreach ($logs as $row) {
        if ($country != null && trim(strtolower($row['country'])) == strtolower($country)) {
         
            $image = "";
            $price = $row["price_out"] . "$";
            $row["application"] = rtrim($row["application"]);
            if (file_exists('../assets/img/apps_icons/' . ucfirst($row["application"]) . '.png')) {

                $image = "<img src='assets/img/apps_icons/" . ucfirst($row["application"]) . ".png'  width='32'/>" . $row["application"];
            } else {
                $image = "<img src='assets/img/placeholder.png'  width='32'/>" . $row["application"];
            }
            $button = '<button type="button" class="btn btn-primary" onclick="purchase(\'' . $row["app_code"] . '\',\'' . $row["country_code"] . '\',\'\',\'' . $_SESSION['api_key'] . '\')"><i class="fa fa-check" aria-hidden="true"></i></button>';

            $finalstr = $image ;//. "  " . $price; //." ".$button ;
            $result = (object) ['id' => 0, 'app' => ''];
            //$result->id = $row["id"];
            $result->id = $row["application"];
            $result->app = $finalstr;
            $result->check = $button;
            $result->count = $row["count"];
            $result->country = $row["country"];
            $result->country_code = $row["country_code"];
            $result->app_code = $row["app_code"];
            $result->price_out = $row["price_out"]. "$";

            array_push($jarray, $result);
        }
    }

}

echo (json_encode($jarray));
