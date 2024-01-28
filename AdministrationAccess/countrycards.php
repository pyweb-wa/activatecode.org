<?php
ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:login.php');
    die();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Country - Cards</title>
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/toastr.min.css">
    <link rel="stylesheet" href="assets/css/daterangepicker.css">
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
                <div class="container-fluid" ng-app="countryapp" ng-controller="CountryController">

                  
                <section class="content" ng-init='getdata();autoreactivatestatus();'>
                <div class="card card-solid">
                <div class="card-header py-3">
            <div class="col-2"><button ng-click="autoreactivate()"  class="btn btn-block btn-primary btn-flat" id="autoreactivatebutton">Auto Reactivate</button> </div>
                        </div>
                    <div class="card-body pb-0">
                        <div class="row mb-3" id="card-container">
                            <div ng-repeat="country in countries" class="col-12 col-sm-6 col-md-3 d-flex align-items-stretch flex-column">
                                <div class="card bg-light d-flex flex-fill">
                                    <div class="card-header  text-muted border-bottom-0 d-flex" ng-class="country.available == '0' ? 'bg-danger':((country.available <= 500 )? 'bg-warning':'bg-primary') ">
                                        <h5 class="lead text-white "> <b>{{country.country}} {{country.country_code}}_{{country.country_char}} </b></h5>
                                        <div class="col-6 text-right ml-auto">
                                        <a href="JavaScript:void(0)" ng-click="delete(country)" class="btn  btn-sm  btn-danger ml-auto">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="JavaScript:void(0)" ng-click="SaveConfig(country)" class="btn ml-2 py-0 px-2  btn-success btn-lg   ">
                                            <i class="fas fa-save"></i>
                                        </a>
                                        </div>

                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                                    <li class="nav-item font-weight-bold .text-primary"><span class="fa-li"><i class="fa fa-bullseye"></i></span>Source: {{country.source}} </li>
                                                    <li class="nav-item"><span class="fa-li"><i class="fa fa-bars"></i></span>Total: {{country.total}} </li>
                                                    <li class="nav-item"><span class="fa-li"><i class="fa fa-home"></i></span>Available:{{ country.available}} </li>
                                                    <li class="nav-item"><span class="fa-li"><i class="fas fa-phone"></i></span> Requested:{{ country.requested }}</li>
                                                    <li class="nav-item"><span class="fa-li"><i class="fas fa-sms"></i></span> Has Message: {{country.has_sms}} </li>
                                                    <li class="nav-item"><span class="fa-li"><i class="fas fa-ban"></i></span> No sms recived: {{country.no_sms}} </li>

                                                </ul>
                                                <i class="fa fa-clock " aria-hidden="true"></i> <span class="font-weight-bold"> Server Time:</span> <br>
                                                <h6>{{country.servertime}} </h6>
                                                <i class="fa fa-clock " aria-hidden="true"></i> <span class="font-weight-bold"> Push Time:</span> <br>
                                                <h6>{{country.pushtime}} </h6>
                                            </div>
                                            <!-- <div class="col-6 align-middle pb-5">
                                                <i class="fa fa-clock " aria-hidden="true"></i> <span class="font-weight-bold"> Server Time:</span> <br>
                                                <h6>{{country.servertime}} </h6>
                                            </div> -->
                                        </div>
                                        <div class="row  mt-2">


                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>START</label>
                                                    <div class="input-group date " id="timepicker{{country.country}}" data-target-input="nearest">
                                                        <input ng-change="datachanged()" ng-model="country.start" name="start{{country.country}}" type="text" class="form-control timepicker" data-target="#stoptimepicker{{country.country}}" />
                                                        <div class="input-group-append" data-target="#stoptimepicker{{country.country}}" data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <!-- text input -->
                                                <div class="form-group">
                                                    <label>STOP</label>
                                                    <div class="input-group date " id="timepicker{{country.country}}" data-target-input="nearest">
                                                        <input ng-change="datachanged()" ng-model="country.stop" type="text" class="form-control timepicker" name="stop{{country.country}}" data-target="#starttimepicker{{country.country}}" />
                                                        <div class="input-group-append" data-target="#starttimepicker{{country.country}}" data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row  mt-2">


                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>START In Minutes</label>
                                                    <div class="input-group " data-target-input="nearest">
                                                        <input ng-change="datachanged()" ng-model="country.Mstart" type="number" class="form-control " name="MStart{{country.country}}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <!-- text input -->
                                                <div class="form-group">
                                                    <label>STOP In Minutes</label>
                                                    <div class="input-group " data-target-input="nearest">
                                                        <input ng-change="datachanged()" ng-model="country.Mstop" type="number" class="form-control " name="Mstop{{country.country}}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between">
                                            <div class="text-left"><span class="badge " ng-class="country.status == '1' ? 'badge-primary' : 'badge-danger'">{{country | status}}</span></div>
                                            <div class="text-right" id="power">
                                                <a href="JavaScript:void(0)" ng-click="powertoggle(country)" id="power{{country.country}}" class="btn  btn-sm  " ng-class='country.status == "0"?"btn-danger":"btn-success" '>
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                                <a href="JavaScript:void(0)" class="btn btn-sm btn-primary" ng-click="download(country)">
                                                    <i class="fas fa-download"></i> </a>
                                                    <a href="JavaScript:void(0)" class="btn btn-sm btn-warning" ng-click="reactivate(country)">
                                                    <i class="fas fa-retweet"></i> </a>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>


                <div class="modal fade" id="modal-delete">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Are You Sure ?</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                            </div>
                            <div class="modal-footer  d-flex">
                                <button type="button" class="btn btn-default mr-auto p-2" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-danger p-2" ng-click="deleteCountry('N')">Delete Number</button>
                                <button type="button" class="btn btn-danger p-2" ng-click="deleteCountry('NB')">Delete Number & Box</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->
            </section>


















                </div>

            </div>
            <?php
            include 'includes/footer.php';
            ?>

        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>


    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/buttons.bootstrap4.min.js"></script>
    <!-- <script src="assets/js/jszip.min.js"></script> -->
    <!-- <script src="assets/js/pdfmake.min.js"></script> -->
    <!-- <script src="assets/js/vfs_fonts.js"></script> -->

    <script src="assets/js/timepicker-bs4.js"></script>
    <script src="assets/js/toastr.min.js"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-resource.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-sanitize.js"></script>
    <script src="assets/js/angular-file-saver.bundle.min.js"></script>
    
    
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/CountryController.js"></script>

</body>

</html>