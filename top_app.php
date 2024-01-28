<?php
// Set the URL to retrieve the JSON data from
$url = 'https://digitalsim.goonline.company/backend/out_interface.php?api_key=381c7f18fab268dde4a14f6a79d7ab36b32a14cdb93c827d95d71f7dcca6daa4&action=get_available';

//Use This
//  smshub_lnovaro

//$url = "https://45-79-8-214.ip.linodeusercontent.com/ruapi/rusender.php?key=8c203af7fe0f1496d5dfe5567a63301ab49fe3f9&action=GET_SERVEICES";
//smshub_Jikatel
//3621U27b0c45b7c77d614bbc55210c5176064

// Initialize a new cURL session
$curl = curl_init();

// Set the cURL options
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
  ),
));

// Execute the cURL request and store the response
$response = curl_exec($curl);

// Check for any cURL errors
if(curl_error($curl)) {
  $error_msg = curl_error($curl);
  echo "cURL Error: " . $error_msg;
}

// Close the cURL session
curl_close($curl);

// Convert the JSON response into a PHP array
$data = json_decode($response, true);

// Extract data for specific application(s)
$applications = array('whatsapp', 'google','telegram','facebook','microsoft','wechat','instagram','yalla','viber');
$extracted_data = array();
foreach ($data['Result'] as $result) {
  if (in_array(strtolower($result['application']), $applications)) {
    $extracted_data[] = $result;
  }
}

// Display the extracted data
#print_r($extracted_data);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Extracted Data Table</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </head>
  <body>
    <div class="container">
      <h2>Extracted Data Table</h2>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Application</th>
            <th>Country Code</th>
            <th>Count</th>
            <th>App Code</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($extracted_data as $result) { ?>
            <tr>
              <td><?php echo $result['application']; ?></td>
              <td><?php echo $result['country_code']; ?></td>
              <td><?php echo $result['count']; ?></td>
              <td><?php echo $result['app_code']; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </body>
</html>