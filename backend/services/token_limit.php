<?php



require '/var/www/smsmarket/html/backend/redisconfig.php';



while (true) {
    if($redis){

    
    $redisKey = 'check_api:e7414e75fb8acc938ad625f2830d0b4bef216e4d6ceecebcd6b1987fe483772a';
    // Set the key to 1
    $redis->set($redisKey, 1);
    echo "set 1\n";
    // Sleep for 10 seconds
    sleep(4);

    // Set the key to 0
    $redis->set($redisKey, 0);
    echo "set 0\n";

    // Sleep for 20 seconds
}
    sleep(16);
}