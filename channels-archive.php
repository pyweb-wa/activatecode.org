<?php
session_start();
if (!isset($_SESSION['user_email'])) {
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
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href=" assets/css/toastr.min.css">
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


        #loading {
  position: fixed;
  display:none ;
  justify-content: center;
  align-items: center;
  text-align: center;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  opacity: 0.7;
/* background-color: #fff; */
  z-index: 99;
  /* top: 50%; */
  left: 20%;
}


#loading-image {
    justify-content: center;
  align-items: center;
  text-align: center;
  position: absolute;
  top: 100px;
  left: 240px;
  z-index: 100;
  
}


    </style>

</head>

<body id="page-top">
    <div id="wrapper">
        <?php
include 'user_includes/sidebar.html';
?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php
include 'user_includes/navbar.php';
?>


                <div class="container-fluid">
                <div id="loading">
                <img id="loading-image" src="assets/img/loader.gif" alt="Loading..."  />
             </div>
<!-- 
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Request channels</p>
                        </div>
                        <div class="card-body">
                            <div class="row">

                            </div>



                        </div>
                    </div> -->
                </div>


               

                <div class="container-fluid">
                    <!-- <h3 class="text-dark mb-4">Your Channels</h3> -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Downloaded Channels</p>
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
include 'user_includes/footer.php';
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
    <script src="assets/bootstrap/js/bootstrap-select.min.js"></script>
    <script src="assets/js/toastr.min.js"></script>

    <script src="assets/js/theme.js"></script>



    <script src="assets/js/dataTables.select.js"></script>
    <script src="assets/js/dataTables.altEditor.free.js"></script>
    <script src="assets/js/_channels_archive.js"></script>

</body>

</html>