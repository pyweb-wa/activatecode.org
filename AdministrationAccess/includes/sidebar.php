<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-danger p-0">
    <div class="container-fluid d-flex flex-column p-0">
        <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#">
            <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-sms"></i></div>
            <div class="sidebar-brand-text mx-3"><span>ActivateGode </span></div>
        </a>
        <hr class="sidebar-divider my-0">
        <ul class="nav navbar-nav text-light" id="accordionSidebar">
            <!-- <li class="nav-item " role="presentation"><a id="nav_item_dashboard" class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li> -->


            <li class="nav-item" role="presentation"><a id="nav_item_customer" class="nav-link" href="users.php"><i class="far fa-address-card"></i><span>Customers</span></a></li>

            <li class="nav-item" role="presentation"><a id="nav_item_customer_prop" class="nav-link" href="customer_prop.php"><i class="far fa-address-card"></i><span>Customers Properties </span></a></li>
            <li class="nav-item" role="presentation"><a id="nav_item_reports" class="nav-link" href="report.php"><i class="far fa-user-circle"></i><span>Reports</span></a></li>
            <?php if ($_SESSION["is_super"] == 1) { ?>
                <!-- <li class="nav-item"  role="presentation"><a id="nav_item_customer_perm" class="nav-link" href="customerPermissions.php"><i class="far fa-address-card"></i><span>Customers Access Permissions </span></a></li>  -->
                <li class="nav-item" role="presentation"><a id="nav_item_usr_rate" class="nav-link" href="users_rate.php"><i class="fas fa-hand-holding-usd"></i><span>Users Rate</span></a></li>
                <li class="nav-item" role="presentation"><a id="nav_item_usr_cntry_perm" class="nav-link" href="customerCountry.php"><i class="fas fa-hand-holding-usd"></i><span>Users Country Permission</span></a></li>
                <li class="nav-item" role="presentation"><a id="nav_item_supervisor" class="nav-link" href="admins.php"><i class="far fa-address-card"></i><span>Supervisors</span></a></li>
                <li class="nav-item" role="presentation"><a id="nav_item_sup_usr" class="nav-link" href="admin_customer.php"><i class="far fa-address-card"></i><span>Supervisors-Users</span></a></li>
                <li class="nav-item" role="presentation"><a id="nav_item_sms_supplier" class="nav-link" href="fapis.php"><i class="far fa-handshake"></i><span>SMS Suppliers</span></a></li>
                <li class="nav-item" role="presentation"><a id="nav_item_price" class="nav-link" href="pricing.php"><i class="fas fa-hand-holding-usd"></i><span>Pricing</span></a></li>
                <li class="nav-item" role="presentation"><a id="nav_item_supplier_priority" class="nav-link" href="suppliers_priority.php"><i class="far fa-handshake"></i><span>Suppliers Priority</span></a></li>
                <li class="nav-item" role="presentation"><a id="nav_item_app" class="nav-link" href="services.php"><i class="far fa-paper-plane"></i><span>Applications</span></a></li>

                <!-- <li class="nav-item"  role="presentation"><a id="nav_item_user_country_acc" class="nav-link" href="userToCountry.php"><i class="fas fa-hand-holding-usd"></i><span>Users Countries Access</span></a></li> -->
                <!-- <li class="nav-item"  role="presentation"><a id="nav_item_billing_rep" class="nav-link" href="billing.php"><i class="fas fa-hand-holding-usd"></i><span>billing Report</span></a></li> -->
            <?php } //else { echo '<li class="nav-item"  role="presentation"><a id="nav_item_customer" class="nav-link" href="available.php"><i class="far fa-address-card"></i><span>Available Apps</span></a></li> ';}
            ?>
            <!-- <li class="nav-item" role="presentation"><a id="nav_item_customer" class="nav-link" href="available.php"><i class="far fa-address-card"></i><span>Available Apps</span></a></li> -->
            <!-- <li class="nav-item" role="presentation"><a id="nav_item_customer" class="nav-link" href="stats_numbers.php"><i class="far fa-address-card"></i><span>Numbers Statistics</span></a></li> -->
            <!-- <li class="nav-item" role="presentation"><a id="nav_item_customer" class="nav-link" href="countrycards.php"><i class="far fa-address-card"></i><span>Country Numbers Control</span></a></li> -->
            <!-- <li class="nav-item" role="presentation"><a id="nav_item_customer" class="nav-link" href="recieved_sms.php"><i class="far fa-address-card"></i><span>Recieved SMS</span></a></li>
            <li class="nav-item" role="presentation"><a id="nav_item_customer" class="nav-link" href="uploaded_numbers.php"><i class="far fa-address-card"></i><span>Uploaded Numbers</span></a></li> -->
            <li class="nav-item" role="presentation"><a id="nav_item_app_log_his" class="nav-link" href="historylog.php"><i class="far fa-address-card"></i><span>Application Log History</span></a></li>
            <li class="nav-item" role="presentation"><a id="nav_item_login_logger" class="nav-link" href="login_logger.php"><i class="far fa-address-card"></i><span>Login Logger</span></a></li>
            <!-- <li class="nav-item" role="presentation"><a class="nav-link" href="register.html"><i class="fas fa-user-circle"></i><span>Register</span></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" href="forgot-password.html"><i class="fas fa-key"></i><span>Forgotten Password</span></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" href="404.html"><i class="fas fa-exclamation-circle"></i><span>Page Not Found</span></a></li>
            <li class="nav-item" role="presentation"><a class="nav-link" href="blank.html"><i class="fas fa-window-maximize"></i><span>Blank Page</span></a></li> -->
        </ul>
        <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
    </div>
</nav>