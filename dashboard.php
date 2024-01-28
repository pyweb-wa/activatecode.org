<?php
ob_start();
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location:login.php');
    die();
}
// echo($_SESSION['api_key']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Brand</title>
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap-select.min.css">
    <link rel="stylesheet" href=" assets/css/toastr.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php
include 'user_includes/sidebar.html';
?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php
include 'user_includes/navbar.php';
?>
                <?php
include 'user_includes/indexmain.php';
?>
            </div>
            <?php
include 'user_includes/footer.php';
?>

        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap-select.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/toastr.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap-table.min.js"></script>
    <script src="assets/js/indexMain.js"></script>
           
</body>

</html>