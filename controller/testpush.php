<?php
$url = 'https://digitalsim.goonline.company/push/push_data.php';

$jsonData = createPhoneNumber(10, "test");
// echo $jsonData;
// die();
//$phone = "56926819761";
//$jsonData = '{"results":[{"receivedtime":1671211160,"phone_number":"12345667","sms":"G-937302 is your Google verification code.","sender":"devdev"}]}';
//$jsonData = '{"results":[{"receivedtime":1671211160,"phone_number":"'.$phone.'","sms":"Tu chip esta OK! Tienes 1GB+50min+Whatsapp Ilim x 15 dias. Ademas durante 3 meses realiza 1 Recarga cada 30 dias y te damos 2GB+100min x 15 dias. Recarga ahora en mi.wom.cl/rec","sender":"WOM"}]}';

// Define the authorization key and code
$password = 'DijiTalSIM-@-passwordCode';
$authorizationKey = md5(time() . $password);
$codeKey = time();

// Set up the cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: ' . $authorizationKey,
    'Code: ' . $codeKey,
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Send the request and get the response
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo $response;
}

// Close the cURL request
curl_close($ch);

function createPhoneNumber($count, $source)
{
    $numbers = array();
    $country_code = "LB";
    //$source = "test";
    for ($i = 0; $i < $count; $i++) {
        $number = array(
            'phone_number' => "96170" . rand(100000, 999999),
            'country_code' => $country_code,
            'source' => $source,
        );
        $numbers[] = $number;
    }
    $data = array(
        'numbers' => $numbers,
        'application' => array('whatsapp'),
    );
    return json_encode($data);
}
