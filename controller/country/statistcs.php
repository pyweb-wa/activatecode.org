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

// try {
//     // connect to the database
//     $dsn = "mysql:host=localhost;dbname=smsdb";

//     $username = 'mixsimverify';

//     $password = 'SMS@mixsimverify.com';
//     $charset = 'utf8';

//$conn = new PDO($dsn, $username, $password);
// set the PDO error mode to exception
//$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// execute the SELECT query
//$stmt = $conn->query("SELECT * FROM `bananaapi-number` order by id DESC");


// retrieve the rows of the result set
//    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// close the cursor and connection
//  $stmt->closeCursor();
//$conn = null;
// } catch(PDOException $e) {
//     echo "Connection failed: " . $e->getMessage();
// }

// get the column names from the first row
//$columns = array_keys($data[0]);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Numbers</title>
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
            <section class="content" >
            <div class="container mt-5">
                <table id="table_id" class="table table-striped table-info">

                    <thead>
                        <tr>
                            <th>id</th>
                            <th>phone_number</th>
                            <th>country_code</th>
                            <th>taked</th>
                            <th>source</th>
                            <th>application</th>
                            <th>createdTime</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>id</th>
                            <th>phone_number</th>
                            <th>country_code</th>
                            <th>taked</th>
                            <th>source</th>
                            <th>application</th>
                            <th>createdTime</th>
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
                    [6, "desc"]
                ],
                "info": true,
                "dom": '<"top"f><"top"lp>rt<"bottom"lp><"top"f><"clear">',
                ajax: 'getData.php',
            });
        });
    </script>

</body>

</html>