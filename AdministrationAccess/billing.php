<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:login.php');
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Brand</title>
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap-select.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.17/dist/css/bootstrap-select.min.css"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">


</head>

<body id="page-top">
    <div id="wrapper">
        <?php
        include 'includes/sidebar.php';
        ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php
                include 'includes/navbar.php';
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">

                           
                            <div class="col-lg-7">
                            <div class="card-body bg-white"> 
                                        <table id="myTable" class="table"> 
                                        <tr>
                                        <th>date</th>
                                        <!-- <th>server_date</th> -->
                                        <th>Name</th>
                                        <th>total_request</th>
                                        <th>has_sms</th>

                                        </tr>
                             <?php 

                             include '../backend/config.php';
                             try {
                               
                               
                                //date_default_timezone_set('Asia/Beirut');


                                    // Define start and end dates
                                    $startDate = date('Y-m-d', strtotime('-3 days'));
                                    $endDate = date('Y-m-d');
                                    $id_list = array(17,19);
                                   // $hash = 0;
                                    // Iterate over date range
                                    foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
                                     $formattedDate    = $date->format('Y-m-d');
                                     $startOfDay = $formattedDate . ' 00:00:00';
                                     $endOfDay = $formattedDate . ' 23:59:59';
                                       
                                foreach($id_list as $id){

                                   
                                    $sql = 'SELECT `foreignapi`.`Name` AS Name, COUNT(*) AS total_request, COUNT(CASE WHEN requests_log.SMSCode IS NOT NULL THEN 1 END) AS has_sms FROM requests_log JOIN `foreignapi` ON `foreignapi`.`Id_Api` = ? JOIN `foreignapiservice` ON `foreignapiservice`.`Id_Foreign_Api` = `foreignapi`.`Id_Api` WHERE `foreignapiservice`.`Id_Service_Api` = requests_log.service AND requests_log.TimeStmp >= ? AND requests_log.TimeStmp <= ? GROUP BY `foreignapi`.`Name`;';

                                    if($id == 17){
                                      
                                         $sql = "SELECT `foreignapi`.`Name` AS Name, COUNT(*) AS total_request, COUNT(CASE WHEN requests_log.SMSCode IS NOT NULL THEN 1 END) AS has_sms FROM requests_log JOIN `foreignapi` ON `foreignapi`.`Id_Api` = ? JOIN `foreignapiservice` ON `foreignapiservice`.`Id_Foreign_Api` = `foreignapi`.`Id_Api` JOIN `bananaapi-number` ON `requests_log`.`Phone_Nb` = `bananaapi-number`.`phone_number` WHERE `foreignapiservice`.`Id_Service_Api` = requests_log.service AND requests_log.TimeStmp >= ? AND requests_log.TimeStmp <= ? AND `bananaapi-number`.`source` NOT LIKE '%hash%' GROUP BY `foreignapi`.`Name`;";
                                         
                                    }
                                    
                                    
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute([$id,$startOfDay,$endOfDay]);
                                    if ($stmt->rowCount() > 0) {
                                       
                                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                             echo "<tr>
                                             <td>" . $formattedDate . "</td>
                                            
                                             <td>" . $row["Name"] . "</td> 
                                             <td>" . $row["total_request"] . "</td>
                                             <td>" . $row["has_sms"] . "</td>
                                             </tr>";
                                          
                                        }
                                       
                                    } else {
                                     //   echo "No results found.";
                                    }
                                }
                            }
                                // Display the results in an HTML table
                              
                            } catch(PDOException $e) {
                                echo "Connection failed: " . $e->getMessage();
                            }
                        
                            // Close the database connection
                            $conn = null;
                            ?>


</thead> <tbody></tbody> </table>

                                

                        </div>


                    </div>
                </div>
                <?php
                include 'includes/footer.php';
                ?>

            </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
        </div>




        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/dataTables.bootstrap4.min.js"></script>
        <script src="assets/js/dataTables.buttons.min.js"></script>
        <script src="assets/js/buttons.bootstrap4.min.js"></script>
        <script src="assets/js/jszip.min.js"></script>
        <script src="assets/js/pdfmake.min.js"></script>
        <script src="assets/js/vfs_fonts.js"></script>
        <script src="assets/js/buttons.html5.min.js"></script>
        <script src="assets/js/buttons.print.min.js"></script>
        <script src="assets/js/buttons.colVis.min.js"></script>
        <script src="assets/js/theme.js"></script>
        <script src="assets/bootstrap/js/bootstrap-select.min.js"></script>
        <script src="assets/js/dataTables.select.js"></script>
        <script src="assets/js/dataTables.altEditor.free.js"></script>
        <!-- <script>  $('#nav_item_billing_rep').addClass("active"); </script> -->
</body>

</html>