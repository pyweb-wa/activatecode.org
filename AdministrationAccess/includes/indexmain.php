<div class="container-fluid">
    <!-- <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark mb-0">Dashboard</h3><a class="btn btn-primary btn-sm d-none d-sm-inline-block" role="button" href="#"><i class="fas fa-download fa-sm text-white-50"></i>&nbsp;Generate Report</a>
    </div> -->

    <div class="row">
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card shadow border-left-primary py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-uppercase text-primary font-weight-bold text-xs mb-1"><span>Total API
                                    balance</span></div>
                            <div class="text-dark font-weight-bold h5 mb-0">
                                <span>$<?php echo total_apibalance() ?></span></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card shadow border-left-success py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-uppercase text-success font-weight-bold text-xs mb-1"><span>Customers
                                    amounts</span></div>
                            <div class="text-dark font-weight-bold h5 mb-0">
                                <span>$<?php echo total_userbalance();?></span></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card shadow border-left-info py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-uppercase text-info font-weight-bold text-xs mb-1"><span>Profits</span>
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="text-dark font-weight-bold h5 mb-0 mr-3">
                                        <span>$<?php echo total_Profits() ?></span></div>
                                </div>

                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card shadow border-left-warning py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col mr-2">
                            <div class="text-uppercase text-warning font-weight-bold text-xs mb-1">
                                <span>Customers</span></div>
                            <div class="text-dark font-weight-bold h5 mb-0"><span><?php echo total_Customers();?></span>
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-comments fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 col-xl-8">
        <div >
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary font-weight-bold m-0">Requests per day</h6>
                </div>
                <div class="card-body">

                    <div class="chart-area"><canvas id="chLine"></canvas></div>
                </div>
            </div>
        </div>
        <div  >
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary font-weight-bold m-0">Daily Profits</h6>
                </div>
                <div class="card-body">

                    <div class="chart-area"><canvas id="chLine2"></canvas></div>
                </div>
            </div>
        </div>
</div>
        <div class="col-lg-5 col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary font-weight-bold m-0">Most used Apps</h6>

                </div>
                
<!-- ****************************************************** -->

                <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                <?php $apps=top_used_apps();
                    foreach($apps as $tr): ?>
                     <li class="list-group-item d-flex justify-content-between align-items-center">
                     <div class="row align-items-center no-gutters">
                        <div>
                            <img src="assets/img/login_sms.png" width='50' alt="App Image" >
                        </div>
                        <div class="col-auto" style="margin:20px">
                            <div class="btn btn-primary btn-circle  btn-sm"><?php echo $tr['count']; ?></div>
                        </div>
                        
                        <div class="col mr-4">
                            <p class="mb-0"><?php echo ($tr['app'] ." ".$tr['API']); ?> </p>
                            <span class="badge badge-pill badge-warning"><?php echo $tr['country']; ?></span>
                        </div>

                        
                    </div>
                  </li>
                         
                    
                <?php endforeach; ?>
                  
                  
                  
                </ul>
              </div>

<!-- *************************************************************** -->
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="text-primary font-weight-bold m-0">Projects</h6>
                </div>
                <div class="card-body">
                    <h4 class="small font-weight-bold">Server migration<span class="float-right">20%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;"><span class="sr-only">20%</span></div>
                    </div>
                    <h4 class="small font-weight-bold">Sales tracking<span class="float-right">40%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%;"><span class="sr-only">40%</span></div>
                    </div>
                    <h4 class="small font-weight-bold">Customer Database<span class="float-right">60%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-primary" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"><span class="sr-only">60%</span></div>
                    </div>
                    <h4 class="small font-weight-bold">Payout Details<span class="float-right">80%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%;"><span class="sr-only">80%</span></div>
                    </div>
                    <h4 class="small font-weight-bold">Account setup<span class="float-right">Complete!</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"><span class="sr-only">100%</span></div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="text-primary font-weight-bold m-0">Todo List</h6>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <h6 class="mb-0"><strong>Lunch meeting</strong></h6><span class="text-xs">10:30 AM</span>
                            </div>
                            <div class="col-auto">
                                <div class="custom-control custom-checkbox"><input class="custom-control-input" type="checkbox" id="formCheck-1"><label class="custom-control-label" for="formCheck-1"></label></div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <h6 class="mb-0"><strong>Lunch meeting</strong></h6><span class="text-xs">11:30 AM</span>
                            </div>
                            <div class="col-auto">
                                <div class="custom-control custom-checkbox"><input class="custom-control-input" type="checkbox" id="formCheck-2"><label class="custom-control-label" for="formCheck-2"></label></div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row align-items-center no-gutters">
                            <div class="col mr-2">
                                <h6 class="mb-0"><strong>Lunch meeting</strong></h6><span class="text-xs">12:30 AM</span>
                            </div>
                            <div class="col-auto">
                                <div class="custom-control custom-checkbox"><input class="custom-control-input" type="checkbox" id="formCheck-3"><label class="custom-control-label" for="formCheck-3"></label></div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card text-white bg-primary shadow">
                        <div class="card-body">
                            <p class="m-0">Primary</p>
                            <p class="text-white-50 small m-0">#4e73df</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card text-white bg-success shadow">
                        <div class="card-body">
                            <p class="m-0">Success</p>
                            <p class="text-white-50 small m-0">#1cc88a</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card text-white bg-info shadow">
                        <div class="card-body">
                            <p class="m-0">Info</p>
                            <p class="text-white-50 small m-0">#36b9cc</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card text-white bg-warning shadow">
                        <div class="card-body">
                            <p class="m-0">Warning</p>
                            <p class="text-white-50 small m-0">#f6c23e</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card text-white bg-danger shadow">
                        <div class="card-body">
                            <p class="m-0">Danger</p>
                            <p class="text-white-50 small m-0">#e74a3b</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card text-white bg-secondary shadow">
                        <div class="card-body">
                            <p class="m-0">Secondary</p>
                            <p class="text-white-50 small m-0">#858796</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>