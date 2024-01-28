<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location:login.php');
    die();
}
if ($_SESSION['id'] != 52) {
    header('Location:dashboard.php');
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
      <div id="loading">
                <img id="loading-image" src="assets/img/loader.gif" alt="Loading..."  />
             </div>

                <div class="container-fluid">
              
                <div class="col-md-12 align-items-stretch">
                                <div class="card  h-60  shadow">
                                    <div class="card-header py-2">
                                        <p class="text-primary m-0 font-weight-bold">Upload Channels File</p>
                                    </div>
                                    <div class="card-body">
                                        <div id="drop-area"   >
                                            <form class="my-form" action="clientfxn/order_upload.php" method="post" enctype="multipart/form-data">
                                                <p>Upload channels </p>
                                                <!-- onchange="handleFiles(this.files)" -->
                                                <input type="file" id="fileElem" name="fileElem" accept=".txt" >
                                                <!-- <label class="button2 btn-success" for="fileElem">Select a file</label> -->
                                                <input type="submit" value="Convert" name="submit">
                                            </form>
                                            <div id="gallery">
                                           
                                            </div>
                                           
                                            
                                          
                                             
                                        </div>
                                    </div>

                                    <div class="card-footer"> </div>
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



    <script src="assets/js/_convert.js"></script>

</body>

</html>