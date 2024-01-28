<?php
// require_once "validate_token.php";
// if (!checkTokenInDatabase()) {
//     header('Location: index.php');
//     exit(); 
// }

$dsn = "mysql:host=localhost;dbname=smsdb";

$username = 'mixsimverify';

$password = 'SMS@mixsimverify.com';
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

$conn = mysqli_connect("localhost","mixsimverify","SMS@mixsimverify.com","smsdb");


$sql = "SELECT
(SELECT COUNT(*) FROM `bananaapi-results` WHERE phone_number LIKE '569%' AND `bananaapi-results`.`timestamp` >= (SELECT pushtime FROM `country_stats` WHERE country = 'Chile')) AS total_data,
(SELECT COUNT(*) FROM `bananaapi-results` WHERE phone_number LIKE '569%' AND `bananaapi-results`.`timestamp` >= (SELECT pushtime FROM `country_stats` WHERE country = 'Chile' AND taked = 1)) AS taked_count;
";
$query=mysqli_query($conn, $sql) or die("getData22.php: get employees");
$totalData_all = mysqli_fetch_array($query);

$totalData = $totalData_all['total_data'];
$total_taked = $totalData_all['taked_count'];
$searchValue = $requestData['search']['value'];


$sql = "SELECT  * FROM `bananaapi-results`  where phone_number like '569%' and `bananaapi-results`.`timestamp` >= (select pushtime from `country_stats` where country = 'Chile') ";




if (!empty($searchValue)) {
    if (strpos($searchValue, ':') !== false) {
        $searchParts = explode(':', $searchValue, 2);
        $prefix = trim($searchParts[0]);
        $searchTerm = trim($searchParts[1]);

        switch ($prefix) {
            case 't':
                $sql .= " AND taked LIKE '%" . $searchTerm . "%' ";
                break;
            case 'a':
                $sql .= " AND application LIKE '%" . $searchTerm . "%' ";
                break;
            case 'p':
                $sql .= " AND phone_number LIKE '%" . $searchTerm . "%' ";
                break;
            case 's':
                $sql .= " AND sms LIKE '%" . $searchTerm . "%' ";
                break;
            default:
                $sql .= " AND (taked LIKE '%" . $searchValue . "%' ";
                $sql .= " OR Sender_n LIKE '%" . $searchValue . "%' ";
                $sql .= " OR application LIKE '%" . $searchValue . "%' ";
                $sql .= " OR sms LIKE '%" . $searchValue . "%' ";
                $sql .= " OR phone_number LIKE '%" . $searchValue . "%') ";
                break;
        }
    } else {
        $sql .= " AND (taked LIKE '%" . $searchValue . "%' ";
        $sql .= " OR Sender_n LIKE '%" . $searchValue . "%' ";
        $sql .= " OR application LIKE '%" . $searchValue . "%' ";
        $sql .= " OR sms LIKE '%" . $searchValue . "%' ";
        $sql .= " OR phone_number LIKE '%" . $searchValue . "%') ";
    }
}










// if( !empty($searchValue) ) {  
//     $sql .= " AND( ";
//     $sql .=" phone_number LIKE '%".$searchValue."%' ";    
//     $sql .=" OR code LIKE '%".$searchValue."%' ";
//     $sql .=" OR sms LIKE '%".$searchValue."%'  ";

//     $sql .=" ) ";
   
// }


$start = $requestData['start'];
$length = $requestData['length'];





$sql .= " ORDER BY `bananaapi-results`.`id` DESC  LIMIT ".$start.",".$length." ;";

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
    $nestedData[] = $row["application"];
    $data[] = $nestedData;
}




$json_data = array(
    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval( $totalData ),  // total number of records
    "recordsTotal_taked"    => intval( $total_taked ),  // total number of records
    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData

    "data"            => $data   // total data array
    );

echo json_encode($json_data);  // send data as json format

?>


