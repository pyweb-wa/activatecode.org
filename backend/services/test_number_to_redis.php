<?php
$source = "LB_35_1998";



function updateNumbers($source){
    require '/var/www/smsmarket/html/backend/redisconfig.php';
    $phoneNumbers = fetchNumbersFromRedis($source,10000);
    if (!$phoneNumbers){
        echo "can't find data from source $source ";
        return;
    }
    $redisKey = 'live_'.$source;
    foreach ($phoneNumbers as $number) {
    $redis->sadd($redisKey, $number['phone_number']);
    }
}

function fetchNumbersFromRedis($source,$numberOfItems = 1000) {
    require '/var/www/smsmarket/html/backend/redisconfig.php';
    echo $source;

    $jsonValue = $redis->get($source);
    
    $data = json_decode($jsonValue, true);
    if($data == null) {
        return array ();
    }
    $itemsToDelete = array_slice($data, 0, $numberOfItems);
    // Remove the first 100 items from the data
    $data = array_slice($data, $numberOfItems);

    // Encode items back to JSON
    echo sizeof($data) .'\\n';
    $newJsonValue = json_encode($data);
    // Store new JSON value in Redis
    //$redis->set($source, $newJsonValue);

    return $itemsToDelete;
}