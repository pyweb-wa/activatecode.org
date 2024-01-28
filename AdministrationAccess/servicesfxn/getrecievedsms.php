<?php

session_start();
$source = $_SESSION['name'];
$dsn = "mysql:host=localhost;dbname=smsdb";

$username = 'mixsimverify';

$password = 'mix@123123';
$charset = 'utf8';



$requestData = $_REQUEST;

$columns = array(
    0 => 'id',
    1 => 'phone_number',
    2 => 'timestamp',
    3 => 'sms',
    4 => 'code',
    5 => 'Sender_n',
    6 => 'taked',
);

$conn = mysqli_connect("localhost","mixsimverify","mix@123123","smsdb");


$sql = "SELECT COUNT(*) as total_data FROM `bananaapi-results` r join `bananaapi-number` n on r.phone_number = n.phone_number WHERE n.source='".$source."'";
if( !empty($searchValue) ) {  
    $sql .=" and phone_number LIKE '%".$searchValue."%' ";    
    $sql .=" OR code LIKE '%".$searchValue."%' ";
    $sql .=" OR Sender_n LIKE '%".$searchValue."%' ";
   
}

$query=mysqli_query($conn, $sql) or die("getData22.php: get employees");
$totalData = mysqli_fetch_array($query);
$totalData = $totalData[0];

$searchValue = $requestData['search']['value'];


$sql = "SELECT r.* FROM `bananaapi-results` r join `bananaapi-number` n on r.phone_number = n.phone_number WHERE n.source='".$source."'";
if( !empty($searchValue) ) {  
    $sql .=" and phone_number LIKE '%".$searchValue."%' ";    
    $sql .=" OR code LIKE '%".$searchValue."%' ";
    $sql .=" OR Sender_n LIKE '%".$searchValue."%' ";
   
}


$start = $requestData['start'];
$length = $requestData['length'];





$sql .= " ORDER BY r.`id` DESC  LIMIT ".$start.",".$length." ;";

$query=mysqli_query($conn, $sql) or die("getData2.php: get employees");
$totalFiltered = $totalData;
$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();

    $nestedData[] = $row["id"];
    $nestedData[] = $row["phone_number"];
    $nestedData[] = $row["timestamp"];
    $nestedData[] = $row["sms"];
    $nestedData[] = $row["code"];
    $nestedData[] = $row["Sender_n"];
    $nestedData[] = $row["taked"];
    $data[] = $nestedData;
}




$json_data = array(
    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval( $totalData ),  // total number of records
    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
    );

echo json_encode($json_data);  // send data as json format

?>


