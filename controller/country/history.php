<?php
require_once "validate_token.php";
if (!checkTokenInDatabase()) {
    header('Location: index.php');
    exit();
}

include '/var/www/smsmarket/html/backend/config.php';
if (!isset($_GET['code'])) {
    die();
}
if ($_GET['code'] != "azxsdcfrevgtrrd4fg3") {
    die();
}

function gethistory()
{
    $query = "SELECT COUNT(*) as count,`Name`,country_name,DATE_FORMAT(TimeStmp,'%Y-%m-%d') as idate FROM `requests_log` rq JOIN foreignapiservice fs on rq.service = fs.Id_Service_Api WHERE Id_user!= 19 and Id_user!=28 and SMSCode IS NOT NULL  and `TimeStmp` >= DATE_SUB(CURDATE(), INTERVAL 3 DAY) GROUP by `Name`,country_name,idate";
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}

function gethistory2()
{
    $query = "SELECT COUNT(*) as count,`Name`,country_name,DATE_FORMAT(TimeStmp,'%Y-%m-%d') as idate FROM `requests_log` rq JOIN foreignapiservice fs on rq.service = fs.Id_Service_Api WHERE Id_user in (19 ,28) and SMSCode IS NOT NULL  and `TimeStmp` >= DATE_SUB(CURDATE(), INTERVAL 3 DAY) GROUP by `Name`,country_name,idate";
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}
$hist = gethistory();
$hist2 = gethistory2();
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title" id="title">Application Log History Main</h3>
                            <button type="button" class="btn btn-warning" onclick="getOther('main')">Main</button>
                            <button type="button" class="btn btn-primary" onclick="getOther('')">Other Users</button>
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
    <!-- </div> -->
    <!-- ./wrapper -->
    <!-- </section> -->
    <!-- /.content -->
    <!-- </div> -->


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
            columns: ['Name', 'country_name', 'idate', 'count'],
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
            },
            summary: {
                groupItems: [{
                    column: "count",
                    summaryType: "sum"
                }]
            }
        });
    });
function getOther(type){
    var titleElement = document.getElementById('title');
    console.log(type)
    let histro = "";
    if(type == 'main'){
        console.log("in main")
         histro = '<?php echo json_encode($hist); ?>';
        titleElement.textContent = 'Application Log History Main';

    }
    else{
        console.log("in other")
         histro = '<?php echo json_encode($hist2); ?>';
        titleElement.textContent = 'Application Log History Others';

    }
    console.log(histro)
        $('#gridContainer').dxDataGrid({
            dataSource: JSON.parse(histro),
            columns: ['Name', 'country_name', 'idate', 'count'],
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
            },
            summary: {
                groupItems: [{
                    column: "count",
                    summaryType: "sum"
                }]
            }
        });
}
</script>

</html>