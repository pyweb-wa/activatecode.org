<?php
ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:login.php');
    die();
}
require_once "../backend/config.php";
function getusersids(){
    $query = "SELECT id FROM `users`  WHERE admin_id = ".$_SESSION['id'];
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $logs = $stmt->fetchall();
    $ids = array();
    foreach ($logs as $log) {
    if (isset($log['id'])) {
        $ids[] = $log['id'];
    }
}
    return $ids;
}
function gethistory()
{
    $query = "SELECT COUNT(*) as count,`Name`,country_name,DATE_FORMAT(TimeStmp,'%Y-%m-%d') as idate FROM `requests_log` rq JOIN foreignapiservice fs on rq.service = fs.Id_Service_Api WHERE SMSCode IS NOT NULL and Id_user IN (".implode(",",getusersids()).") GROUP by `Name`,country_name,idate";
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}
$hist = gethistory();


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
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href=" assets/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/22.2.6/css/dx.light.css" />

    <style>
        .even.selected td {
            background-color: #e74a3b !important;
            color: white !important;
        }

        .odd.selected td {
            background-color: #e74a3b !important;
            color: white !important;
            /* Add !important to make sure override datables base styles */
        }
    </style>

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
                <div class="container-fluid"> 
                    <div class="card shadow mt-3">                    
                        <div class="card-body">
                            <div class="row">
                            <div id="gridContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php
            include 'includes/footer.php';
            ?>

        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>


    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/dataTables.buttons.min.js"></script>
    <!-- <script src="assets/js/buttons.bootstrap4.min.js"></script> -->
    <!-- <script src="assets/js/jszip.min.js"></script> -->
    <!-- <script src="assets/js/pdfmake.min.js"></script> -->
    <!-- <script src="assets/js/vfs_fonts.js"></script> -->
    <!-- <script src="assets/js/buttons.html5.min.js"></script>
    <script src="assets/js/buttons.print.min.js"></script>
    <script src="assets/js/buttons.colVis.min.js"></script> -->

    <script src="assets/js/theme.js"></script>
    <script src="assets/js/toastr.min.js"></script>



    <script src="assets/js/dataTables.select.js"></script>
    <script src="assets/js/dataTables.altEditor.free.js"></script>
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
            },
            summary: {
                groupItems: [{
                    column: "count",
                    summaryType: "sum"
                }]
            }
        });
    });
</script>
<script>
    $(document).ready(function () { 
        $("#nav_main_title").text("Application Log History");
    });
    $('#nav_item_app_log_his').addClass("active");
</script>

</body>

</html>