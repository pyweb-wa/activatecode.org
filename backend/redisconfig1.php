<?php
$redis1 = new Redis();
try{
    $redisConnected1 = $redis1->connect('192.168.100.21', 6379,1);
    if($redisConnected1) $redis1->auth('foobared.123');
}
catch (Exception $e) {
    $redisConnected1 = 0;
    $redis1=0;
}
