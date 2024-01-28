<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 ">
            <!-- <div class="row">
                <div class="card  col-lg-12 ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-primary font-weight-bold m-0">Select Country</h6>

                    </div>
                    <div class="card-body">

                        <br />
                        <select class="selectpicker show-tick" id="carrier_list" data-live-search="true">
                            <?php

                            //     $carriers = getcarriers($countrys[0]['country_name']);
                            //     echo "<option value= 'any' data-content=\"<img class='' src='assets/img/carrier.jpg' width='24'></img> Any Carrier \"></option>";
                            //     foreach ($carriers as $carrier) {
                            //          echo "<option value= '" . $carrier['carrier'] . "' data-content=\"<img class='' src='assets/img/carrier.jpg' width='24'></img> " . $carrier['carrier'] . " \"></option>";
                            //  }
                                ?>
                        </select> 

                    </div>
                </div>
            </div> -->
            <div class="row">
                <div class="card shadow  col-lg-11 ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-primary font-weight-bold m-0">Available Applications</h6>

                    </div>
                    <div class="card-body">
                        <select class="selectpicker show-tick" id="country_list" data-live-search="true">
                            <?php
                                 $countrys = getavailablenumber();

                                 if($countrys){
                                foreach ($countrys as $country) {
                                    $country = rtrim($country);
                                    if(file_exists("assets/img/countrys-flags/png/" . str_replace(" ", "-", strtolower($country)) . ".png")){
                                    echo "<option value= '" . $country . "' data-content=\"<img class='' src='assets/img/countrys-flags/png/" . str_replace(" ", "-", strtolower($country)) . ".png'  width='24'  ></img> " . $country . " \"></option>";
                                    }
                                    else{
                                        echo "<option value= '" . $country . "' data-content=\"<img class='' src='assets/img/placeholder.png'  width='24' ></img> " . $country . " \"></option>";
                                    
                                    }
                                }
                            }
                                ?>
                        </select>

                        <style>
                            .hidden {
                                display: none;
                                visibility: hidden;
                            }
                        </style>
                        <table id="applist" class="table table-borderless  ">
                            <thead>
                                <tr>
                                    <th data-class='hidden' data-field="id">id</th>
                                    <th data-field="app">App</th>
                                    <th data-field="app_code">App Code</th>
                                    <th data-field="country_code">Country Code</th>
                                    <th data-field="price_out">Price</th>
                                    <th data-field="count">Count</th>
                                    <th data-field="check">Buy</th>
                                </tr>
                            </thead>

                        </table>

                    </div>

                </div>
            </div>



        </div>


        <div class="col-lg-11  mt-3">
            <div class="row">
                <div class="card shadow  col-lg-12  ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-primary font-weight-bold m-0">history table</h6>

                    </div>
                    <div class="card-body  ">
                        <table data-search="true" data-show-refresh="true" data-show-toggle="true"
                            data-show-columns="true" data-show-columns-toggle-all="true" data-detail-view="true"
                            data-click-to-select="true" data-detail-formatter="detailFormatter"
                            data-show-pagination-switch="true" data-pagination="true" data-id-field="Id_request"
                            data-page-list="[10, 25, 50, 100, all]" data-show-footer="true" id="requests_history"
                            class="table table-bordeless dt-responsive nowrap ">
                            <thead>
                                <tr>
                                    <th data-field='Id_request'>id</th>
                                    <th data-field='Status'>Status</th>
                                    <th data-field='Phone_Nb'>Phone_Nb</th>
                                    <th data-field='SMSCode'>SMSCode</th>
                                    <th data-field='sms_content'>sms_content</th>
                                    <th data-field='TimeStmp'>time</th>
                                    <th data-field='code'>App</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>