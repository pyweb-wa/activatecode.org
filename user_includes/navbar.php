<?php

if (!isset($_SESSION)) {
    if (!isset($_SESSION['user_email'])) {
        #TODO replace login.php with simmixverify.com in all pages check sessions
        header('Location:login.php');
        die();
    }
}
$name = $_SESSION["name"];

?>
<nav class="navbar navbar-light navbar-expand bg-white shadow mb-4 topbar static-top">
    <div class="container-fluid"><button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop" type="button"><i class="fas fa-bars"></i></button>

        <ul class="nav navbar-nav flex-nowrap ml-auto">



            <div class="d-none d-sm-block topbar-divider"></div>
            <li class="nav-item dropdown no-arrow" role="presentation">
                <div class="nav-item dropdown no-arrow">
                    <a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="false" href="#">
                        <span class="d-none d-lg-inline mr-2 text-gray-600 small"><?php echo $_SESSION['name']; ?></span>
                        <img class="border rounded-circle img-profile" src="assets/img/client.png"></a>

                    <div class="dropdown-menu shadow dropdown-menu-right animated--grow-in" role="menu">
                        <!-- <a class="dropdown-item" role="presentation" href="#">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Profile</a> -->
                        <a class="dropdown-item" role="presentation" href="setting.php"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Settings</a>

                        <!-- <a class="dropdown-item" role="presentation" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Activity log</a> -->

                        <div class="dropdown-divider"></div><a class="dropdown-item" role="presentation" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>&nbsp;Logout</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>