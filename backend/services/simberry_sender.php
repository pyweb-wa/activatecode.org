<?php

while(true)
{
    Featch_From_Redis();
    sleep(1);
}
function Featch_From_Redis(){
require '/var/www/smsmarket/html/backend/redisconfig.php';
$keys = $redis->keys('simberry:*');
//var_dump($keys);
if($keys){
    foreach($keys as $key){
        
      //  $numbers_object = $redis->smembers($key);
        $numbers_object = $redis->sRandMember($key,5000);   
        $numbers = json_encode($numbers_object,true);
        echo "Start with ".$key." ==> count:".count($numbers_object)."\n";
        $parts = explode(':', $key);
        $source = $parts[1];
        $status = call_simberry($source,$numbers);
        if($status){

            $currentTime = time();
            
            foreach ($numbers_object as $number) {
                $redis->srem($key, $number);
                $redisKey = 'todelete:'.$source;
                $redis->zadd($redisKey, $currentTime, $number);
             }
        }
        else{
            echo "error when sending data\n";
        }
        
    }
   
}
}


function call_simberry($source,$numbers){

    if (strpos($source, '_') !== false) {
       // $numbers = json_encode($numbers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        $parts = explode('_', $source);
        // Check if there are enough parts
        if (count($parts) >= 3) {
            $server_name = $parts[1];
            $list_id = $parts[2];
            include '/var/www/smsmarket/html/backend/inAPI/simberry/api2.php';
            $accounts = get_accounts();
            $found = false;
           
            foreach ($accounts as $server) {
                if (isset($server['name']) && $server['name'] == $server_name) {
                    $found = $server;
                    break;
                }
            }
            
            if ($found) {
              $res =  AddArrayToList($server,$numbers,$list_id);
              echo "$res\n\n";
              if($res){
                if (strpos($res, "list_id") !== false) {
                  //  echo $res."\n";
                    return true;
                    
                }
              }
             
            }
                            
        } 
        }
        return false;
}


