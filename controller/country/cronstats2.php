<?php
// require_once "validate_token.php";
// if (!checkTokenInDatabase()) {
//     header('Location: index.php');
//     exit(); 
// }
date_default_timezone_set('Asia/Beirut');
$count = 0;
$del = 1;
while (true) {
    $start = microtime(true);
 
    
    stats($del);
    $end = microtime(true);
    $executionTime = $end - $start;

    echo "Script execution time: " . $executionTime . " seconds" . PHP_EOL;

    sleep(15);
    $count = $count + 1;
    if ($count >10){
        $count = 0;
        $del = 1;
    }
    else{

        $del = 0;
    }
    
    
}

function deteted_unwanted(){
   // include '/var/www/smsmarket/html/backend/config.php';
    include 'config.php';

    echo "deleteing all unwanted data\n";
    
    
    $sql = "WITH source_to_delete AS (
        SELECT `redis_numbers`.`source` from `redis_numbers`,`countries_control` where`redis_numbers`.`createdTime` <= (NOW() - INTERVAL 7 MINUTE) and `redis_numbers`.`source` not IN (SELECT source from `country_stats`) GROUP by `redis_numbers`.`source`
      )
      DELETE FROM `redis_numbers`
      WHERE `source` IN (SELECT `source` FROM source_to_delete);
      DELETE FROM `countries_control` WHERE `source` IN (SELECT `source` FROM source_to_delete);
      ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        // $query = "DELETE FROM `countries_control` where `countries_control`.`source` not in (select source from `redis_numbers` GROUP by source)";
        // $stmt = $pdo->prepare($query);
        // $stmt->execute();




            
//        try {
//     $pdo->beginTransaction();

//     // Step 1: Identify sources to delete
//     $sourceToDeleteQuery = "
//         WITH source_to_delete AS (
//             SELECT `redis_numbers`.`source`
//             FROM `redis_numbers`
//             JOIN `countries_control` ON `redis_numbers`.`source` = `countries_control`.`source`
//             WHERE `redis_numbers`.`createdTime` <= (NOW() - INTERVAL 7 MINUTE)
//               AND `redis_numbers`.`source` NOT IN (SELECT `source` FROM `country_stats`)
//             GROUP BY `redis_numbers`.`source`
//         )
//     ";

//     // Step 2: Delete records from redis_numbers
//     $deleteBananaapiQuery = "
//         DELETE FROM `redis_numbers`
//         WHERE `source` IN (SELECT `source` FROM source_to_delete)
//     ";

//     // Step 3: Delete records from countries_control
//     $deleteCountriesQuery = "
//         DELETE FROM `countries_control`
//         WHERE `source` IN (SELECT `source` FROM source_to_delete)
//     ";

//     // Execute queries
//     $pdo->exec($sourceToDeleteQuery);
//     echo 1;
//     $pdo->exec($deleteBananaapiQuery);
//     echo 2;
//     $pdo->exec($deleteCountriesQuery);
//      echo 3;       
//     // Commit the transaction
//     $pdo->commit();

//     echo "Transaction successfully completed.";

// } catch (PDOException $e) {
//     // An error occurred, rollback the transaction
//     $pdo->rollBack();
//     echo "Transaction failed: " . $e->getMessage();
// }
                echo "end of deleting\n";
}

function stats($del)
{

    try {
        
        
        //include '/var/www/smsmarket/html/backend/config.php';
        include '/var/www/smsmarket/html/backend/redisconfig.php';
        include 'config.php';


        $sql = "SELECT * FROM `countries_control`";
        if ($pdo == null){
           return; 
        }
       
        $stmt = $pdo->prepare($sql);
        $stmt->execute([]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = [];
       // var_dump($results);
        if (sizeof($results) > 0) {
            foreach ($results as $res) {
               

                $sql = "SELECT
               
                (SELECT COUNT(*) FROM `redis_numbers` WHERE taked = 1 and `country_code` = :country_code and `source` = :source and `redis_numbers`.`createdTime` = :currentday) AS requested,

                (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`,`foreignapiservice`,`redis_numbers` where `foreignapiservice`.`country` = :country_code  and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS NOT NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `redis_numbers`.`is_finished` = 1 and `redis_numbers`.`phone_number` = `requests_log`.`Phone_Nb` and `redis_numbers`.`source` = :source and `requests_log`.`TimeStmp` >= :currentday) AS has_sms,

                (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`, `foreignapiservice`, `redis_numbers` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`service` = `foreignapiservice`.Id_Service_Api AND `requests_log`.`sms_content` IS NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `redis_numbers`.`is_finished` = 0 AND `redis_numbers`.`phone_number` = `requests_log`.`Phone_Nb` AND `redis_numbers`.`source` = :source and `requests_log`.`TimeStmp` >= :currentday ) AS no_sms,

                (SELECT enabled FROM `countries_control` WHERE source=:source and `countries_control`.`country_id` =:country_id) AS `status`";

                if ($pdo == null){
                    echo "can't find pdo";
                    return; 
                 }
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array("country_code" => $res['country_char'], "source" => $res['source'], "currentday" => $res['created_time'], "country_id" => $res['country_id']));
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (sizeof($result) > 0) {
                    $result = $result[0];
                    $result['country'] = $res['country_char'];
                    $result['country_code'] = $res['country_code'];
                    $result['country_char'] = $res['country_char'];
                    $result['status'] = $res['enabled'];
                    $start = strtotime($res['start']);
                    $start = date('h:i a', $start);
                    // $start = DateTime::createFromFormat('h:i A', $res['start']);
                    // $start = $start->format('H:i:s');
                    $stop = strtotime($res['stop']);
                    $stop = date('h:i a', $stop);
                    $result['start'] = $start;
                    $result['stop'] = $stop;
                    $result['Mstart'] = intval($res['mstart']);
                    $result['Mstop'] = intval($res['mstop']);
                    $result['source'] = $res['source'];
                    $result['pushtime'] = $res['created_time'];
                    $result['servertime'] = date('Y-m-d H:i:s', time());

                    array_push($response, $result);
                    echo json_encode($res)."\n\n";
                    // break;
                }

            }
           
        }
       
        // echo json_encode($response);
        // Retrieve existing status data
        $existingStatus = [];
        $selectExisting = "SELECT `country`,`source`, `status` FROM `country_stats`";
        $existingStmt = $pdo->query($selectExisting);
        while ($row = $existingStmt->fetch(PDO::FETCH_ASSOC)) {
            $existingStatus[$row["country"] . "_" . $row["source"]] = $row['status'];
        }

        $sql = "INSERT INTO `country_stats` (`country`, `country_char`, `country_code`, `has_sms`, `no_sms`, `requested`, `servertime`, `source`, `start`, `status`, `stop`, `total`, `Mstop`, `Mstart`, `available`,`pushtime`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ";

        if (count($response) > 0) {
            $todel = "truncate table country_stats";
            if ($pdo == null){
                return; 
             }
            $pdo->exec($todel);
            foreach ($response as $key => $country) {
                $status = isset($existingStatus[$country["country"] . "_" . $country["source"]]) ? $existingStatus[$country["country"] . "_" . $country["source"]] : $country["status"];
                echo $country["country"] . "_" . $country["source"] . " new_status= " . $status . "\n";
                echo $country["country"] . "_" . $country["source"] . " old_status= " . $country["status"] . "\n------------\n";
                $redisKey = "live_".$country["source"];
               // $jsonValue = $redis->get($redisKey);
                $available = $redis->scard($redisKey);

                // $data = json_decode($jsonValue, true);
                // $available = sizeof($data);
                $total = intval($available) + intval($country["requested"]);
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $country["country"],
                    $country["country_char"],
                    $country["country_code"],
                    $country["has_sms"],
                    $country["no_sms"],
                    $country["requested"],
                    $country["servertime"],
                    $country["source"],
                    $country["start"],
                    $status,
                    $country["stop"],
                    $total,
                    $country["Mstop"],
                    $country["Mstart"],
                    intval($available),
                    $country["pushtime"],
                ]);
            }

        }

    } catch (Exception $e) {
        echo $sql . "\n\n ".json_encode($res)."\n\n";
        echo 'Caught exception: ', $e->getMessage(), "\n";

    }

}
