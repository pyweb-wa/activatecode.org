<?php
require_once "config.php";
// require_once "validate_token.php";
// if (!checkTokenInDatabase()) {
//     header('Location: index.php');
//     exit(); 
// }

function gethistory()
{
    $query = " SELECT COUNT(*) as count,`Name`,country_name,DATE_FORMAT(TimeStmp,'%Y-%m-%d') as idate FROM `requests_log` rq JOIN foreignapiservice fs on rq.service = fs.Id_Service_Api GROUP by `Name`,country_name,idate";
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}
$hist = gethistory();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usage Statistics</title>


    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="./css/toastr.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/22.2.6/css/dx.light.css" />
    <link rel="stylesheet" href="./css/adminlte.min.css">

</head>

<body class="hold-transition sidebar-mini" ng-app="countryapp" ng-controller="CountryController">


    <?php include "menu.php"; ?>


    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Available Numbers</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div id="gridContainer"></div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
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
    </section>
    <!-- /.content -->
    </div>


</body>
<!-- jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./js/bootstrap.bundle.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.4/dayjs.min.js"></script> -->
<script src="./js/timepicker-bs4.js"></script>
<script src="./js/toastr.min.js"></script>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-resource.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-sanitize.js"></script>
<script src="./js/angular-file-saver.bundle.min.js"></script> -->
<script src="./js/adminlte.min.js"></script>
<!-- <script src="./FileSaver.js"></script> -->
<script src="https://cdn3.devexpress.com/jslib/22.2.6/js/dx.all.js"></script>

<script>
    $(() => {
        let histro = '<?php echo json_encode($hist); ?>';
        $('#gridContainer').dxDataGrid({
            dataSource: JSON.parse(histro),
            columns: ['Name', 'country_name', 'idate','count'],
            showBorders: true,
            grouping: {
                autoExpandAll: true,
            },
            searchPanel: {
                visible: true,
            },
            paging: {
                pageSize: 10,
            },
            groupPanel: {
                visible: true,
            }
        });
    });
</script>

</html>