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
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/css/toastr.min.css">
    <link rel="stylesheet" href="assets/css/dx.common.css">
    <link rel="stylesheet" href="assets/css/dx.light.css">
    <link rel="stylesheet" href="assets/css/selectize.min.css">

</head>
<style>
     #ttable {
        width: 100%;
    }    
    #ttable th, #ttable td {
        width: auto;
        text-align: center;
    }
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
    #progress {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 1000;
    }
    #progress i {
        font-size: 2em;
        margin-right: 10px;
    }
    .hidden {
        display: none !important;
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
                                <div class="row" style="width:100%">
                                    <div class="col">
                                        <h6 class="text-primary font-weight-bold m-0">Customer/Permissions table</h6>
                                    </div>
                                    <div class="col">
                                        <div id="progress" style="text-align: end;" class="hidden">
                                            <i class="fas fa-spinner fa-spin"></i> Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div id="table-container" class="col">
                                        <div class="table-responsive">
                                            <input type="text" id="customerSearch" class="form-control" placeholder="Search by Customer">
                                            <div id="divtbl"></div>
                                        </div>
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
        <!-- Modal -->

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style="max-width: 720px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Select Country</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span id="modalUserName" hidden></span>
                        <div class="row">
                            <div class="col-10">
                                <input style="width: 100%;" type="text" id="countrySearch" placeholder="Search by Country Name or Country Code" oninput="filterCountries()">
                            </div>
                            <div class="col">
                                <button class="btn btn-primary btn-sm" onclick="updateCountry()">Save</button>
                            </div>
                        </div>

                        <div style="padding-top:20px" id="modalCountryButtons"></div>
                        <!-- Add other information as needed -->
                    </div>
                </div>
            </div>
        </div>




        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/buttons.bootstrap4.min.js"></script>
        <script src="assets/js/theme.js"></script>
        <script src="assets/bootstrap/js/bootstrap-select.min.js"></script>
        <script src="assets/js/_countriespermession.js"></script>
        <script src="assets/js/dx.all.js"></script>
        <script src="assets/js/selectize.min.js"></script>
        <script>
            $(document).ready(function () { 
                $("#nav_main_title").text("Customer Country Permissions");
            });
            $('#nav_item_usr_cntry_perm').addClass("active");
        </script>
</body>

</html>