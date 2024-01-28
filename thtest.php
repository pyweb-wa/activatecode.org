<?php
    // require_once "/var/www/smsmarket/html/backend/outAPI/Jikatel_api.php";
    // $jikatel_backend = new jikatel_backend();
    // $res = $jikatel_backend->GetDataStats(); 
    // $newsms =  json_encode(['info' => $res,'count' => count($res)]);
    // echo $newsms;


    include '/var/www/smsmarket/html/backend/redisconfig.php';
    $liveRedisKey = "users_rate:live";
    $oldRedisKey = "users_rate:old";
    $live_data = $redis->get($liveRedisKey);
    
    $live_data = json_decode($live_data, true);
    $old_data = $redis->get($oldRedisKey);
    echo $old_data;
    $old_data = json_decode($old_data, true);

   
    // $mergedArray = array_merge($live_data, $old_data);
?>