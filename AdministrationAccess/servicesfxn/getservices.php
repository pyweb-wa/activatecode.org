
<?php
session_start();

if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

if (!isset($_GET['Id_Api'])) {
    die();
}

require_once './../../backend/config.php';
$Id_Api = (int) $_GET['Id_Api'];


$requestData = $_REQUEST;
$sql = "SELECT COUNT(*) as total_data FROM `foreignapiservice` WHERE is_deleted=0 and Id_Foreign_Api=? ";
$stmt = $pdo->prepare($sql);
$stmt->execute([$Id_Api]);
$totalData = $stmt->fetchall();
$totalData = $totalData[0]['total_data'];

$searchValue = $requestData['search']['value'];


$sql ="SELECT `Id_Service_Api`, `Name`, `code`, `price_in`, `price_out`, `acc_price_out`,
 `max_numbers`, `availability`, `Id_Foreign_Api`, `Modification_Date`, `description`, `country`, `carrier`,
  `service_of_api`, `country_of_api` FROM `foreignapiservice` WHERE is_deleted=0 and Id_Foreign_Api=? ";

if( !empty($searchValue) ) {  
    $sql .=" and ( Name LIKE '%".$searchValue."%' ";    
    $sql .=" OR code LIKE '%".$searchValue."%' ";
    $sql .=" OR country LIKE '%".$searchValue."%' ) ";
   
}

$start = $requestData['start'];
$length = $requestData['length'];

$sql .= " ORDER BY `Id_Service_Api` DESC  LIMIT ".$start.",".$length." ;";

$stmt = $pdo->prepare($sql);
$stmt->execute([$Id_Api]);
$logs = $stmt->fetchall();
$data =  array();
$jarray = [];
foreach ($logs as $json) {
    $nestedData=array();

    $nestedData['Id_Service_Api'] = $json["Id_Service_Api"];
    $nestedData['Name'] = $json["Name"];
    $nestedData['code'] = $json["code"];
    $nestedData['country'] = $json["country"];
    $nestedData['description'] = $json["description"];
    $nestedData['price_in'] = $json["price_in"];
    $nestedData['price_out'] = $json["price_out"];
    $nestedData['carrier'] = $json["carrier"];
    $nestedData['service_of_api'] = $json["service_of_api"];
    $data[] = $nestedData;

    // array_push($data, $json);
}
$totalFiltered = $totalData;
$json_data = array(
    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval( $totalData ),  // total number of records
    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
    );

echo json_encode($json_data);  // send data as json format

// echo (json_encode($jarray));
