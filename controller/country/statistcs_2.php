<?php
require_once "validate_token.php";
if (!checkTokenInDatabase()) {
    header('Location: index.php');
    exit(); 
}

if (!isset($_GET['code'])) {
    die();
}
if ($_GET['code'] != "azxsdcfrevgtrrd4fg3") {
    die();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>SMS Code</title>
    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/adminlte.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css" />
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css" /> -->
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.js"></script> -->

</head>

<body class="hold-transition sidebar-mini" ng-app="countryapp" ng-controller="CountryController">
    <div class="wrapper">
        <?php include "menu.php"; ?>


        <div class="content-wrapper">
            <section class="content">
                <div class="container mt-5">
                    <table id="table_id" class="table table-striped table-info">

                        <thead>
                            <tr>
                                <th>id</th>
                                <th>phone_number</th>
                                <th>timestamp</th>
                                <th>sms</th>
                                <th>code</th>
                                <th>Sender_n</th>
                                <th>taked</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>id</th>
                                <th>phone_number</th>
                                <th>timestamp</th>
                                <th>sms</th>
                                <th>code</th>
                                <th>Sender_n</th>
                                <th>taked</th>
                            </tr>
                        </tfoot>



                    </table>
                </div>

            </section>
            <!-- /.control-sidebar -->
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.2.0
            </div>
            <strong>Copyright &copy; 2015-2023 <a href="#">ProDev.io</a>.</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->
    <!-- /.content -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/bootstrap.bundle.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-resource.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-sanitize.js"></script>
    <script src="./js/angular-file-saver.bundle.min.js"></script>
    <script src="./js/adminlte.min.js"></script>
    <!-- <script src="./FileSaver.js"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
 

    <script>
        $(document).ready(function() {
            $('#table_id').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 20,
                "order": [
                    [0, "desc"]
                ],
                "info": true,
                "dom": '<"top"f><"top"lp>rt<"bottom"lp><"top"f><"clear">',
                ajax: 'getData2.php',
            });
        });
    </script>

</body>

</html>