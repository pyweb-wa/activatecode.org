<?php
// The JSON data to be sent
$data = array('action' => 'GET_NUMBERS', 'key' => 'ruPass','sum' => 3,'country' =>'russia','operator' =>'any','service' => 'whatsapp','exceptionPhoneSet'=> array('777'));
//$json = json_encode($data);
$data = array('action' => 'GET_SERVICES', 'key' => 'ruPass');

$json = json_encode($data);

// Compress the data using gzencode
$compressed_data = gzencode($json);

// Create a curl handle
$ch = curl_init();

// Set the URL and other curl options
curl_setopt($ch, CURLOPT_URL, 'https://receiver.goonline.company/ruapi.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $compressed_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Encoding: gzip'
));
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

// Execute the request
$response = curl_exec($ch);
$response = gzdecode($response);

// Check for errors

if ($response === false) {
    echo 'Error: ' . curl_error($ch);
} else {
    $decoded_response = gzdecode($response);
    echo $decoded_response;
}
// Close the curl handle
curl_close($ch);



