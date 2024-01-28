<?php
ob_start();
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location:login.php');
    die();
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Brand</title>
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.17/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php
        include 'user_includes/sidebar.html';
        $site = "http://api.activatecode.org/";
        ?>
        <div class="d-flex flex-column" id="content-wrapper">

            <div id="content">
                <?php include 'user_includes/navbar.php'; ?>
                <div class="container-fluid">

                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">API Documentation</p>
                        </div>
                        <div class="card-body">
                            <!-- <div class="mt-2  border border-ligh rounded mb-0">

                                <h2>get applications</h2>

                                <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=getapplist</code>
                            </div> -->

                            <div class="mt-4  border border-ligh rounded mb-0">
                                <h2>get balance</h2>

                                <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=getbalance</code>
                                <br>
                                <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                    <h5>Response</h5>

                                    <code>
                                        {"ResponseCode":0,"Msg":"OK","Result":{"balance":"50.00"}}
                                    </code>

                                </div>
                            </div>

                            <div class="mt-4  border border-ligh rounded mb-0">
                                <h2>get Bulk numbers</h2>


                                <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=getbulk_numbers&appcode=[appcode]&country=[country]&count=10</code>
                                <br>
                                <p>  The minimum count number is 10.</p>
                                <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                               
                                    <h5>Response</h5>
                                   
                                    <code>
                                    {"ResponseCode":0,"Msg":"OK","Result":[{"id":13412043,"Number":93785847878},{"id":13412046,"Number":93783107003},{"id":13412049,"Number":93730372022},{"id":13412052,"Number":93739082131},{"id":13412055,"Number":93780176929},{"id":13412058,"Number":93788371424},{"id":13412061,"Number":93737934600},{"id":13412064,"Number":93736748444},{"id":13412067,"Number":93734194851},{"id":13412070,"Number":93785818064}],"App":"wa","Cost":"1.00","Balance":290.7,"CountryCode":93}
                                    </code>
                                    
                                </div>
                                <p>
                                
                                      </p>
                            </div>

                           <div class="mt-4  border border-ligh rounded mb-0">
                            <h2>Callback Settings</h2>
    
                            <p>You can include a callback URL in your setting page. Additionally, you must click the enable button to activate it. Once you add the callback URL and enable it, our system will automatically send you all your SMS without the need to use a getcode request.</p>

                            <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                            <h5>Request Format:</h5>
                            <!-- <pre> -->
                                <code>
                                    {
                                    "id": 15652659,
                                    "phone_number": 212681075870,
                                    "sms": "Your WhatsApp code: 128-961. You can also tap on this link to verify your phone: v.whatsapp.com/128961. Don't share this code with others",
                                    "application": "whatsapp",
                                    "code": "128961"
                                    }
                                </code>
                            <!-- </pre> -->
                                </div>
                                <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                            <h5>Your Response:</h5>
                            <!-- <pre> -->
                                <code>
                                    {
                                    "status": "OK"
                                    }
                                </code>
                            <!-- </pre> -->
                                </div>
                                </div>
                            <div class="mt-4  border border-ligh rounded mb-0">
                                <h2>get number</h2>


                                <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=getnumber&appcode=[appcode]&country=[country]</code>
                                <br>
                                <p>  applist and countrys avilable in getapplist request</p>
                                <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                    <h5>Response</h5>
                                 
                                    <code>
                                        {"ResponseCode":0,"Msg":"OK","Result":{"id":"58","Number":"84327790731","App":"wa","Cost":"0.12","Balance":49.88}}
                                    </code>

                                </div>

                            </div>
                            <div class="mt-4  border border-ligh rounded mb-0">

                                <h2>get code</h2>
                                <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=getcode&id=[id]</code>

                                <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                    <h5>Response</h5>

                                    <code>
                                        {"ResponseCode":0,"Msg":"OK","Result":{"SMS":"
                                        <#> Your WhatsApp code: 123456 You can also tap on this link to verify your phone: v.whatsapp.com/123456 Don't share this code with others 4sgLq1p5sV6","Code":"123456"}}
                                    </code>

                                    <p class="mt-2">
                                        Waiting for sms:
                                    </p>
                                    <code>
                                        {"ResponseCode":0,"Msg":"waiting for sms","Result":null}
                                    </code>

                                </div>

                                <!-- <div class="mt-4  border border-ligh rounded mb-0">

                                    <h2>get rest number</h2>
                                    <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=restnumber</code>

                                    <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                        <h5>Response</h5>

                                        <code>
                                            {"ResponseCode":0,"Msg":"OK","Result":[{"geo":"GH","count":780},{"geo":"EG","count":74},{"geo":"CG","count":1},{"geo":"ID","count":8579},{"geo":"IN","count":835},{"geo":"KH","count":3},{"geo":"RU","count":33},{"geo":"NG","count":2316},{"geo":"KE","count":0},{"geo":"CM","count":6},{"geo":"PE","count":41},{"geo":"HN","count":0},{"geo":"TZ","count":13},{"geo":"ET","count":17},{"geo":"ZM","count":55}]}
                                        </code>



                                    </div> -->
                                    <div class="mt-4  border border-ligh rounded mb-0">


                                <h2>get available number</h2>
                                    <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=get_available</code>
                                
                                    <h6><p> The status flag indicates whether the county is enabled or not. The status can change within minutes, and it is necessary to monitor the status flag every 20 minutes. If the flag is set to 0, there is no need to request numbers from this country.</p> </h6>
                                <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                    <h5>Response</h5>

                                    <code>
                                    {"ResponseCode":0,"Msg":"OK","Result":[{"count":"43905","application":"whatsapp","country_code":"SD","app_code":"wa","status":0,"country":"Sudan","price_out":1},{"count":"27","application":"whatsapp","country_code":"GH","app_code":"wa","status":0,"country":"Ghana","price_out":1},{"count":"12886","application":"whatsapp","country_code":"YE","app_code":"wa","status":0,"country":"Yemen","price_out":1},{"count":"15934","application":"whatsapp","country_code":"SY","app_code":"wa","status":1,"country":"Syria","price_out":1},{"count":"21","application":"whatsapp","country_code":"NG","app_code":"wa","status":0,"country":"Nigeria","price_out":1},{"count":"18600","application":"whatsapp","country_code":"MA","app_code":"wa","status":1,"country":"Morocco","price_out":1},{"count":"16730","application":"whatsapp","country_code":"IQ","app_code":"wa","status":1,"country":"Iraq","price_out":1}]}
                                    </code>



                                   </div>

                                    <!-- <div class="mt-4  border border-ligh rounded mb-0">

                                        <h2>get channel</h2>
                                        <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=getchannels&country=IN&count=1</code>

                                        <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                            <h5>Response</h5>

                                            <samp>
                                                {"ResponseCode":0,"Msg":"OK","Result":{"id":"11","Count":1,"country":"","Cost":0.3,"Balance":1567}}
                                            </samp>



                                        </div>


                                        <h2>download channel</h2>
                                        <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=down_result&id=11</code>

                                        <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                            <h5>Response</h5>

                                            <samp>
                                                {"Code":0,"Data":[{"a":"rO0ABXVyAAJbQqzzF_gGCFTgAgAAeHAAAAAqAAKlCsvOLDVD81sNsaFVsCDbkRP-NvTvh3U2MNMz_UoeXadZZQAfk1ho","b":"rO0ABXNyAA9jb20ud2hhdHNhcHAuTWXk6K3RrOBlqgIAA0wAAmNjdAASTGphdmEvbGFuZy9TdHJpbmc7TAAJamFiYmVyX2lkcQB-AAFMAAZudW1iZXJxAH4AAXhwdAACOTJ0AAw5MjMwMzI2NzQ2OTN0AAozMDMyNjc0Njkz","c":"PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0ndXRmLTgnIHN0YW5kYWxvbmU9J3llcycgPz4KPG1hcD4KICAgIDxsb25nIG5hbWU9ImNsaWVudF9zdGF0aWNfa2V5cGFpcl9lbmNfc3VjY2VzcyIgdmFsdWU9IjEiIC8-CiAgICA8c3RyaW5nIG5hbWU9ImNsaWVudF9zdGF0aWNfa2V5cGFpcl9lbmMiPlswLCZxdW90O2NEQmh2blpodXIxeHg0YUF0MjQ0WjhGT2RjZkFRb21renZsZVNMaUxzSExLMzlvWTlqQnltUHB4SWs0ZkVncnY0eUQxRG1UR0ZCUFhiNVJMVmFhVTN6SklsUEY0cUxwUVZydXpWTUd6Q1VZJnF1b3Q7LCZxdW90OzVzTzQ3U2haMStpTWphcGEmcXVvdDtdPC9zdHJpbmc-CiAgICA8c3RyaW5nIG5hbWU9InNlcnZlcl9zdGF0aWNfcHVibGljIj54RG42TXFCUG4zTzZwdERoUFF0L3RxY1hydjJkSzdhUi8vTlFMRklWYWwwPC9zdHJpbmc-CiAgICA8aW50IG5hbWU9ImNsaWVudF9hbmRyb2lkX2tleV9zdG9yZV9hdXRoX3ZlciIgdmFsdWU9IjEiIC8-CiAgICA8Ym9vbGVhbiBuYW1lPSJjYW5fdXNlcl9hbmRyb2lkX2tleV9zdG9yZSIgdmFsdWU9InRydWUiIC8-CiAgICA8c3RyaW5nIG5hbWU9ImNsaWVudF9zdGF0aWNfa2V5cGFpcl9wd2RfZW5jIj5bMiwmcXVvdDs0ejA3dnZsU3dROFdjSk1HTjVISFZ4Tm81bGNNOE1zd05SZHp2K3h5YU5JKzJMdzdCaGVXMUF5blMzT3RNQjhQaGhqTzdkM1JNcVY1VGVjY1g2YW9pZyZxdW90OywmcXVvdDtZeEViYWwzV0tidGJyamJZcVZFTW5nJnF1b3Q7LCZxdW90O3hRR2VBUSZxdW90OywmcXVvdDt1d29JZzZyN1A5XC83QkZtSG14d0FLQSZxdW90O108L3N0cmluZz4KPC9tYXA-Cg","d":"PK","e":null}]}
                                            </samp>



                                        </div>


                                        <h2>download hashs</h2>
                                        <code> <?php echo $site; ?>backend/out_interface.php?api_key=<?php echo $_SESSION['api_key']; ?>&action=down_hashs&id=1</code>

                                        <div class="p-3 mb-2 bg-light text-dark rounded mb-2">
                                            <h5>Response</h5>

                                            <samp>
                                                y982nksLJ2dWIfFgrwIQ6N7vc442m+32NDO7EzltfDU=,XePf1fMmu/Dy1495U1SC0PfkKIHHn4HVEqCVDst3+6U=
                                                +bhhmR5S57oUJRc5maGarK5WQThQLsutbthNE42w5fI=,/L4bqZPgKapdMl/b/haOOh/zG5k4P/Gfd9hCMqr3j9U=
                                                xgY0z/QJhQ5F9EZUg42BOeotdb7eM+EjmetVwo+0RGE=,yGF0e/T1LUNe0SE/vjShoAHn+y3VFA/i3qBK0+hcqEE=
                                            </samp>



                                        </div>


                                    </div>
                                </div> -->
                            </div>



                            <div class="mt-4  border border-ligh rounded mb-0 col-6 float-right">
                                <h2>Country List</h2>

                                <div class="table-responsive table mt-2" id="dataTablewrap" role="grid" aria-describedby="dataTable_info">
                                        <table class="table table-striped table-bordered dt-responsive nowrap" id="dataTablecountry">

                                        </table>
                                    </div>
                            </div>
                            <div class="mt-4  border border-ligh rounded mb-0 col-6">
                                <h2>Applications List</h2>

                                <div class="table-responsive table mt-2" id="dataTablewrap" role="grid" aria-describedby="dataTable_info">
                                        <table class="table table-striped table-bordered dt-responsive nowrap" id="dataTableapps">

                                        </table>
                                    </div>
                            </div>




                        </div>

                    </div>


                    <?php
                    include 'user_includes/footer.php';
                    ?>

                </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
            </div>
            <script src="assets/js/jquery.min.js"></script>
            <script src="assets/bootstrap/js/bootstrap.min.js"></script>
            <script src="assets/js/jquery.dataTables.min.js"></script>
        <script src="assets/js/dataTables.bootstrap4.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.17/js/bootstrap-select.min.js"></script>
            <script src="assets/js/theme.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            <script src="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.js"></script>
            <script src="assets/js/documentation.js"></script>

</body>

</html>