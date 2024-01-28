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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.17/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include 'user_includes/sidebar.html';  ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php include 'user_includes/navbar.php';  ?>
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Customer Setting</h3>
                    <!-- ************************************************* -->

                    <!--end of row -->
                    <!-- ************************************************* -->

                    <div class="row">

                        <div class="col-md-6  align-items-stretch">
                            <div class="card  h-100 shadow">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 font-weight-bold">Token Setting</p>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>access token</label> &nbsp; <label style="color:red">*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-universal-access"></i>
                                                </span>
                                            </div>
                                            <input class="form-control" id="access_token" readonly>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="renew_token()">renew</button>
                                </div>

                                <div class="card-footer"> </div>
                            </div>
                        </div>


                        <div class="col-md-6  align-items-stretch">
                            <div class="card  h-100 shadow">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 font-weight-bold">Password Reset</p>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>old Password</label> &nbsp; <label style="color:red">*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-key"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" id="old_pass" required>
                                        </div>
                                        <label>New Password</label> &nbsp; <label style="color:red">*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-key"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" id="new_pass" placeholder="Password" required>
                                        </div>
                                        <label>Password confirmation</label> &nbsp; <label style="color:red">*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-key"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" id="confirm_pass" placeholder="Confirm Password" required>
                                        </div>
                                        <!-- <span id="msg">123</span> -->
                                    </div>
                                    <button type="button" class="btn btn-primary" id="new_pass_submit" onclick="change_password()">Submit</button>
                                </div>

                                <div class="card-footer"> </div>
                            </div>
                        </div>
                    </div>

                    <!-- ************************************************* -->

                    <div class="row" style="margin-top:10px">
                        <div class="col-md-6  align-items-stretch">
                            <div class="card  h-100 shadow">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 font-weight-bold" style="float: left;">CallBack URL</p>
                                    <div class="custom-control custom-switch" style="float: right;">
                                        <input type="checkbox" class="custom-control-input" id="customSwitches" checked>
                                        <label class="custom-control-label" for="customSwitches">sevice on</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>enter your callback url to receive sms on it</label> &nbsp; <label style="color:red">*</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-universal-access"></i>
                                                </span>
                                            </div>
                                            <input class="form-control" id="call_back_url">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="test_url()">Check</button>
                                    <button type="button" style="float: right;" class="btn btn-danger" onclick="clear_url()">reset</button>
                                </div>

                                <div class="card-footer"> </div>
                            </div>
                        </div>
                    </div>
                    <!--end of row -->
                    <!-- ********************************************** -->


                    <!-- ******************************************* -->

                </div>
                <?php include 'user_includes/footer.php'; ?>
            </div>
            <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
        </div>
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.17/js/bootstrap-select.min.js"></script>
        <script src="assets/js/theme.js"></script>
        <script src="assets/js/toastr.min.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->
        <script src="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.js"></script>
        <script src="assets/js/setting.js"></script>
</body>

</html>