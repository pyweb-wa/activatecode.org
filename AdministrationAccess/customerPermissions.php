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
                                <h6 class="text-primary font-weight-bold m-0">Update Customers Permissions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col" style="margin-right: 25px;">
                                        <div class="row">
                                            <div class="col">
                                                <h6 style="float: left;">Select users:</h6>
                                            </div>
                                            <div class="col">
                                                <a href="#" onclick="clear_List('users')" style="float: right;"><u><i>clear</i></u></a>
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
                                                <a href="#" onclick="clear_List('api_list')" style="float: right;"><u><i>clear</i></u></a>
                                            </div>
                                        </div>
                                        <div class="row"><select id="api_list" single></select></div>
                                    </div>

                                    <div class="col">
                                        <div class="row">
                                            <div class="col">
                                                <h6 style="float: left;">Select Countries:</h6>
                                            </div>
                                            <div class="col">
                                                <a href="#" onclick="clear_List('countries_list')" style="float: right;"><u><i>clear</i></u></a>
                                            </div>
                                        </div>
                                        <div class="row"><select id="countries_list" multiple></select></div>
                                    </div>
                                </div>
                                <div class="row mainrow">
                                    <div class="col text-left">
                                        <button type="button" onclick="clearUserPermissions()" class="btn btn-danger">Reset Selected User</button>
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


                <div class="row mainrow" style="display: none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="text-primary font-weight-bold m-0">Customer Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col" style="margin-left: 15px;">
                                        <div class="row">
                                            <h6 style="float: left;">Select Customer</h6>
                                        </div>
                                        <div class="row">
                                            <select id="user_select" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-primary">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">

                                        <div class="row" style="place-content: center;">
                                            <div class="form-group">

                                                <label>Current Balance</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" min="0" step="0.01" class="form-control" id="current_balance" disabled>
                                                </div>
                                                <label>Amount Recieved</label> &nbsp; <label style="color:red">*</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fas fa-dollar-sign"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" min="0" step="0.01" class="form-control" id="amount" onkeypress="return isNumberKey(this, event);">
                                                </div>
                                                <label>Gift</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="fas  fa-gift"></i>
                                                        </span>
                                                    </div>
                                                    <input type="number" min="0" step="0.01" class="form-control" id="gift" onkeypress="return isNumberKey(this, event);">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-warning" id="customerRecieveBtn" onclick="submit()">Submit</button> -->
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label>New Password</label> &nbsp; <label style="color:red">*</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </span>
                                                </div>
                                                <input class="form-control" id="new_pass">
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-warning" id="new_pass_submit" onclick="pass_submit()">Submit</button> -->

                                    </div>
                                </div>
                                <div class="row" style="float: right;padding-right: 15px;">
                                    <button type="button" class="btn btn-outline-success btn-lg" id="new_pass_submit" onclick="SaveCustomerSettings()">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mainrow">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="text-primary font-weight-bold m-0">View Customers Permissions</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="card-body">
                                        <div class="table-responsive table mt-2" id="dataTablewrap" role="grid" aria-describedby="dataTable_info">
                                            <table class="table table-striped table-bordered dt-responsive" id="myTable">
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mainrow">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="text-primary font-weight-bold m-0">Customers Recharge History</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <h6 style="float: left;">Select Customer</h6>
                                </div>
                                <div class="row">
                                    <select id="user_select_his" class="form-control selectpicker" data-live-search="true" data-style="btn-outline-primary">
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="card-body">
                                        <div class="table-responsive table mt-2" id="dataTablewrap" role="grid" aria-describedby="dataTable_info">
                                            <table class="table table-striped table-bordered dt-responsive" id="table_cus_recharge">
                                            </table>
                                        </div>
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
        <script src="assets/js/dataTables.buttons.min.js"></script> -
       <script src="assets/js/buttons.bootstrap4.min.js"></script> 
        <script src="assets/js/jszip.min.js"></script>
        <!-- <script src="assets/js/pdfmake.min.js"></script> -->
        <!-- <script src="assets/js/vfs_fonts.js"></script> -->
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