<?php
ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);
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
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
    <style>
        .even.selected td {
            background-color: #e74a3b !important;
            color: white !important;
        }

        .odd.selected td {
            background-color: #e74a3b !important;
            color: white !important;
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
                    <div class="card shadow mt-3">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Customers info</p>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="table-responsive table mt-2" id="dataTable1" role="grid" aria-describedby="dataTable_info">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="dataTable">
                                        <!-- <table class="table dataTable my-0" id="dataTable"> -->
                                        <!-- table table-bordered table-striped dataTable  table-dark-->
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
    <!-- <script src="assets/js/jszip.min.js"></script> -->
    <!-- <script src="assets/js/pdfmake.min.js"></script> -->
    <!-- <script src="assets/js/vfs_fonts.js"></script> -->
    <script src="assets/js/buttons.html5.min.js"></script>
    <script src="assets/js/buttons.print.min.js"></script>
    <script src="assets/js/buttons.colVis.min.js"></script>

    <script src="assets/js/theme.js"></script>



    <script src="assets/js/dataTables.select.js"></script>
    <script src="assets/js/dataTables.altEditor.free.js"></script>
    <script src="assets/js/_usersEditors.js"></script>
    <script>
        $(document).ready(function () { 
            $("#nav_main_title").text("Customers");
        });
    </script>
</body>

</html>