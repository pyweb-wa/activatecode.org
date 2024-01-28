
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['email'])) {
    header('Location:login.php');
    die();
}

if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}
require_once './../../backend/config.php';
$GLOBALS['pdo'] = $pdo;
if (isset($_POST["action"])) {

    if ($_POST["action"] == "getapplist") {
        echo (json_encode(getapplist()));
        die();
    } else if ($_POST["action"] == "update_price") {
        if (isset($_POST["app_list"]) && isset($_POST["price"])) {
            $app_list = $_POST["app_list"];
            $country_list = $_POST["country_list"];

            $price = $_POST["price"];
            if (is_numeric($price)) {
                $result = update_price($app_list, $price, $country_list);
                if ($result == true) {
                    echo (json_encode('{"msg":"OK"}'));
                    die();
                }
            }
        }
    } else if ($_POST["action"] == "getcountrylist") {
        echo (json_encode(getcountrylist()));
        die();
    }
    else if ($_POST["action"] == "getallpricing") {
        echo (json_encode(getallpricing()));
        die();
    }


    echo (json_encode('{"msg":"error"}'));
}
function getapplist()
{


    $stmt = $GLOBALS['pdo']->prepare("SELECT Name FROM `foreignapiservice` group by Name");

    $stmt->execute([]);
    $logs = $stmt->fetchall();
    $jarray = [];
    foreach ($logs as $json) {
        array_push($jarray, $json);
    }

    return $jarray;
}
function getallpricing()
{

    $requestData = $_REQUEST;

    $sql = "SELECT COUNT(*) as total_data FROM `foreignapiservice` group by Name,country_name,price_out ";
    $stmt =  $GLOBALS['pdo']->prepare($sql);
    $stmt->execute([]);
    $totalData = $stmt->fetchall();
    $totalData = $totalData[0]['total_data'];
    
    $searchValue = $requestData['search']['value'];

$sql = "SELECT Name,country_name,price_out FROM `foreignapiservice`  ";
if( !empty($searchValue) ) {  
    $sql .="where  ( Name LIKE '%".$searchValue."%' ";    
    $sql .=" OR country_name LIKE '%".$searchValue."%' ";
    $sql .=" OR price_out LIKE '%".$searchValue."%' ) ";
   
}


$start = $requestData['start'];
$length = $requestData['length'];

$sql .= " group by Name,country_name,price_out ORDER BY `Name` DESC  LIMIT ".$start.",".$length." ";
$stmt = $GLOBALS['pdo']->prepare($sql);
$stmt->execute([]);
$logs = $stmt->fetchall();

    // $stmt = $GLOBALS['pdo']->prepare("SELECT Name,country_name,price_out FROM `foreignapiservice` group by Name,country_name,price_out");

    // $stmt->execute([]);
    // $logs = $stmt->fetchall();

    return $logs;
}

function getcountrylist()
{


    $stmt = $GLOBALS['pdo']->prepare("SELECT country_char,country FROM `countryList`;");

    $stmt->execute([]);
    $logs = $stmt->fetchall();
    $jarray = [];
    foreach ($logs as $json) {
        array_push($jarray, $json);
    }

    return $jarray;
}


function update_price($app_list, $price, $country_list)

{
    $countries = strlen($country_list) > 0 ? explode(',', $country_list) : [] ;
    $apps = strlen($app_list) > 0 ? explode(',', $app_list): [] ;
    
    if (count($apps) > 0) {
        $stmt = $GLOBALS['pdo']->prepare("UPDATE `foreignapiservice` SET `price_out`=? WHERE `Name`=?");
        foreach ($apps as $app) {
            if (count($countries) > 0) {
                $stmt = $GLOBALS['pdo']->prepare("UPDATE `foreignapiservice` SET `price_out`=? WHERE `Name`=? and `country`=?");
                foreach ($countries as $country) {
                    $stmt->execute([$price, $app, $country]);
                }
            } else {
                $stmt->execute([$price, $app]);
            }
        }
    }
    return true;
}
#return (json_encode($jarray));
