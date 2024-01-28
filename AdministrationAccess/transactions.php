<?php
session_start();
$month=null;
$year=null;

if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:login.php');
    die();
} 
include './transfxn/trans.php'; 
$trans=[];
$datefilter=0;
$title="Customers";

if (isset($_GET['CustomerID']))
{
    if ( (int)($_GET['CustomerID'])!=0){ 
        $custId=(int)($_GET['CustomerID']);
        $trans=get_trans_by_user($custId);
    }
}
else if (isset($_GET['apiID']))
{
    if ( (int)($_GET['apiID'])!=0){ 
        $apiid=(int)($_GET['apiID']);
        $trans=get_trans_by_api($apiid);
        $title="Suppliers";
    }
}

else   
{
  
     if(isset($_GET['month']) && isset($_GET['year']))
     {
        $month=(int)($_GET['month']);
        $year=(int)($_GET['year']); 
     }
    if($year==null ){$year=date("Y");}
    if($month==null ){$month=date("m");}
    $trans=get_trans_by_date($month,$year);
    $datefilter=1;
    $title="Monthy Transactions $month - $year";
        
     
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
        <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
        <style>
            .even.selected td {
                background-color: #e74a3b;
                !important;
                color: white;
            }
            
            .odd.selected td {
                background-color: #e74a3b;
                !important;
                color: white;
                /* Add !important to make sure override datables base styles */
            }
        </style>

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
                            <div class="container-fluid">
                                <h3 class="text-dark mb-4"><?php echo($title);?></h3>
                                <div class="card shadow">
                                    <div class="card-header py-3">
                                        <p class="text-primary m-0 font-weight-bold"><?php echo($title);?></p>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="table-responsive table mt-2" id="dataTable1" role="grid" aria-describedby="dataTable_info">
                                                <table class="table table-striped table-bordered dt-responsive nowrap" id="dataTable">
                                                    <thead>
                                                        <tr>
                                                            <th>tid</th>
                                                            <?php if($datefilter==1){echo('<th>Type</th>');} ?>
                                                            <th>Contact</th>
                                                            <th>debit</th>
                                                            <th>credit</th>
                                                            <th>description</th>
                                                            <th>date</th>
                                                            <th>notes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody> 
                                                            <?php 
                                                           
                                                            foreach ($trans as $tr) {
                                                                echo("<tr>");
                                                                echo("<td>".$tr['tid']." </td>");
                                                                if($datefilter==1){echo("<td>".$tr['type']." </td>");}
                                                                echo("<td>".$tr['name']." </td>");
                                                                echo("<td>".$tr['debit']." </td>");
                                                                echo("<td>".$tr['credit']." </td>");
                                                                echo("<td>".$tr['description']." </td>");
                                                                echo("<td>".$tr['date']." </td>");
                                                                echo("<td>".$tr['notes']." </td>");
                                                                echo("</tr>");
                                                            }
                                                            ?> 
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>



                                    </div>
                                </div>
                            </div>

                    </div>
                    <?php
            include 'includes/footer.php';
            ?>

                </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>


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
        <script src="assets/js/dataTables.select.js"></script>
        <script> 
        $(document).ready(function() { $('#dataTable').DataTable();} );
     
        </script>
    </body>

    </html>