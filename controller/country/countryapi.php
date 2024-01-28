<?php
// require_once "validate_token.php";
// if (!checkTokenInDatabase()) {
//     header('Location: index.php');
//     exit(); 
// }
$data = file_get_contents("php://input");
$data = json_decode($data, true);
$url = 'https://activatecode.org/backend/outAPI/Jikatel_api.php';

if (isset($data) && !empty($data["action"])) {
    if ($data["action"] == "getdata") {
        echo getdata($data);
    }

    if ($data["action"] == "powertoggle") {
        echo powertoggle($data);
    }

    if ($data["action"] == "SaveConfig") {
        echo SaveConfig($data);
    }

    if ($data["action"] == "download") {
        echo download($data);
    }

    if ($data["action"] == "deleteN") {
        echo deletenumber($data);
    }

    if ($data["action"] == "deleteNB") {
        echo deletenumBox($data);
    }

    if ($data["action"] == "getstats") {
        echo getstats($data);
    }
    if ($data["action"] == "reactivate") {
        echo reactivate($data);
    }
    if ($data["action"] == "autoreactivate") {
        echo autoreactivate($data);
    }
    if ($data["action"] == "autoreactivatestatus") {
        echo autoreactivatestatus($data);
    }
    if ($data["action"] == "get_load") {
        echo get_loads($data);
    }
}
function deletenumber($data)
{
    $response = sendpost($data);
    $response = json_decode($response, true);
    if (isset($response['status'])) {
        if ($response['status'] == "ok") {
            return json_encode(['status' => 'ok']);
        }
    }
    return json_encode(['status' => 'error']);
}
function deletenumBox($data)
{
    $response = sendpost($data);
    echo $response;
    die();
    $response = json_decode($response, true);
    if (isset($response['status'])) {
        if ($response['status'] == "ok") {
            return json_encode(['status' => 'ok']);
        }
    }
    return json_encode(['status' => 'error']);
}
function reactivate($data)
{
    $response = sendpost($data);
    $response = json_decode($response, true);
    if (isset($response['status'])) {
        if ($response['status'] == "ok") {
            return json_encode(['status' => 'ok']);
        }
    }
    return json_encode(['status' => 'error']);
}
function autoreactivate($data)
{
    $response = sendpost($data);
    $response = json_decode($response, true);
    if (isset($response['status'])) {
        if ($response['status'] == "ok") {
            return json_encode(['status' => 'ok', 'msg' => $response['msg']]);
        }
    }
    return json_encode(['status' => 'error']);
}
function autoreactivatestatus($data)
{

    $response = sendpost($data);
    $response = json_decode($response, true);
    if (isset($response['status'])) {
        if ($response['status'] == "ok") {
            return json_encode(['status' => 'ok', 'msg' => $response['msg']]);
        }
    }
    return json_encode(['status' => 'error']);
}
function download($data)
{
    //  var_dump($data);
    // die();
    $response = sendpost($data);
    //  echo $response;
    // die();
    return json_encode(['status' => 'ok', 'msg' => $response]);
}
function SaveConfig($data)
{ //todo
    $response = sendpost($data);
    $response = json_decode($response, true);
    if (isset($response['status'])) {
        if ($response['status'] == "ok") {
            return json_encode(['status' => 'ok']);
        }
    }
    return json_encode(['status' => 'error']);
}
function powertoggle($data)
{
    $response = sendpost($data);
    // echo $response;
    // die();
    $response = json_decode($response, true);
    if (isset($response['status'])) {
        //echo 3;
        if ($response['status'] == "ok") {
            return json_encode(['status' => 'ok']);
        }
    }
    return json_encode(['status' => 'error']);
}

function getdata($data)
{
    // require_once "/var/www/smsmarket/html/backend/outAPI/Jikatel_api.php";
// $jikatel_backend = new jikatel_backend();
    // $response = sendpost($data);
//    global $jikatel_backend;
    // $res = $jikatel_backend->GetDataStats();

    $response = sendpost($data);
    $res = json_decode($response, true);

    return json_encode(['info' => $res,'count' => count($res)]);
}

function getstats($data)
{

    $response = sendpost($data);

    $res = json_decode($response, true);

    return json_encode(['info' => $res]);
};

function sendpost($postrequest)
{

    $url = 'https://activatecode.org/backend/outAPI/Jikatel_api.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $time = time();
    $auth = md5($time . "banana-api-passwordCode");
    $headers = array(
        "code: " . $time,
        "Authorization: " . $auth,
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postrequest));
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    if ($resp === false) {
        echo 'cURL error: ' . curl_error($curl);
    }
    curl_close($curl);
    return $resp;
}

function get_loads($data){
    // Redis keys
    include '/var/www/smsmarket/html/backend/redisconfig.php';
    $redisKeys = ['server_loads:main', 'server_loads:server1', 'server_loads:server2', 'server_loads:server3'];
    $jsonData = [];
    foreach ($redisKeys as $redisKey) {
        // Get Threads_connected value
        $threadsConnected = $redis->get($redisKey . ':Threads_connected');
        
        // Get Active_connections value
        $activeConnections = $redis->get($redisKey . ':Active_connections');
        
        // Add data to the JSON array
        $jsonData[] = [
            'redis_key' => $redisKey,
            'threads_connected' => $threadsConnected,
            'active_connections' => $activeConnections,
        ];
    }

    // Encode the array as JSON
    // $jsonString = json_encode($jsonData, JSON_PRETTY_PRINT);

    // Output the JSON string
     // Close the Redis connection
    // $redis->disconnect();
    // header('Content-Type: application/json');
    return json_encode($jsonData);

   
}
