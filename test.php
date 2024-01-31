<?php
$start = microtime(true);

 //update_redis_numbers();
//move_balance();

//check_balance(19);
simbery();
// die(); 
// // //getfromredis();
// $amount = 1;
// $b = balance($amount);
// if($b != false){
//     echo "Good $b";
//     die();
// }
// else{
//     echo "Lock";
//     die();
// }


$end = microtime(true);
    $executionTime = $end - $start;

    echo "<br>Script execution time: " . $executionTime . " seconds" . PHP_EOL;
function simbery(){
    include '/var/www/smsmarket/html/backend/redisconfig.php';
    $key = "simberry:SY_84_806";
    $redis->del($key);
//     $numbers_object = $redis->sRandMember($key,15000);
//    // $members = $redis->sMembers('MA_35_3214');
//     echo sizeof($numbers_object);


}

function check_balance($userId){
    include '/var/www/smsmarket/html/backend/config.php';
    $sql = "select `users`.`Id`,balance  from `users` where users.Id = ? ;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $res = $stmt->fetchAll();
    $mysql_balance = $res[0]['balance'];
    include '/var/www/smsmarket/html/backend/redisconfig.php';
   
    $balanceKey = "balance:$userId";
    $redis_balance = $redis->get($balanceKey);
    echo "balance for userId :$userId\n";
    echo "mysql_balance: $mysql_balance\n\nredis_balance: $redis_balance\n";
    echo __FUNCTION__;
}
function move_balance(){

    include '/var/www/smsmarket/html/backend/config.php';
    $sql = "select `users`.`Id`,balance  from `users`;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $res = $stmt->fetchAll();
    if (sizeof($res) > 0) {
        foreach($res as $item){
            $userId = $item['Id'];
            $balance =$item['balance'];
            echo  "$userId - $balance\n" ;
            include '/var/www/smsmarket/html/backend/redisconfig.php';  
            $balanceKey = "balance:$userId";
            $redis->set($balanceKey,$balance);
        }
       
        
    }
}
function balance($amount){
 
    include '/var/www/smsmarket/html/backend/redisconfig.php';
        $userId = 29;
        //  $balanceKey = "balance:$userId";
        //  $lockKey = "lock:$userId";
        //  $redis->del($lockKey);
        // // $currentBalance = $redis->set($balanceKey,100);
        // //    die(); 
        // // Lock to ensure atomicity
        // $newBalance = $redis->decrby($balanceKey, $amount);
        // return $newBalance;
        $lockKey = "lock:$userId";
        $lockExists = $redis->exists($lockKey);
        while($lockExists) {
            $lockExists = $redis->exists($lockKey);
            $log = "[-] ".(string) $userId . "Lock $amount  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
            file_put_contents("/var/www/smsmarket/logging/balance_lock.log", $log, FILE_APPEND);
           usleep(5000);
        }
        $lockAcquired = $redis->set($lockKey, 1, ['nx' => true, 'px' => 10]); // 10 second lock
        if (!$lockAcquired) {
            $log = "[-] ".(string) $userId . "Lock22 $amount  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
        
            file_put_contents("/var/www/smsmarket/logging/balance_lock.log", $log, FILE_APPEND);
            return false;
        }
        $balanceKey = "balance:$userId";
        $currentBalance = $redis->get($balanceKey);
        if ($currentBalance === false || $currentBalance < $amount) {
            $redis->del($lockKey); // Release the lock
            return false;
        }
        // Deduct from balance
        $newBalance = $redis->incrbyfloat($balanceKey, -$amount);
        $redis->del($lockKey);
        return $newBalance;       
}


function deleteFromredis(){
    //$pattern = 'redis_numbers:*';

      //     $keys = $redis->keys($pattern);
    // //echo json_encode($keys);
    // $redis->del($keys);

    // die();
    // $keys = $redis->keys('redis_numbers:*');
    // $count = count($keys);
    // echo "Number of keys: $count";
        

    include '/var/www/smsmarket/html/backend/redisconfig1.php';
  
    // $keys = $redis1->keys($pattern);
    // //echo json_encode($keys);
    // $redis1->del($keys);


}


function getfromredis(){
    //17056153534019
    include '/var/www/smsmarket/html/backend/redisconfig1.php';
    $redisKey="redis_numbers:17056153534019";
   $res=  $redis1->hgetall($redisKey);
    echo json_encode($res);
}
function update_redis_numbers(){
   
    
   
    include '/var/www/smsmarket/html/backend/redisconfig1.php';
 
    // $count = [];

    // $keys = $redis1->keys('redis_numbers:*');
    
    // foreach ($keys as $key) {
    //     $fields = $redis1->hgetall($key);
    //     $source = $fields['source'];
    //     if (isset($fields['taked']) && $fields['taked'] == 1  ) {
           
    //         if (!isset($count[$source])) {
    //             $count[$source] = [
    //                 'source' => $source,
    //                 'taked' => 0,
    //                 'finished' => 0,
    //             ];
    //         }
    
    //         $count[$source]['taked']++;
    //     }
    //     if(isset($fields['is_finished']) && $fields['is_finished'] == 1)
    //     $count[$source]['finished']++;
    // }
    
    // // Convert the associative array to indexed array
    // $resultArray = array_values($count);
    
    // echo json_encode($resultArray);
    


    $count = [];
    $pipeline = $redis1->multi(\Redis::PIPELINE);
    $keys = $redis1->keys('redis_numbers*');

    foreach ($keys as $key) {
    $pipeline->hgetall($key);
    }

    $results = $pipeline->exec();

    $keys = $results[0];
    foreach ($keys as $key) {
       
    $fields = $redis1->hgetall($key);
    $source = $fields['source'];
    if (isset($fields['taked']) && $fields['taked'] == 1) {
        if (!isset($count[$source])) {
            $count[$source] = [
                'source' => $source,
                'taked' => 0,
                'finished' => 0,
            ];
        }
        $count[$source]['taked']++;
    }
    if (isset($fields['is_finished']) && $fields['is_finished'] == 1) {
        $count[$source]['finished']++;
    }
    }

    // Convert the associative array to indexed array
    $resultArray = array_values($count);

    echo json_encode($resultArray);
    

    
}
// Return the results array
function get_loads(){
    // Redis keys
    include '/var/www/smsmarket/html/backend/redisconfig.php';
    $redisKeys = ['server_loads:main', 'server_loads:server1', 'server_loads:server2'];

    // Create a Predis client
   

    // Initialize an array to hold the JSON data
    $jsonData = [];

    // Loop through the Redis keys
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
    $jsonString = json_encode($jsonData, JSON_PRETTY_PRINT);

    // Output the JSON string
    echo $jsonString;

    // Close the Redis connection
    $redis->disconnect();
}

function check_api_redis(){

    include '/var/www/smsmarket/html/backend/redisconfig.php';
    $ttl = 300;
    $tableName = 'source_list';

    $key = 'MA'; 
    $status = 1;

   // $redis->set("$tableName:$key", $status);
   $array = (['LB_35_1998_test2']);
   //$redis->append($tableName, $array);
    $redis->set($tableName, json_encode($array));
   // $status = $redis->set("$tableName","LB_35_1998_test");
     $tableName = 'source_list';
    $status = $redis->get("$tableName");
    $status = json_decode($status,true);
    $status[] = 'grape';
    $redis->set($tableName, json_encode($status));

    var_dump($status);
die();
//     $array = array(
//          array("id" => 1, "Number" => 100000),
//         array("id" => 2, "Number" => 200000),
//         array("id" => 3, "Number" => 300000)
//       );
    
//     $redisHashKey = 'request_id';
//     $redisSetKey = 'request_number';

//     // Insert each item into the Redis hash and set
//     foreach ($array as $item) {
//     // Use "id" and "Number" as the fields in the Redis hash
//     $fieldById = 'id:' . $item['id'];
//     $fieldByNumber = 'Number:' . $item['Number'];

//     // Convert the item to JSON before storing in the hash
//     $jsonItem = json_encode($item);

//    // Set the JSON item in the Redis hash with TTL using setex
//    $redis->setex($redisHashKey . ':' . $fieldById, $ttl, $jsonItem);
//    $redis->setex($redisHashKey . ':' . $fieldByNumber, $ttl, $jsonItem);

//    // Add the keys to the Redis set with TTL using setex
//    $redis->sadd($redisSetKey, $fieldById, $fieldByNumber);
//    $redis->setex($redisSetKey . ':' . $fieldById, $ttl, 1); // You can set any value here, as we use this as a set.
//    $redis->setex($redisSetKey . ':' . $fieldByNumber, $ttl, 1);
//  echo $jsonItem."<br>";  


//}


    // Specify the "id" or "Number" to search
$idToSearch = 2;
$numberToSearch = 300000;
// Get the JSON data from the Redis hash using "id"
$jsonItemById = $redis->get($redisHashKey . ':' . 'id:' . $idToSearch);
// Get the JSON data from the Redis hash using "Number"
$jsonItemByNumber = $redis->get($redisHashKey . ':' . 'Number:' . $numberToSearch);

// Decode the JSON data into a PHP array
$itemById = json_decode($jsonItemById, true);
$itemByNumber = json_decode($jsonItemByNumber, true);
print_r( $itemById);
echo "<br>";
print_r($itemByNumber);
$itemByNumber['status'] = 1;
$redis->setex($redisHashKey . ':' . 'Number:' . $numberToSearch, $ttl, json_encode($itemByNumber));
$jsonItemByNumber = $redis->get($redisHashKey . ':' . 'Number:' . $numberToSearch);

$itemByNumber = json_decode($jsonItemByNumber, true);
echo "<br>";
print_r($itemByNumber);

//die();

    // $redisKey = 'check_api:74a2f8ffa13a9e419e23b88cc72379a17fb415543ce760a9d961314bb07c13a5';
    // $newValue = 1;
    // $redis->set($redisKey, $newValue);
    $redis->close();
}
function callback_check(){
    $url = "http://27.124.44.188:9091/activate-code/callback";
    $ch = curl_init();
    $object = '{ "id": 15652659, "phone_number": 212681075870, "sms": "this is a test sms for check the callbackurl", "application": "whatsapp", "code": "128961"}';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $object);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
        )
    );
    $response = curl_exec($ch);
    var_dump($response);
}
function updateRecords()
{
    try {
        include '/var/www/smsmarket/html/backend/config.php';
        // Fetch all IDs from the temporary table
        $selectQuery = 'SELECT * FROM `bananaapi-number` WHERE `createdTime` = "2023-12-22 10:11:08"';
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->execute();
        $allRecords = $selectStmt->fetchAll(PDO::FETCH_ASSOC);

        // Update records in batches of 1000 with retry on deadlock
        $batchSize = 1000;
        $offset = 0;
        $maxRetries = 5; // Maximum number of retries
        $all = count($allRecords);
        $newCountryCode = "GH";
        $newSource = "GH_34_805";
      //  update   `bananaapi-number` set country_code ="GH", source = "GH_34-805" where `createdTime` =  "2023-12-22 10:11:08";
        while ($offset < $all) {
            // Get the current batch of records
            $recordsInBatch = array_slice($allRecords, $offset, $batchSize);

            if (!empty($recordsInBatch)) {
                $retryCount = 0;

                // Retry loop in case of deadlock
                while ($retryCount <= $maxRetries) {
                    try {
                        // Update records in `bananaapi-number` in the current batch
                        foreach ($recordsInBatch as $record) {
                            $updateQuery = "UPDATE `bananaapi-number` 
                                            SET country_code = ?, source = ?
                                            WHERE id = ?";
                            $updateStmt = $pdo->prepare($updateQuery);
                            $updateStmt->execute([$newCountryCode, $newSource, $record['id']]);
                        }

                        $all = $all - $batchSize;
                        echo "Updated " . count($recordsInBatch) . " records. Remaining: " . $all . "\n";

                        break; // Exit retry loop if successful
                    } catch (PDOException $e) {
                        if ($e->getCode() == 40001) {
                            // Deadlock error, retry
                            $retryCount++;
                            echo "Deadlock detected, retrying (attempt $retryCount)...\n";
                            usleep(500000); // Sleep for 0.5 seconds before retrying
                        } else {
                            // Another type of error, rethrow
                            throw $e;
                        }
                    }
                }

                $offset += $batchSize;
            }
        }

        echo "Operation completed successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function checkquery(){
    include 'backend/config.php';
    $sql = "SELECT 
            `bananaapi-number`.`country_code` AS country_char, 
            `bananaapi-number`.`source`,
            `countryList`.`country`, 
            `countryList`.`country_code`, 
            `countries_control`.`enabled`,
            `countries_control`.`start`,
            `countries_control`.`stop`,
            `countries_control`.`mstart`,
            `countries_control`.`mstop` 
        FROM 
            `bananaapi-number`, `countries_control` ,countryList  where is_finished = 0 and `countryList`.`id` = `countries_control`.`country_id` and `bananaapi-number`.`source` = `countries_control`.`source` and `bananaapi-number`.`country_code` = `countryList`.`country_char` GROUP BY 
            `bananaapi-number`.`country_code`, 
            `countryList`.`country`, 
            `countryList`.`country_code`, 
            `countries_control`.`enabled`,
            `countries_control`.`start`,
            `countries_control`.`stop`,
            `countries_control`.`mstart`,
            `countries_control`.`mstop`,
            `bananaapi-number`.`source`;";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = [];
            if(sizeof($results)>0){
                foreach($results as $res){
                    // if ($res['country_char'] != "CL") {
                        
                    //     continue;
                    // }
                    $sql = "SELECT 
                    (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = :country_code and `source` = :source) AS total,
                     (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = :country_code and `source` = :source) AS available, 
                    (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = :country_code and `source` = :source) AS requested, 
                    (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`,`foreignapiservice`,`bananaapi-number` where `foreignapiservice`.`country` = :country_code  and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS NOT NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `bananaapi-number`.`is_finished` = 1 and `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb` and `bananaapi-number`.`source` = :source) AS has_sms,
                     (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`, `foreignapiservice`, `bananaapi-number` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`service` = `foreignapiservice`.Id_Service_Api AND `requests_log`.`sms_content` IS NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`is_finished` = 0 AND `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb` AND `bananaapi-number`.`source` = :source) AS no_sms";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array("country_code" => $res['country_char'],"source" =>  $res['source']));
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if(sizeof($result)>0){
                        $result = $result[0];
                        $result['country'] = $res['country'];
                        $result['country_code'] = $res['country_code'];
                        $result['country_char'] = $res['country_char'];
                        $result['status'] = $res['enabled'];
                        $start = strtotime($res['start']);
                        $start = date('h:i a', $start);
                        $stop = strtotime($res['stop']);
                        $stop = date('h:i a', $stop);
                        $result['start'] = $start;
                        $result['stop'] = $stop;
                        $result['Mstart'] = intval($res['mstart']);
                        $result['Mstop'] = intval($res['mstop']);
                        $result['source'] = $res['source'];
                        $result['servertime'] = date('Y-m-d H:i:s', time());
      
                        array_push($response,$result);
                       // break;
                    }
                }

                }
    echo json_encode($response);
    
}

function available_ruTobanana(){
    include 'backend/config.php';


$countryStmt = $pdo->prepare("SELECT country_char, country FROM countryList");
$appStmt = $pdo->prepare("SELECT id , application  FROM application_code");
$countryStmt->execute();
$appStmt->execute();
$countryMap = $countryStmt->fetchAll(PDO::FETCH_KEY_PAIR);
$appMap = $appStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// echo "countryMap: ". json_encode($countryMap)."</br>";
// echo "appMap: ".  json_encode($appMap)."</br>";


$json = '{"countryList":[{"country":"Chile","operatorMap":{"Any":{"df":"1"}}},{"country":"Lebanon","operatorMap":{"Any":{"wa":"3"}}}],"status":"SUCCESS"}';
$json = '{"countryList":[{"country":"Chile","operatorMap":{"Any":{"aa":"29","ab":"29","af":"29","ah":"29","ai":"29","aj":"29","ak":"29"}}}],"status":"SUCCESS"}';
$data = json_decode($json, true);

// echo json_encode($data)."</br>";
// die();

$countryList = $data['countryList'];
$applications = array_values($appMap);

$results = array();
foreach ($countryList as $countryData) {
    $country = $countryData['country'];
    $operatorMap = $countryData['operatorMap'];
    foreach ($operatorMap as $operator => $appData) {
        foreach ($appData as $appCode => $count) {
            // Step 2: Use the extracted information to generate the desired output.
            $app = $applications[array_search($appCode, array_keys($appMap))];
            $countryCode = array_search($country, $countryMap);
            $results[] = array(
                'count' => $count,
                'application' => $app,
                'country_code' => $countryCode,
                'app_code' => $appCode,
            );
        }
    }
}

// Generate the final output.
$output = array(
    'ResponseCode' => 0,
    'Msg' => 'OK',
    'Result' => $results,
);

echo json_encode($output);



// // create new array with transformed data
// $result = array();
// foreach ($data['countryList'] as $countryData) {
//   $countryCode = array_search($countryData['country'], $countryMap);
//   foreach ($countryData['operatorMap'] as $appCode => $countMap) {
//     $appCode  = key((array)$countMap);
//     $application = $appMap[$appCode];
//     foreach ($countMap as $count) {
        
//       $result[] = array(
//         'count' => $count,
//         'application' => $application,
//         'country_code' => $countryCode,
//         'app_code' => $appCode,
//       );
//     }
//   }
// }

// encode new data as JSON and output
// $newJson = json_encode(array(
//   'ResponseCode' => 0,
//   'Msg' => 'OK',
//   'Result' => $result,
// ));
// echo $newJson;
}


function cards()
{
    
  
        //,(SELECT GROUP_CONCAT(DISTINCT  foreignapiservice.Name) as applications FROM `requests_log`,`foreignapiservice` where `foreignapiservice`.`country_name` = "Kyrgyzstan" and requests_log.service = `foreignapiservice`.Id_Service_Api ) As application
        // $sql = 'SELECT 
        // (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = "KG") AS total,
        // (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = "KG") AS avilable, 
        // (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = "KG") AS Requested, 
        // (SELECT COUNT(*) FROM `requests_log`,`foreignapiservice`,`bananaapi-number` where `foreignapiservice`.`country` = "KG" and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS NOT NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `bananaapi-number`.`taked` = 1 and `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb`) AS Has_sms,
        // (SELECT COUNT(*) FROM `requests_log`,`foreignapiservice`,`bananaapi-number` where `foreignapiservice`.`country` = "KG" and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS  NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `bananaapi-number`.`taked` = 1 and `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb`) AS Has_no_sms';
        // //,(SELECT GROUP_CONCAT(DISTINCT  foreignapiservice.Name) as applications FROM `requests_log`,`foreignapiservice` where `foreignapiservice`.`country_name` = "Kyrgyzstan" and requests_log.service = `foreignapiservice`.Id_Service_Api ) As application
    include 'backend/config.php';
    
    $start_time = microtime(true);
    $sql = "select `country_code` from `bananaapi-number` where taked = 0 GROUP by `country_code`";
    // Execute the query
    $sql = 'SELECT 
    COUNT(*) AS total,
    SUM(taked = 0) AS avilable,
    SUM(taked = 1) AS Requested,
    SUM(requests_log.sms_content IS NOT NULL) AS Has_sms,
    SUM(requests_log.sms_content IS NULL) AS Has_no_sms
    FROM `bananaapi-number`
    LEFT JOIN requests_log ON requests_log.Phone_Nb = `bananaapi-number`.phone_number
    LEFT JOIN foreignapiservice ON requests_log.service = foreignapiservice.Id_Service_Api
    WHERE `bananaapi-number`.country_code = "KG"
    AND foreignapiservice.country = "KG"
    AND foreignapiservice.Id_Foreign_Api = 17';

    // $sql = 'SELECT 
    // (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = "KG") AS total,
    // (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = "KG") AS avilable, 
    // (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = "KG") AS Requested, 
    // (SELECT COUNT(*) FROM `requests_log` JOIN `foreignapiservice` ON `requests_log`.`service` = `foreignapiservice`.`Id_Service_Api` JOIN `bananaapi-number` ON `bananaapi-number`.phone_number = `requests_log`.`Phone_Nb` WHERE `foreignapiservice`.`country` = "KG" AND `requests_log`.`sms_content` IS NOT NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`taked` = 1) AS Has_sms,
    // (SELECT COUNT(*) FROM `requests_log` JOIN `foreignapiservice` ON `requests_log`.`service` = `foreignapiservice`.`Id_Service_Api` JOIN `bananaapi-number` ON `bananaapi-number`.phone_number = `requests_log`.`Phone_Nb` WHERE `foreignapiservice`.`country` = "KG" AND `requests_log`.`sms_content` IS  NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`taked` = 1) AS Has_no_sms';



    $stmt = $pdo->prepare($sql);
    $stmt->execute([]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ini_set('output_buffering', 'off');
    var_dump($results);
    // Turn on implicit flush
    ob_implicit_flush(true);

    // Send some data to the client
    echo "Starting...\n";

    flush();

    // if(sizeof(($results)>0)){
    //     foreach($results as $res){
    //         $sql =   $sql = 'SELECT 
    //         (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = :country_code) AS total,
    //         (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = :country_code) AS avilable, 
    //         (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = :country_code) AS Requested, 
    //         (SELECT COUNT(*) FROM `requests_log` JOIN `foreignapiservice` ON `requests_log`.`service` = `foreignapiservice`.`Id_Service_Api` JOIN `bananaapi-number` ON `bananaapi-number`.phone_number = `requests_log`.`Phone_Nb` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`sms_content` IS NOT NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`taked` = 1) AS Has_sms,
    //         (SELECT COUNT(*) FROM `requests_log` JOIN `foreignapiservice` ON `requests_log`.`service` = `foreignapiservice`.`Id_Service_Api` JOIN `bananaapi-number` ON `bananaapi-number`.phone_number = `requests_log`.`Phone_Nb` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`sms_content` IS  NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`taked` = 1) AS Has_no_sms';
    //         $stmt = $pdo->prepare($sql);
    //         $stmt->execute(array("country_code" => $res['country_code']));
    //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         var_dump($result);
    //         echo "<br>";
    //         flush();


    //     }
    // }
    // End the timer and calculate elapsed time
    $end_time = microtime(true);
    $elapsed_time = $end_time - $start_time;

    // Output the results and elapsed time
    echo "Query results: ";
    #print_r($results);
    echo "Elapsed time: " . number_format($elapsed_time, 6) . " seconds";
    }
    function countryenable(){
        include 'backend/config.php';
        for ($i = 1; $i <= 240; $i++) {
            // Generate random password
            
            $sql = "INSERT INTO `countries_enabled`(`country_id`, `enabled`) VALUES  (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$i, 1]);
        
            // Print username and password
        
            //echo "Password: $password";
        }
        
        // Close database connection
    
}

function createuser(){
    include 'backend/config.php';
    for ($i = 4; $i <= 25; $i++) {
        // Generate random password
        $password = bin2hex(random_bytes(5)); // Generates a random string of 8 characters (4 bytes)
    
        // Create user record
        $username = 'user_' . $i;
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO upload_user (username, user_pass) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password_hash]);
    
        // Print username and password
        echo "Username: $username , $password <br>";
        //echo "Password: $password";
    }
    
    // Close database connection
   
}

function InsertApplications(){
    include 'backend/config.php';
    $new_id = 22;
    $desc = "vak-sms.com";
        // Fetch the records you want to iterate over
    $stmt = $pdo->prepare("select * from `foreignapiservice` where `Id_Foreign_Api` = 17 and code = 'wa'");
   // $stmt->bindParam(":value", $value);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Edit the data in each row (if needed)
    foreach ($rows as &$row) {
    $row['Id_Foreign_Api'] = $new_id;
    $row['description'] = $desc;

    unset($row['Id_Service_Api']);
    unset($row['Modification_Date']);

    $columns = "";
    $values = "";

    foreach ($row as $key => $value) {
        $columns .= "$key, ";
        $values .= ":$key, ";
    }
    $columns = rtrim($columns, ", ");
    $values = rtrim($values, ", ");

    $stmt = $pdo->prepare("INSERT INTO `foreignapiservice` ($columns) VALUES ($values)");
    foreach ($row as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->execute();
    }

    echo "Finish";
}

function getpassword($password){
    //$password = 'LP@online@123'; // the password to hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // hash the password using the default algorithm
    echo $hashed_password;
}

//parsenumber();
function parsenumber(){
    $string = "Your  code: 585-058You can also tap on this link to verify your phone: v..com/585058Don't share this code with others";
    #$string = "WhatsApp :>4: 269-1381>: v.whatsapp.com\/269138>4 ?@820B=89";
  //     if (strpos(strtolower($string), "whatsapp") !== false) {
    $string = " 308-0511>: v..com/308051>4 ?@820B=89";
    $string = str_replace(":>4:", "", $string);
    $all_digits = preg_replace('/\D/', '', $string);
     $size = strlen($all_digits);

    if ($size >= 6) {
       $code = substr($all_digits, 0, 6);
    } else {
        $code= "";
    }
echo $code;
die();


    if (strpos(strtolower($string), "whatsapp") !== false || strpos(strtolower($string), "can also tap on this link") !== false) {
    $pattern = '/\d{3}-\d{3}/';
    preg_match($pattern, $string, $matches);
    if(sizeof($matches) >=1){
    $number = $matches[0];
    $clean_string = str_replace("-", "", $number);

    
    #$string = "WhatsApp :>4: 269-1381>: v.whatsapp.com/269138>4 ?@820B=89";
    #$pattern = '/(?<=\/)\d+(?=>)/';
    #preg_match($pattern, $string, $matches);
    #$number = $matches[0];
    

   // outputs 269-1381
    }
    else{
        $clean_string = "notfound";
    }
    echo $clean_string; 
    }
}
function splitapp(){
    $data = '{"application":["tisktok"],"numbers":[{"phone_number":"123123","source":"dev","country_code":"cd"},{"phone_number":"22222","source":"dev","country_code":"cd"}]}';
      $req_dump = json_decode($data, JSON_UNESCAPED_SLASHES);
            $Final_results = (array) [];
            $applist = array("whatsapp", "facebook", "google","telegram","tiktok","instagram","zoom","snapchat","apple","coinbase","imo","line","linkedin","microsoft","netflix","protonmail","signal","openai","steam","twitter","uber","wechat","yahoo","zoho","vivo","qsms","lazada","bigotv","wesing");
            if(isset($req_dump["numbers"])){
                if (!empty($req_dump['numbers'])) {
                        if (isset($req_dump['application'])){
                            if (sizeof($req_dump['application']) ==1 ){
                                if ($req_dump['application'][0] == "any")
                                { 
                                    $req_dump["application"] = $applist;
                                    
                                }
                               
                            }
                            
                            $Final_results["application"]= $req_dump["application"];
                            $check = array_intersect($Final_results["application"], $applist);
                            if ($check != $Final_results["application"]) {
                               
                                echo "error with application name ".json_encode($req_dump['application']);
                                die();
                              }

                             
                        }
                    //     $Final_results["numbers"] = (array) [];
                    //  foreach ($req_dump['numbers'] as  $value) {
                    //     if (isset($value["country_code"]) && isset($value["phone_number"]) ) {
                    //      array_push($Final_results["numbers"], $value);

                    //  }

                    
                }
            $res = json_encode($Final_results,JSON_PRETTY_PRINT);
            echo $res;
}
}





function getnumberfromstring(){
    $string = "853 701 is your Instagram code. Don't share it.";
$all_digits = preg_replace('/\D/', '', $string);
$size = strlen($all_digits);

if ($size >= 6) {
    $first_6_chars = substr($all_digits, 0, 6);
   
   
} else {
    echo "The string is too short";
}
echo $first_6_chars;

}
 function jsontest()
{
    include 'backend/config.php';
    try {
        $result = (object) [
            'ResponseCode' => 1,
            'Msg' => 'OK',
            'Result' => null,
        ];
        $stmt = $pdo->prepare('SELECT `country_code`, COUNT(*) as count FROM `bananaapi-number` where `taked` = 0 GROUP BY `country_code`');
        $stmt->execute();
        $res = $stmt->fetchAll();
        $stmt->closeCursor();
        if (sizeof($res) > 0) {
            
            $array = (array) $res;
            $result->ResponseCode = 0;
            $result->Result = $array;
           
    
             echo json_encode($result);


        }
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
       
    }
}


function newChannel(){

$data = '
       [ {
        "country": "IN",
        "code": "91810786044",
        "gid": "50381dc7-ef89-48f1-882a-ab883d9f6baa",
        "ipub": "MDVDNEIwRkVGQjBBRThGQTgyNTU0OEZGREZDNUE4MkQ1NThCODRDOEIzMEFBQjdFMTc1N0E1NDc2MEZDODBCMTNB",
        "channel": "VD2LyhezO8SmWIaYwuVZhK",
        "rid": "474488508",
        "manufacturer": "Xiaomi",
        "uid": "2ctp7d76oel45h4znxj0oipkj",
        "clientKey": "YAgRmTk2hDu9wjbzFdZqrHM1V3UxRk7TtQjx2AC/rXxZBX5kBC3bsuzHEjOx88Rm77Vz384YiDxnt3mgWDtUNg",
        "me": "rO0ABXNyAA9jb20ud2hhdHNhcHAuTWXk6K3RrOBlqgIAA0wAAmNjdAASTGphdmEvbGFuZy9TdHJpbmc7TAAJamFiYmVyX2lkcQB-AAFMAAZudW1iZXJxAH4AAXhwdAACOTF0AAw5MTgxMDc4NjA0NDF0AAo4MTA3ODYwNDQx",
        "keystore": "PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0ndXRmLTgnIHN0YW5kYWxvbmU9J3llcycgPz4KPG1hcD4KICAgIDxsb25nIG5hbWU9ImNsaWVudF9zdGF0aWNfa2V5cGFpcl9lbmNfc3VjY2VzcyIgdmFsdWU9IjUwMSIgLz4KICAgIDxib29sZWFuIG5hbWU9ImNhbl91c2VyX2FuZHJvaWRfa2V5X3N0b3JlIiB2YWx1ZT0idHJ1ZSIgLz4KICAgIDxzdHJpbmcgbmFtZT0iY2xpZW50X3N0YXRpY19rZXlwYWlyX2VuYyI-WzAsJnF1b3Q7VXgyazJXWmhsZEt0bWtMZmxlbTJrVmYrTExmOFpLR0M5aXZkYit5RUdxYXRxdUVuUlpCdnFxQ05wMjR3c2JcL0dQR0pQa0pjelU5d2dCYlFwS1JrY2psczRpRm1IcFVHbUZUbVRTQ1NHbGRFJnF1b3Q7LCZxdW90O2J1K3NkTVVwTXhtbjR0U3AmcXVvdDtdPC9zdHJpbmc-CiAgICA8c3RyaW5nIG5hbWU9InNlcnZlcl9zdGF0aWNfcHVibGljIj5xSld2U3R0Tm9wcWdRMkNnWFlUYzRqbVNVS1dkMVJ2MlFUTWJReVlwS3dZPC9zdHJpbmc-CjwvbWFwPgo",
        "model": "Redmi Note 5 Pro",
        "barnd": "xiaomi",
        "pushName": "",
        "rc2": "rO0ABXVyAAJbQqzzF_gGCFTgAgAAeHAAAAAqAAIjFeKD1kMTjKq98pBcNJHVZUUkma7sFrNJ1KBwVT-izHiaNZgFzO_j",
        "waVersion": "2.22.17.76",
        "logintime": "1673497625",
        "display": "OPM1.171019.011",
        "ip": "2401:4900:5279:493b:615d:cc4d:3281:b773",
        "serverKey": "qJWvSttNopqgQ2CgXYTc4jmSUKWd1Rv2QTMbQyYpKwY",
        "kv": "4",
        "version": "8.1.0",
        "ipri": "Mjg2NkUyMkNEMUIwMDdGMzU0Q0ZCQkM1NDZGODdBNkQ0NjcwQjAyN0M2RkQzM0E0N0MwQURDRTA1N0Y4QjI1Qg",
        "spk": "MDgxQTEyMjEwNUVCMkNBNkE0N0Y3OTU2RkREMDIyNERFNjM3OUZFMDdCNTQ3ODU4RjlDRjUwMUFDQTlCQkU2MTZGNzVGNEI3NkExQTIwOTgzNUU1MjRFMUQ0QURFQzBDNDUwOUVBQzUxMDI0NjNFNjMyNjdDNkEyRTc0NERGOEMwNUJEMDZFMThFQTk1NjIyNDAwNzdENzIwOTEyMjJBODE3Rjc0OEZDOEI3RENGOURGNjJFNTcyMDkwOTdGNDRCQzUxRDgxOTlCRDY0RjNBMjIyRjk2RjQ5QUEyQkU1RTA5OEY2NDJDNTgyQUQyQzI3NjkzNjYxQjBBQTk4NzM5MkY4OTE3QzZFODUzMzMyOTIwNzI5QTVEMjAwMDAwMDAwMDAwMA",
        "createTime": "1673497643892",
        "aid": "f0dbb62bf7ebb260",
        "board": "sdm636",
        "callTime": "-1"
    }
]

';

//$fname = "newch.json";
readobj( $data);

}

function newapi()
{
//  setlocale(LC_TIME, "zh_CN");  
/*
	$now = time ();
Date_default_timezone_set ('Asia/Shanghai');
$p= date('C', $now );
echo $p .'</br>';*/
Date_default_timezone_set ('Asia/Hong_Kong');

$p = new DateTime(null, new DateTimeZone('Asia/Hong_Kong'));
$p = round($p->getTimestamp() *1000);
echo $p;
    $date = date_create();
   $partnerKey = "oxRPoeFZ9XCTnvlG";
   
   $apikey = '3QKGMwwadU2cTXDN';
   $t = date_timestamp_get($date);
//    $t = 1632157522;

   //echo $t. " ==> ". date('Y-m-d H:i:s','1632339026').PHP_EOL."<br>";
   $country = 'IN';
   $sig =  md5( "apikey=" .$apikey . "country=".$country . "t=" .$p .$partnerKey );
    
    echo "sig: "."apikey=" .$apikey . "country=".$country . "t=" .$p.$partnerKey."<br><br>";
    echo "md5=".$sig."<br><br>";
    $url = "http://qnvtbj.xjwi5.com/udaa/api/wa/pull/6fogoz?apikey=".$apikey."&country=".$country."&t=".$p."&sig=".$sig."&cnt=1";
    echo "url:".$url."<br><br>";



    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_PORT => "80",
        CURLOPT_URL =>  $url, #"http://juwuat.74hcjs.com:10238/nMl4aQIaZ9hU/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 50,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
       // CURLOPT_POSTFIELDS => $myobj,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
        ],
       
    ]);

    $response = curl_exec($curl);
    
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo "response: ".$response;
    }



}


function readobj($data)
{
    $data = file_get_contents("temp/t.json");

    $data = json_decode($data, true);
    foreach ($data as $nb) {
        //var_dump($nb);
        $rc2 = $nb['rc2'];
        $me = $nb['me'];
        $me = str_replace('-', '+', $me);
        $me = base64_decode($me);
        $keystore = $nb['keystore'];
        $keystore = str_replace('-', '+', $keystore);
        $keystore = base64_decode($keystore);
        preg_match_all('!\d+!', $me, $phone);
        //var_dump($phone);
        $phone = $phone[0];

        $cc = $phone[0];

        $phone_without_cc = $phone[2];

        //echo $phone."<br>".$cc."<br>".$phone_without_cc;
        //die();

        mkdir("temp/emulator/" . $phone[1]);
        system("cp -a temp/com.whatsapp temp/emulator/" . $phone[1] . "/");
        file_put_contents("temp/emulator/" . $phone[1] . "/com.whatsapp/files/me", $me);
        file_put_contents("temp/emulator/" . $phone[1] . "/com.whatsapp/files/rc2", $rc2);
        file_put_contents("temp/emulator/" . $phone[1] . "/com.whatsapp/shared_prefs/keystore.xml", $keystore);
        xmlregisterphone("temp/emulator/" . $phone[1] . "/com.whatsapp/shared_prefs/", $cc, $phone_without_cc);
        xmlpreflight("temp/emulator/" . $phone[1] . "/com.whatsapp/shared_prefs/", $cc, $phone_without_cc);

//    zipanddownload("temp/emulator/".$phone[1]."/","temp/emulator/".$phone[1].".zip");
        //echo "rc2: ".$rc2. "<br> me: ".$me."<br> key: ".$keystore."<br>phone: ".$phone[1];
        //var_dump($phone);
        // break;

    }
    zipanddownload("temp/emulator/", "temp/emulator.zip");

}
function zipanddownload($dir, $zip_file)
{
// Get real path for our folder
    $rootPath = realpath($dir);

// Initialize archive object
    $zip = new ZipArchive();
    $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        if (!$file->isDir()) {
            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        } else {
            if ($relativePath !== false) {
                $zip->addEmptyDir($relativePath);
            }

        }
    }

// Zip archive will be created only after closing object
    $zip->close();

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($zip_file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zip_file));
    readfile($zip_file);
}
function xmlpreflight($dirname, $cc, $phone)
{

    $dom = new DOMDocument();
    $dom->load($dirname . 'com.whatsapp_preferences_light.xml');
    $library = $dom->documentElement;
    //echo $library->childNodes->item(11)->nodeValue;
    $library->childNodes->item(11)->nodeValue = $phone;
    $library->childNodes->item(43)->nodeValue = $cc . $phone;
    $library->childNodes->item(89)->nodeValue = $cc;
    // 2nd way #$library->getElementsByTagName('book')->item($cnt-1)->getElementsByTagName('title')->item(0)->nodeValue .= ' Series';

    // 3rd Way
    // $library->childNodes->item($cnt-1)->childNodes->item(0)->nodeValue .= ' Series';
    //header("Content-type: text/xml");
    $dom->save($dirname . 'com.whatsapp_preferences_light.xml');
}

function xmlregisterphone($dirname, $cc, $phone)
{
    $dom = new DOMDocument();
    $dom->load($dirname . 'registration.RegisterPhone.xml');
    $library = $dom->documentElement;
    //var_dump($library);
    $cnt = $library->childNodes->length;
    //echo $cnt;
    $library->childNodes->item(1)->nodeValue = $phone;
    $library->childNodes->item(7)->nodeValue = $phone;
    $library->childNodes->item(11)->nodeValue = $cc;
    $library->childNodes->item(13)->nodeValue = $cc;
    // 2nd way #$library->getElementsByTagName('book')->item($cnt-1)->getElementsByTagName('title')->item(0)->nodeValue .= ' Series';

    // 3rd Way
    // $library->childNodes->item($cnt-1)->childNodes->item(0)->nodeValue .= ' Series';
    //header("Content-type: text/xml");
    $dom->save($dirname . 'registration.RegisterPhone.xml');
}

function callapi()
{
    $uuid = "99999999999999999999";
    $date = date_create();
    $uuid = md5($uuid . date_timestamp_get($date));
    // $myarray = array(
    //     't' => date_timestamp_get($date),
    //     'channel' => "PVUTdPeXI",
    //     'key' => "K2dU3o94rEh0",
    //     'callBackUrl' => "http://old-channels.mixsimverify.com/receiver2.php?id=" . $uuid,
    //     'cnt' => '1',
    //     'countries' => "",
    //     'first' => '1',
    //     'upType' => 1,
    //     'needKeys' => "client_static_keypair_pwd_enc",
    // );
    $first = '1';
    $cnt = '1';
    $countries = '';

    $myarray = array(
        't' => date_timestamp_get($date),
        'channel' => "PVUTdPeXI",
        'key' => "K2dU3o94rEh0",
        'callBackUrl' => "http://old-channels.mixsimverify.com/receiver2.php?id=" . $uuid,
        'cnt' => $cnt,
        'countries' => $countries,
        'first' => $first,
        'upType' => 1,
        'needKeys' => "client_static_keypair_pwd_enc",
    );
    $keys = array_keys($myarray);
    sort($keys);
    $sig = "";
    $myobj = "";
    foreach ($keys as $key) {
        if ($key == "key") {
            continue;
        }

        $sig .= $key . "=" . $myarray[$key];
        $myobj .= $key . "=" . $myarray[$key] . "&";
    }
    $sig = $sig . $myarray['key'];
    $sig = md5($sig);
    $myobj = $myobj . "sig=" . $sig;

//        file_put_contents("/var/www/html/oldChannels/test.log","old-in ask\n\n");

    //return array("Code" => 0, "ErrMsg" => "",'Id'=>$sig,'ANY'=> $myobj);
    //return $myobj;
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_PORT => "9940",
        CURLOPT_URL => "http://161.117.234.9/owxmdghqgx/", #"http://juwuat.74hcjs.com:10238/nMl4aQIaZ9hU/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 50,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $myobj,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
        ],

    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    //file_put_contents("/var/www/html/oldChannels/test.log","old-in res ".$response."\n\n");
    //echo $response;
    curl_close($curl);

    if ($err) {
        // file_put_contents("/var/www/html/oldChannels/test.log","old-in res ".$err."\n\n");

        echo "cURL Error #:" . $err;
    } else {
        //file_put_contents("/var/www/html/oldChannels/test.log","old-in res ".$response."\n\n");

        echo $response;
    }

}
