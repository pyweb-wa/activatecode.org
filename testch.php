<!DOCTYPE html>
<html>
  <head>
    <title>Bootstrap Card Example</title>
    <!-- Link to Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    
    <style>
      /* Add some custom styles to the card */
      .card {
        margin: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
      }

      /* On mouse-over, add a darker background color */
      .card:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
      }

      /* Add some padding and font-size to the card header */
      .card-header {
        padding: 10px;
        font-size: 20px;
      }

      /* Add some padding to the card body */
      .card-body {
        padding: 10px;
      }
    </style>
  </head>
  
  <body>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-chart-bar"></i> Application Stats
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Application</th>
                  <th>Country Code</th>
                  <th>Count</th>
                  <th>App Code</th>
                </tr>
              </thead>
              <tbody>
                <!-- JSON data goes here -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>





    <!-- Link to Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>

      const data = {"ResponseCode":0,"Msg":"OK","Result":[{"count":"240","application":"whatsapp","country_code":"AZ","app_code":"wa"},{"count":"25","application":"google","country_code":"CF","app_code":"go"},{"count":"26","application":"instagram","country_code":"CF","app_code":"in"},{"count":"1","application":"telegram","country_code":"CF","app_code":"te"},{"count":"20","application":"tiktok","country_code":"CF","app_code":"ti"},{"count":"1313","application":"whatsapp","country_code":"CF","app_code":"wa"},{"count":"8","application":"facebook","country_code":"CL","app_code":"fa"},{"count":"3","application":"google","country_code":"CL","app_code":"go"},{"count":"8","application":"instagram","country_code":"CL","app_code":"in"},{"count":"3","application":"tiktok","country_code":"CL","app_code":"ti"},{"count":"469","application":"facebook","country_code":"GN","app_code":"wa"},{"count":"3","application":"telegram","country_code":"IQ","app_code":"te"},{"count":"27540","application":"whatsapp","country_code":"IQ","app_code":"wa"},{"count":"1120","application":"google","country_code":"JO","app_code":"wa"},{"count":"1","application":"whatsapp","country_code":"KG","app_code":"wa"},{"count":"7560","application":"whatsapp","country_code":"LB","app_code":"wa"},{"count":"288","application":"whatsapp","country_code":"LK","app_code":"wa"},{"count":"3944","application":"whatsapp","country_code":"LY","app_code":"wa"}]};

const tableBody = document.querySelector('.card-body tbody');

data.Result.forEach(item => {
  const row = `
    <tr>
      <td>${item.application}</td>
      <td>${item.country_code}</td>
      <td>${item.count}</td>
      <td>${item.app_code}</td>
    </tr>
  `;
  tableBody.innerHTML += row;
});
// });
</script>

</body>
</html>
