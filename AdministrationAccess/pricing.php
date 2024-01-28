<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel" || $_SESSION["is_super"] != 1) {
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
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="assets/css/selectize.min.css">
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

        #Select_user_view .selectize-input {
            height: 40px !important;
            /* Set the height of the input element to 40 pixels */
            line-height: 40px;
            /* Set the line-height of the input element to 40 pixels */
        }

        .bootstrap-select .dropdown-menu {
            max-height: 150px;
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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="text-primary font-weight-bold m-0">Edit Price</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row m-2">
                                        <div class="col" style="margin-right: 25px;">
                                            <div class="row">
                                                <div class="col-7">
                                                    <h6 style="float: left;">Select Applications:</h6>
                                                </div>
                                                <div class="col-3">
                                                    <a href="#" onclick="selectall('app_list')"
                                                        style="float: right;"><u><i>select all</i></u></a>
                                                </div>
                                                <div class="col-2">
                                                    <a href="#" onclick="clear_List('app_list')"
                                                        style="float: right;color: red;"><u><i>clear</i></u></a>
                                                </div>
                                            </div>
                                            <div class="row"><select id="app_list" multiple></select></div>
                                        </div>

                                        <div class="col">
                                            <div class="row">
                                                <div class="col-7">
                                                    <h6 style="float: left;">Select Country:</h6>
                                                </div>
                                                <div class="col-3">
                                                    <a href="#" onclick="selectall('country_list')"
                                                        style="float: right;"><u><i>select all</i></u></a>
                                                </div>
                                                <div class="col-2">
                                                    <a href="#" onclick="clear_List('country_list')"
                                                        style="float: right;color: red;"><u><i>clear</i></u></a>
                                                </div>
                                            </div>
                                            <div class="row"><select id="country_list" multiple></select></div>
                                        </div>
                                        <br>
                                        <div class="input-group mt-4 mb-4">
                                            <input type="number" id="price" class="form-control">
                                            <div class="input-group-append">
                                                <span class="input-group-text">$</span>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row mainrow">
                                        <div class="col">
                                        </div>
                                        <div class="col">
                                        </div>
                                        <div class="col text-right">
                                            <button type="button" onclick="update_price()" class="btn btn-success"
                                                id="update">Update Price</button>
                                        </div>
                                    </div>



                                </div>
                            </div>
                            <div class="card shadow mt-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <p class="text-primary m-0 font-weight-bold">All APP Prices</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="table-responsive table mt-2" id="dataTable1" role="grid"
                                                aria-describedby="dataTable_info">
                                                <table class="table table-striped table-bordered dt-responsive wrap"
                                                    id="dataTable">

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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="assets/js/selectize.min.js"></script>
        <script src="assets/js/_pricing.js"></script>
        <script>
    $(document).ready(function () { 
        $("#nav_main_title").text("Application Pricing");
    });
    $('#nav_item_price').addClass("active");
</script>
</body>

</html>