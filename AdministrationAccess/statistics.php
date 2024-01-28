<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usage Statistics</title>

   
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/bootstrap/adminlte/adminlte.min.css">

</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <section class="content">
   
            <!-- Default box -->
            <div class="card card-solid">
                <div class="card-body pb-0">

                    <div class="row" id="content">
                        <!-- <div class="col-10 col-sm-6 col-md-3 d-flex align-items-stretch flex-column">
                            <div class="card bg-light d-flex flex-fill">
                                <div class="card-header text-muted border-bottom-0">
                                    Country: Chile
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="col-6">
                                            <h2 class="lead"><b>Total used:50000</b></h2>
                                            
                                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                                <li class="small"><span class="fa-li"><i class="fa fa-home"></i></span>avilable:40000 </li>
                                              
                                                <li class="small"><span class="fa-li"><i class="fas fa-phone"></i></span> Requested: 10000</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-sms"></i></span> Has Message: 5000</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-ban"></i></span> No sms recived: 5000</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-mobile-alt"></i></span> applications: Whatsapp,Facebook,google</li>
                                            </ul>
                                        </div>
                                        <!-- <div class="col-5 text-center">
                                            <img src="../../dist/img/user1-128x128.jpg" alt="user-avatar" class="img-circle img-fluid">
                                        </div> -->
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-right">
                                        <a href="#" class="btn btn-sm bg-teal">
                                            <i class="fas fa-comments"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-primary">
                                            <i class="fas fa-user"></i> View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div>


                <!-- /.control-sidebar -->
            </div>
            <!-- ./wrapper -->
        </section>
        <!-- /.content -->
    </div>

</body>
<script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script>

function makeAjaxCall() {
  $.ajax({
    url: 'servicesfxn/get_statistics.php',
    type: 'POST',
    data: {'updatecountry':'True'},
    //dataType: 'json',
    success: function(response) {
        console.log(response)
        $('#content').html(response);
       // var country_list = JSON.parse(response);
       //get_statData(country_list)
      // Process the response here
      console.log(country_list);
    },
    error: function(xhr, status, error) {
      console.log( error);
    },
    complete: function(xhr, status) {
      // Wait for 2 seconds before making the next call
      setTimeout(function() {
        makeAjaxCall();
      }, 20000);
    }
  });
}


function get_statData(country_list){


//country_list.forEach(function(value) {
  $.ajax({
    url: 'servicesfxn/get_statistics.php',
    type: 'POST',
    //data: {'get_stat':'True','country': value['country'],'country_name': value['country_name']},
    data: {'get_stat':'True','country': country_list[0]['country'],'country_name': country_list[0]['country_name']},
    success: function(response) {
      console.log('Response:', response);
    },
    error: function(xhr, status, error) {
      console.log('Error:', error);
    },
    complete: function(xhr, status) {
      // Wait for 2 seconds before making the next call
      setTimeout(function() {
        console.log("finish  " + country_list[0]['country'])
       // makeAjaxCall();
      }, 2000);
    }
  });
//});




}

// Call the function to start the interval
makeAjaxCall();





    </script>

</html>