<?php
require_once "validate_token.php";
if (!checkTokenInDatabase()) {
    header('Location: index.php');
    exit(); 
}
if (!isset($_GET['action']) || $_GET['action'] != "display") {
    echo "o";
    die();
}
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

    <link rel="stylesheet" href="./css/daterangepicker.css">
    <link rel="stylesheet" href="./css/adminlte.min.css">

</head>
<style>
    .table-container {
        max-height: 400px;
        /* Adjust as needed */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    .table-container thead th {
        position: sticky;
        /* Makes the table headers sticky */
        top: 0;
        /* Sticks the headers to the top of the container */
    }

    .styled-table,
    .styled-table th,
    .styled-table td {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Arial', sans-serif;
        color: #333;
        border: 1px solid #ddd;
    }

    .styled-table th,
    .styled-table td {
        padding: 0px;
        border-bottom: 1px solid #ddd;
        width: 20%;
        /* Divides the table equally into three columns */
        text-align: center; 
        /* Centers the text within the columns */
        font-size: 0.8em;
        /* Adjust as needed */
    }

    .styled-table th {
        font-size: 0.8em;
        /* Make the header font size slightly larger */
        background-color: #4CAF50;
        color: white;
    }

    .styled-table tr:hover {
        background-color: #f5f5f5;
    }

    .styled-table tr:nth-child(odd) {
        background-color: white;
    }

    /* Style for light green rows */
    .styled-table tr:nth-child(even) {
        background-color: #e2ffe2;
    }
    .alert-red {
    background-color: red;
    color: white;
    }

    .alert-white {
        background-color: white;
    }
    
    .alert-green {
        background-color: #e2ffe2;
    }
    @keyframes flashdang {
        0%, 50%, 100% {
            background-color: white;
            color: black;
        }
        25%, 75% {
            background-color: red;
            color: white;
        }
    }
    @keyframes flashwarn {
        0%, 50%, 100% {
            background-color: white;
            color: black;
        }
        25%, 75% {
            background-color: orange;
            color: white;
        }
    }
    .flash-alert-dang {
        animation: flashdang 1s infinite;
    }
    .flash-alert-war {
        animation: flashwarn 1s infinite;
    }



</style>
<body class="hold-transition sidebar-mini" ng-app="countryapp" ng-controller="CountryController">
    <div class="wrapper">
        <?php include "menu.php";?>


        <div class="content-wrapper">
            <section class="content" ng-init='getdata();autoreactivatestatus();get_load()'>
                <div class="card card-solid">
                    <div class="card-body pb-0">
                    <div class="row mb-3">

                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>Server loads</th>
                                <th>{{ serv_loads[0].redis_key.split(':')[1].toUpperCase() }}</th>
                                <th>{{ serv_loads[1].redis_key.split(':')[1].toUpperCase() }}</th>
                                <th>{{ serv_loads[2].redis_key.split(':')[1].toUpperCase() }}</th>
                                <th>{{ serv_loads[3].redis_key.split(':')[1].toUpperCase() }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Mysql Threads</td>
                            <td ng-class="{ 
                                        'alert-white': serv_loads[0].threads_connected <= 70,
                                        'flash-alert-war': serv_loads[0].threads_connected > 70 && serv_loads[0].threads_connected < 90,
                                        'flash-alert-dang': serv_loads[0].threads_connected >= 90
                                        }">
                                {{ serv_loads[0].threads_connected }}
                            </td>
                            <td ng-class="{ 
                                        'alert-white': serv_loads[1].threads_connected <= 70,
                                        'flash-alert-war': serv_loads[1].threads_connected > 70 && serv_loads[1].threads_connected < 90,
                                        'flash-alert-dang': serv_loads[1].threads_connected >= 90
                                        }">
                                {{ serv_loads[1].threads_connected }}
                            </td>
                            <td ng-class="{ 
                                        'alert-white': serv_loads[2].threads_connected <= 70,
                                        'flash-alert-war': serv_loads[2].threads_connected > 70 && serv_loads[2].threads_connected < 90,
                                        'flash-alert-dang': serv_loads[2].threads_connected >= 90
                                        }">
                                {{ serv_loads[2].threads_connected }}
                            </td>
                            <td ng-class="{ 
                                        'alert-white': serv_loads[3].threads_connected <= 70,
                                        'flash-alert-war': serv_loads[3].threads_connected > 70 && serv_loads[3].threads_connected < 90,
                                        'flash-alert-dang': serv_loads[3].threads_connected >= 90
                                        }">
                                {{ serv_loads[3].threads_connected }}
                            </td>
                        </tr>
                        <tr>
                            <td>Nginx Threads</td>
                            <td ng-class="{ 
                                        'alert-green': serv_loads[0].active_connections <= 200,
                                        'flash-alert-war': serv_loads[0].active_connections > 200 && serv_loads[0].active_connections < 250,
                                        'flash-alert-dang': serv_loads[0].active_connections >= 250
                                        }">
                                {{ serv_loads[0].active_connections }}
                            </td>
                            <td ng-class="{ 
                                        'alert-green': serv_loads[1].active_connections <= 200,
                                        'flash-alert-war': serv_loads[1].active_connections > 200 && serv_loads[1].active_connections < 250,
                                        'flash-alert-dang': serv_loads[1].active_connections >= 250
                                        }">
                                {{ serv_loads[1].active_connections }}
                            </td>
                            <td ng-class="{ 
                                        'alert-green': serv_loads[2].active_connections <= 200,
                                        'flash-alert-war': serv_loads[2].active_connections > 200 && serv_loads[2].active_connections < 250,
                                        'flash-alert-dang': serv_loads[2].active_connections >= 250
                                        }">
                                {{ serv_loads[2].active_connections }}
                            </td>
                            <td ng-class="{ 
                                        'alert-green': serv_loads[3].active_connections <= 200,
                                        'flash-alert-war': serv_loads[3].active_connections > 200 && serv_loads[3].active_connections < 250,
                                        'flash-alert-dang': serv_loads[3].active_connections >= 250
                                        }">
                                {{ serv_loads[3].active_connections }}
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    </div>
                        <div class="row mb-3" id="card-container">
                            <div ng-repeat="country in countries" class="col-12 col-sm-6 col-md-3 d-flex align-items-stretch flex-column">
                                <div class="card bg-light d-flex flex-fill">
                                    <div class="card-header  text-muted border-bottom-0 d-flex" ng-class="country.available == '0' ? 'bg-danger':((country.available <= 500 )? 'bg-warning':'bg-primary') ">
                                        <h2 class="lead text-white "> <b>{{country.country}} {{country.country_code}}_{{country.country_char}} {{country.application}} </b></h2>
                                        <div class="col-5 text-right ml-auto">
                                        <a href="JavaScript:void(0)" ng-click="delete(country)" class="btn  btn-sm  btn-danger ml-auto">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="JavaScript:void(0)" ng-click="SaveConfig(country)" class="btn ml-2 py-0 px-2  bg-teal btn-lg   ">
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
                                                    <div class="input-group date " id="timepicker{{country.country}}_{{country.source}}" data-target-input="nearest">
                                                        <input ng-change="datachanged()" ng-model="country.start" name="start{{country.country}}_{{country.source}}" type="text" class="form-control timepicker" data-target="#stoptimepicker{{country.country}}_{{country.source}}" />
                                                        <div class="input-group-append" data-target="#stoptimepicker{{country.country}}_{{country.source}}" data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <!-- text input -->
                                                <div class="form-group">
                                                    <label>STOP</label>
                                                    <div class="input-group date " id="timepicker{{country.country}}_{{country.source}}" data-target-input="nearest">
                                                        <input ng-change="datachanged()" ng-model="country.stop" type="text" class="form-control timepicker" name="stop{{country.country}}_{{country.source}}" data-target="#starttimepicker{{country.country}}_{{country.source}}" />
                                                        <div class="input-group-append" data-target="#starttimepicker{{country.country}}_{{country.source}}" data-toggle="datetimepicker">
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
                                                        <input ng-change="datachanged()" ng-model="country.Mstart" type="number" class="form-control " name="MStart{{country.country}}_{{country.source}}" />
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
                                                <a href="JavaScript:void(0)" ng-click="powertoggle(country)" id="power{{country.country}}_{{country.source}}" class="btn  btn-sm  " ng-class='country.status == "0"?"btn-danger":"bg-teal" '>
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


</body>
<!-- jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.4/dayjs.min.js"></script>
<script src="./js/timepicker-bs4.js"></script>
<script src="./js/toastr.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-resource.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular-sanitize.js"></script>
<script src="./js/angular-file-saver.bundle.min.js"></script>
<script src="./js/adminlte.min.js"></script>
<!-- <script src="./FileSaver.js"></script> -->
<script src="./js/CountryController.js"></script>


</html>