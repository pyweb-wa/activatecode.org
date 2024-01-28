<?php

// 
// try {
    // connect to the database
    // $dsn = "mysql:host=localhost;dbname=smsdb";

    // $username = 'mixsimverify';

    // $password = 'SMS@mixsimverify.com';
    // $charset = 'utf8';

    // $conn = new PDO($dsn, $username, $password);
    // // set the PDO error mode to exception
    // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // execute the SELECT query
    // $stmt = $conn->query("SELECT * FROM `redis_numbers`  LIMIT ".$_REQUEST['start']." ,".$_REQUEST['length'].   ";
    // order by id DESC ");

    
    // // retrieve the rows of the result set
    // $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // // close the cursor and connection
    // $stmt->closeCursor();
    // $conn = null;


// get the column names from the first row
// $columns = array_keys($data[0]);


// Connect to the MySQL database
$dsn = "mysql:host=localhost;dbname=smsdb";

$username = 'mixsimverify';

$password = 'SMS@mixsimverify.com';
$charset = 'utf8';

// try {
//     $conn = new PDO($dsn, $username, $password);
//     //$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
//     // set the PDO error mode to exception
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     }
// catch(PDOException $e)
//     {
//     echo "Connection failed: " . $e->getMessage();
//     }

// // Get the parameters from DataTables
// $start = 0;
// $length = 100;
// if(isset( $_POST['start'])){
//     $start = $_POST['start'];
// }
// if(isset( $_POST['length'])){
//     $start =$_POST['length'];
// }

// //$search = $_GET['search']['value'];
// //$order_column = $_GET['order'][0]['column'];
// //$order_dir = $_GET['order'][0]['dir'];

// // Get the columns to order by
// $columns = array(

//     0 => 'id',
//     1 => 'phone_number',
//     2 => 'country_code',
//     3 => 'taked',
//     4 => 'source',
//     5 => 'createdTime'
    
// );

// try{


// // Build the query
// $sql = "SELECT * FROM `redis_numbers`";

// // Add the search term
// if (!empty($search)) {
//     $sql .= " WHERE name LIKE :search OR age LIKE :search OR address LIKE :search";
// }

// // Add the order
// $sql .= " ORDER BY id  ";

// // Add the limit
// $sql .= " LIMIT " . $start . ", " . $length;

// // Execute the query
// $stmt = $conn->prepare($sql);

// if (!empty($search)) {
//     $stmt->bindValue(':search', '%'.$search.'%');
// }

// $stmt->execute();

// // Create an array to store the data
// $data = $stmt->fetchAll();

// // Get the total number of records
// $total_sql = "SELECT count(*)  as c FROM `redis_numbers` ";
// $stmt = $conn->prepare($total_sql);
// $stmt->execute();
// $total_data = $stmt->fetch();
// $total_records = $total_data['c'];

// // Close the database connection
// $conn = null;

// // Build the response
// $response = array(
//     "draw" => intval($_GET['draw']),
//     "recordsTotal" => $total_records,
//     "recordsFiltered" => $total_records,
//     "data" => $data,
//     "columns" =>$columns
// );

// // Send the response as JSON
// echo json_encode($response);

// }
// catch (Exception $e) {
//     echo 'Caught exception: ',  $e->getMessage(), "\n";
//     echo $sql;
//     var_dump($_POST);
   
// }



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


$sql = "SELECT COUNT(*) as total_data FROM `redis_numbers`";
$query=mysqli_query($conn, $sql) or die("getData.php: get employees");
$totalData = mysqli_fetch_array($query);
$totalData = $totalData[0];

$searchValue = $requestData['search']['value'];


$sql = "SELECT  * FROM `redis_numbers` ";
if( !empty($searchValue) ) {  
    $sql .=" WHERE phone_number LIKE '%".$searchValue."%' ";    
    $sql .=" OR country_code LIKE '%".$searchValue."%' ";
    $sql .=" OR source LIKE '%".$searchValue."%' ";
    $sql .=" OR taked LIKE '%".$searchValue."%' ";
}


$start = $requestData['start'];
$length = $requestData['length'];





$sql .= " ORDER BY `redis_numbers`.`taked_time` DESC  LIMIT ".$start.",".$length." ;";

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
    $nestedData[] = $row["taked_time"];
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


