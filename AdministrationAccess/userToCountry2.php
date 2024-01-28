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
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/toastr.min.css">

    <link rel="stylesheet" href="assets/css/dx.common.css">
    <link rel="stylesheet" href="assets/css/dx.light.css">
    <link rel="stylesheet" href="assets/css/selectize.min.css"> 
</head>
<style>
    .selectize-control {
        width: 100%;
    }

    .selectize-input {
        height: 100px;
        overflow: auto;
    }

    .selectize-dropdown-content {
        max-height: 300px;
    }

    .mainrow {
        display: flex;
        flex-wrap: wrap;
        margin: unset;
        padding: 15px;
    }
</style>

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
                <div class="row mainrow">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="text-primary font-weight-bold m-0">Update User Permissions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col" style="margin-right: 25px;">
                                        <div class="row">
                                            <div class="col">
                                                <h6 style="float: left;">Select users:</h6>
                                            </div>
                                            <div class="col">
                                                <a href="#" onclick="clear_List('users')"  style="float: right;"><u><i>clear</i></u></a>
                                            </div>                                                
                                        </div>
                                        <div class="row"><select id="users" multiple></select></div>
                                    </div>

                                    <div class="col" style="margin-right: 25px;">
                                        <div class="row">
                                            <div class="col">
                                                <h6 style="float: left;">Select API:</h6>
                                            </div>
                                            <div class="col">
                                                <a href="#" onclick="clear_List('api_list')"  style="float: right;"><u><i>clear</i></u></a>
                                            </div>                                                
                                        </div>
                                        <div class="row"><select id="api_list" multiple></select></div>
                                    </div>

                                    <div class="col">
                                        <div class="row">
                                            <div class="col">
                                                <h6 style="float: left;">Select Countries:</h6>
                                            </div>
                                            <div class="col">
                                                <a href="#" onclick="clear_List('countries_list')"  style="float: right;"><u><i>clear</i></u></a>
                                            </div>                                                
                                        </div>
                                        <div class="row"><select id="countries_list" multiple></select></div>
                                    </div>
                                </div>
                                <div class="row mainrow">
                                    <div class="col text-left">
                                        <button type="button" onclick="" class="btn btn-danger">Reset Selected User</button>
                                    </div>
                                    <div class="col"> 
                                    </div>
                                    <div class="col"> 
                                    </div>                                    
                                    <div class="col text-right">
                                        <button type="button" onclick="update()" class="btn btn-success" id="update">Update Selected User</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                

                </div>
                <div class="row">


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
        <script src="assets/js/toastr.min.js"></script>
        <script src="assets/js/_countriesAccess2.js"></script>
        <script src="assets/js/dx.all.js"></script>
        <script src="assets/js/selectize.min.js"></script>
</body>

</html>