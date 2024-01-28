<?php

session_start();
$source = $_SESSION['name'];
// Connect to the MySQL database
$dsn = "mysql:host=localhost;dbname=smsdb";

$username = 'mixsimverify';

$password = 'SMS@mixsimverify.com';
$charset = 'utf8';




$requestData = $_REQUEST;

$columns = array(
    0 => 'id',
    1 => 'phone_number',
    2 => 'country_code',
    3 => 'source',
    4 => 'application',
    5 => 'taked',
    6 => 'createdTime',

    
);

$conn = mysqli_connect("localhost","mixsimverify","mix@123123","smsdb");


$sql = "SELECT COUNT(*) as total_data FROM `bananaapi-number` WHERE source='".$source."'";
$query=mysqli_query($conn, $sql) or die("getData.php: get employees");
$totalData = mysqli_fetch_array($query);
$totalData = $totalData[0];

$searchValue = $requestData['search']['value'];


$sql = "SELECT  * FROM `bananaapi-number`  WHERE source='".$source."'";
if( !empty($searchValue) ) {  
    $sql .=" WHERE phone_number LIKE '%".$searchValue."%' ";    
    $sql .=" OR country_code LIKE '%".$searchValue."%' ";
    $sql .=" OR source LIKE '%".$searchValue."%' ";
    $sql .=" OR taked LIKE '%".$searchValue."%' ";
}


$start = $requestData['start'];
$length = $requestData['length'];





$sql .= " ORDER BY `bananaapi-number`.`id` DESC  LIMIT ".$start.",".$length." ;";

$query=mysqli_query($conn, $sql) or die("getData.php: get employees");
$totalFiltered = $totalData;
$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();

    $nestedData[] = $row["id"];
    $nestedData[] = $row["phone_number"];
    $nestedData[] = $row["country_code"];
    $nestedData[] = $row["taked"];
    $nestedData[] = $row["source"];
    $nestedData[] = $row["application"];
    $nestedData[] = $row["createdTime"];
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


