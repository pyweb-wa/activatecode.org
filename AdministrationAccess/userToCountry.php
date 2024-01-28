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
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.17/dist/css/bootstrap-select.min.css"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">


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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">

                            <div class="card  col-lg-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="text-primary font-weight-bold m-0">User Countries Access</h6>

                                </div>
                                <div class="card-body">
                                    <div class="input-group mt-4 mb-4">
                                        <select id="users" class="form-control selectpicker" data-live-search="true" name="users[]" multiple>
                                            <option value="-" id="loading">Loading</option>
                                        </select>
                                    </div>
                                    <br>
                                    <!-- <select class="selectpicker show-tick" id="countries" data-live-search="true"> -->
                                    <select id="countries" class="form-control selectpicker" data-live-search="true" name="countries[]" multiple>
                                        <option value="-" id="loading">Loading</option>
                                    </select>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <button type="button" onclick="update()" class="btn btn-success" id="update">Update</button>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <button type="button" onclick="clearcountries()" class="btn btn-primary">Clear</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-7">
                                <p>To get started, ensure that you include 'country=any' in your 'getnumber' API request. This will enable you to select which countries to provide to the user. If you choose multiple countries, the system will randomly select one of the chosen countries. Additionally, in the future, you can change the selected country in real-time, even if the user is already using the service. Finally, note that if you choose 'null' only or 'null' along with other countries, the system will remove any previously selected countries. </p>
                                <div class="card-body bg-white">
                                    <table id="myTable" class="table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Countries</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- data will be added dynamically -->
                                        </tbody>
                                    </table>
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
        <script src="assets/js/_countriesAccess.js"></script>
</body>

</html>