<?php
$redis = new Redis();
try{
    $redisConnected = $redis->connect('192.168.100.20', 6379,1);
    if($redisConnected) $redis->auth('foobared.123');
}
catch (Exception $e) {
    $redisConnected = 0;
    $redis=0;
}
