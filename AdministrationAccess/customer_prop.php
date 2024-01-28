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
        <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    </head>
    <!-- <style>
        body {
    height: 100%;
    margin: 0;
    font-family: "Lato", sans-serif;
    background-color: #f8f9fc;
}

#wrapper {
    display: flex;
}

#content-wrapper {
    flex: 1;
    overflow-y: auto;
}

.container-fluid {
    padding: 20px;
}

.sidebar {
    position: fixed;
    height: 100vh;
    z-index: 1;
    overflow-y: auto;
}

.topbar {
    z-index: 1000;
}

.navbar {
    position: fixed;
    width: 100%;
    z-index: 1000;
}

.navbar2 {
    z-index: 999;
}

.cust {
    padding: 20px;
    margin-top: 50px; /* Adjust this value based on the height of your top navbar */
    margin-left: 250px; /* Adjust this value based on the width of your sidebar */
}
    </style> -->

    <body id="page-top">
        <div id="wrapper">
            <?php include 'includes/sidebar.php'; ?>
            <div class="d-flex flex-column" id="content-wrapper">
                <div id="content">
                    <?php include 'includes/navbar.php'; ?>
                    <div class="container-fluid cust"> 
                        <!-- ************************************************* -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card shadow">
                                    <div class="card-header py-3">
                                        <div class="row" style="justify-content: center;">
                                            <p class="text-primary m-0 font-weight-bold">Your Balance:</p>
                                            <p class="text-primary m-0 ml-2 font-weight-bold" id="user_blnc_view">0</p>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <label>Select Customers:</label>
                                        <!-- <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                    <i class="far fa-user-circle"></i>
                                                </span>
                                                </div> -->
                                        <select id="user_select" class="form-control selectpicker" data-live-search="true"
                                            data-style="btn-warning">
                                            <option value="-" id="loading">Loading</option>
                                        </select>
                                        <!-- </div> -->
                                        <div class="row py-3">
                                            <div class="col-md-3  align-items-stretch">
                                                <div class="card  h-100 shadow">
                                                    <div class="card-header py-3">
                                                        <p class="text-primary m-0 font-weight-bold">Balance Refill</p>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="form-group">

                                                                <label>Current Balance</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            <i class="fas fa-dollar-sign"></i>
                                                                        </span>
                                                                    </div>
                                                                    <input type="number" min="0" step="0.01"
                                                                        class="form-control" id="current_balance" disabled>
                                                                </div>
                                                                <label>Amount Recieved</label> &nbsp; <label
                                                                    style="color:red">*</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            <i class="fas fa-dollar-sign"></i>
                                                                        </span>
                                                                    </div>
                                                                    <input type="number" min="0" step="0.01"
                                                                        class="form-control" id="amount"
                                                                        onkeypress="return isNumberKey(this, event);">
                                                                </div>
                                                                <?php if ($_SESSION["is_super"] == 1) { ?>
                                                                    <label>Gift</label>
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text">
                                                                                <i class="fas  fa-gift"></i>
                                                                            </span>
                                                                        </div>

                                                                        <input type="number" min="0" step="0.01"
                                                                            class="form-control" id="gift"
                                                                            onkeypress="return isNumberKey(this, event);">
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-warning"
                                                            id="customerRecieveBtn" onclick="submit()">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ******************************************** -->
                                            <div class="col-md-3  align-items-stretch">
                                                <div class="card  h-100 shadow">
                                                    <div class="card-header py-3">
                                                        <p class="text-primary m-0 font-weight-bold">Password Reset</p>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label>New Password</label> &nbsp; <label
                                                                style="color:red">*</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">
                                                                        <i class="fas fa-dollar-sign"></i>
                                                                    </span>
                                                                </div>
                                                                <input class="form-control" id="new_pass">
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-warning" id="new_pass_submit"
                                                            onclick="pass_submit()">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ******************************************** -->
                                            <?php if ($_SESSION["is_super"] == 1) { ?>
                                                <div class="col-md-6 align-items-stretch">
                                                    <div class="card  h-100  shadow">
                                                        <div class="card-header py-3">
                                                            <p class="text-primary m-0 font-weight-bold">Api Permissions</p>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive table mt-2" id="dataTable1" role="grid"
                                                                aria-describedby="dataTable_info">
                                                                <table
                                                                    class="table table-striped table-bordered dt-responsive nowrap"
                                                                    id="dataTable">
                                                                    <!-- <table class="table dataTable my-0" id="dataTable"> -->
                                                                    <!-- table table-bordered table-striped dataTable  table-dark-->
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mt-3">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 font-weight-bold">Recharge History</p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table mt-2" id="dataTablewrap" role="grid"
                                    aria-describedby="dataTable_info">
                                    <table class="table table-striped table-bordered dt-responsive nowrap" id="dataTable2">
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- ******************************************* -->
                    </div>
                </div>
                <?php include 'includes/footer.php'; ?>
            </div>
            <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
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
        <script src="assets/js/_userPermissions.js"></script>
        <script src="assets/js/_balance_refill.js"></script>
        <script>
            $(document).ready(function () {
                $("#nav_main_title").text("Customers Properties");
            });
        </script>
    </body>
</html>