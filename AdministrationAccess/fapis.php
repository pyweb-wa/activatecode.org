<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel" || $_SESSION["is_super"] != 1) {
    header('Location:index.php');
    die();
} ?>
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
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
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
                    <div class="card shadow mt-3">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">API Provider</p>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="table-responsive table mt-2" id="dataTable1" role="grid" aria-describedby="dataTable_info">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="dataTable">
                                        <!-- <table class="table dataTable my-0" id="dataTable"> -->
                                        <!-- table table-bordered table-striped dataTable  table-dark-->
                                    </table>


                                    <!-- ************************************************ -->
                                    <div class="container">
                                        <div class="modal fade" id="myModal" role="dialog">
                                            <div class="modal-dialog">

                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Recharge</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form role="form">
                                                            <div class="form-group">
                                                                <label for="api"><span class="glyphicon glyphicon-user"></span> API ID</label>
                                                                <input type="text" class="form-control" id="api_id_recharge_modal" disabled>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="api"><span class="glyphicon glyphicon-user"></span> API Name</label>
                                                                <input type="text" class="form-control" id="api_recharge_modal" disabled>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="api"><span class="glyphicon glyphicon-user"></span>Current Balance</label>
                                                                <input type="text" class="form-control" id="api_bal_recharge_modal" disabled>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Amount</label>
                                                                <input type="number" min="0" step="0.01" class="form-control" id="amount_recharge_modal" placeholder="Enter Amount in US dollar" onkeypress="return isNumberKey(this, event);">

                                                            </div>
                                                            <a onclick="insert_recharge()" class="btn btn-default text-white btn-success btn-block" data-dismiss="modal"><span class="glyphicon glyphicon-off"></span> Submit</a>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ************************************************* -->
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
    <script src="assets/js/dataTables.select.js"></script>
    <script src="assets/js/dataTables.altEditor.free.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="assets/js/_apisEditors.js"></script>
    <script>
    $(document).ready(function () { 
        $("#nav_main_title").text("SMS Suppliers");
    });
    $('#nav_item_sms_supplier').addClass("active");
</script>
</body>

</html>