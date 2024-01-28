<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel" || $_SESSION["is_super"] != 1) {
    header('Location:login.php');
    die();
}

function gethistory()
{
    include '/var/www/smsmarket/html/backend/redisconfig.php';
    $liveRedisKey = "users_rate:live";
    $oldRedisKey = "users_rate:old";
    $live_data = $redis->get($liveRedisKey);
    $live_data = json_decode($live_data, true);
    $old_data = $redis->get($oldRedisKey);
    $old_data = json_decode($old_data, true);
    $mergedArray = array_merge($live_data, $old_data);
    //$mergedArray = json_encode($mergedArray);
    return $mergedArray;
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
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/22.2.6/css/dx.light.css" />

</head>



<body id="page-top">
    <div id="wrapper">
        <?php include 'includes/sidebar.php';  ?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php include 'includes/navbar.php';  ?>
                <div class="container-fluid">

                    <div class="content-wrapper">
                        <section class="content">
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">  
                                        <p class="text-primary font-weight-bold m-0">Users Rate</p>
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

                    <?php include 'includes/footer.php'; ?>
                </div>
                <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
            </div>

        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn3.devexpress.com/jslib/22.2.6/js/dx.all.js"></script>
<script>
    $(() => {
        let histro = '<?php echo json_encode($hist); ?>';
        $('#gridContainer').dxDataGrid({
            dataSource: JSON.parse(histro),
            columns: [
                'user',
                'date',
                'country',
                'total',
                'has_sms',
                {
                    dataField: 'percentage',
                    caption: 'Percentage',
                    customizeText: function(cellInfo) {
                        if (cellInfo.value !== undefined) {
                            return cellInfo.value.toFixed(2) + '%';
                        }
                        return '';
                    }
                }
            ],
            showBorders: true,
            grouping: {
                autoExpandAll: true,
            },
            searchPanel: {
                visible: true,
            },
            paging: {
                pageSize: 15,
            },
            groupPanel: {
                visible: true,
            },onInitialized: function(e) {
                console.log("Grid initialized", e);
            },
            onOptionChanged: function(e) {
                console.log("Option changed", e);
            },
            summary: {
                groupItems: [{
                    column: "has_sms",
                    summaryType: "sum"
                }]
            }
        });
    });
</script>
<script>
    $(document).ready(function () { 
        $("#nav_main_title").text("User Rate History");
    });
    $('#nav_item_usr_rate').addClass("active");
</script>

</html>