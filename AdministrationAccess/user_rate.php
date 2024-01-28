<?php

ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:login.php');
    die();
}
require_once './../backend/config.php';
function getusers(){
    $sql="SELECT u.name,u.email,u.registration_date,a.Name as adminname,a.email as adminmail FROM `users` u JOIN cms_users a on u.admin_id = a.Id_User";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}
$users = getusers();
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
    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
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
                    <!-- <h3 class="text-dark mb-4">Admin - Customers</h3> -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Users Rate</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="table-responsive table mt-2" id="dataTable1" role="grid" aria-describedby="dataTable_info">
                                    <div id="gridContainer"></div>
                                </div>
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
    <script src="https://cdn3.devexpress.com/jslib/22.2.6/js/dx.all.js"></script>

    <script src="assets/js/theme.js"></script>
    <script >
         
        let histro = '<?php echo json_encode($users); ?>';
        $(() => {
            $('#gridContainer').dxDataGrid({
                dataSource: JSON.parse(histro),
                columns: [
                    {caption: 'admin name',dataField: 'adminname'},
                    {caption: 'admin email',dataField: 'adminmail'},
                    'name',
                    'email',
                    'registration_date'],
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
                rowAlternationEnabled: true
            });
        });

        </script>

</body>

</html>