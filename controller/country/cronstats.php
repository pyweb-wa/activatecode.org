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

    sleep(20);
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
    include '/var/www/smsmarket/html/backend/config.php';

    echo "deleteing all unwanted data\n";
    
    
    $sql = "WITH source_to_delete AS (
        SELECT `bananaapi-number`.`source` from `bananaapi-number`,`countries_control` where`bananaapi-number`.`createdTime` <= (NOW() - INTERVAL 7 MINUTE) and `bananaapi-number`.`source` not IN (SELECT source from `country_stats`) GROUP by `bananaapi-number`.`source`
      )
      DELETE FROM `bananaapi-number`
      WHERE `source` IN (SELECT `source` FROM source_to_delete);
      DELETE FROM `countries_control` WHERE `source` IN (SELECT `source` FROM source_to_delete);
      ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        // $query = "DELETE FROM `countries_control` where `countries_control`.`source` not in (select source from `bananaapi-number` GROUP by source)";
        // $stmt = $pdo->prepare($query);
        // $stmt->execute();




            
//        try {
//     $pdo->beginTransaction();

//     // Step 1: Identify sources to delete
//     $sourceToDeleteQuery = "
//         WITH source_to_delete AS (
//             SELECT `bananaapi-number`.`source`
//             FROM `bananaapi-number`
//             JOIN `countries_control` ON `bananaapi-number`.`source` = `countries_control`.`source`
//             WHERE `bananaapi-number`.`createdTime` <= (NOW() - INTERVAL 7 MINUTE)
//               AND `bananaapi-number`.`source` NOT IN (SELECT `source` FROM `country_stats`)
//             GROUP BY `bananaapi-number`.`source`
//         )
//     ";

//     // Step 2: Delete records from bananaapi-number
//     $deleteBananaapiQuery = "
//         DELETE FROM `bananaapi-number`
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
        
        
        // if ($del == 1){
        //     deteted_unwanted();
        // }
        include '/var/www/smsmarket/html/backend/config.php';
        // $sql = "SELECT `bananaapi-number`.`createdTime` t FROM `bananaapi-number` group by t ORDER BY t DESC limit 1";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute([]);
        // $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $currentday = $results[0]['t'];

        $sql = "SELECT
		`t`.`country_id`,
        `t`.`country_char`,
        `t`.`source`,
        `t`.`createdTime`,
        `t`.`country`,
        `t`.`country_code`,
        `t`.`enabled`,
        `t`.`start`,
        `t`.`stop`,
        `t`.`mstart`,
        `t`.`mstop`
    FROM
        (
            SELECT
                `bananaapi-number`.`country_code` AS country_char,
                `bananaapi-number`.`source`,
                MAX(`bananaapi-number`.`createdTime`) AS createdTime,
                `countryList`.`country`,
            	`countryList`.`id` as country_id,
                `countryList`.`country_code`,
                `countries_control`.`enabled`,
                `countries_control`.`start`,
                `countries_control`.`stop`,
                `countries_control`.`mstart`,
                `countries_control`.`mstop`
            FROM
                `bananaapi-number`
            JOIN
                `countries_control` ON `bananaapi-number`.`source` = `countries_control`.`source`
            JOIN
                `countryList` ON `bananaapi-number`.`country_code` = `countryList`.`country_char`
            WHERE
                `bananaapi-number`.`is_finished` = 0
            GROUP BY
                `bananaapi-number`.`country_code`,
                `countryList`.`country`,
            	`countryList`.`id`,
                `countryList`.`country_code`,
                `countries_control`.`enabled`,
                `countries_control`.`start`,
                `countries_control`.`stop`,
                `countries_control`.`mstart`,
                `countries_control`.`mstop`,
                `bananaapi-number`.`source`
        ) AS t;";
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
                // if ($res['country_char'] != "CL") {

                //     continue;
                // }
                
                $sql = "SELECT
                (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = :country_code and `source` = :source and `bananaapi-number`.`createdTime` = :currentday) AS total,
                 (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = :country_code and `source` = :source and `bananaapi-number`.`createdTime` = :currentday) AS available,
                (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = :country_code and `source` = :source and `bananaapi-number`.`createdTime` = :currentday) AS requested,
                (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`,`foreignapiservice`,`bananaapi-number` where `foreignapiservice`.`country` = :country_code  and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS NOT NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `bananaapi-number`.`is_finished` = 1 and `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb` and `bananaapi-number`.`source` = :source and `requests_log`.`TimeStmp` >= :currentday) AS has_sms,
                 (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`, `foreignapiservice`, `bananaapi-number` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`service` = `foreignapiservice`.Id_Service_Api AND `requests_log`.`sms_content` IS NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`is_finished` = 0 AND `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb` AND `bananaapi-number`.`source` = :source and `requests_log`.`TimeStmp` >= :currentday ) AS no_sms,
                (SELECT enabled FROM `countries_control` WHERE source=:source and `countries_control`.`country_id` =:country_id) AS `status`";
                if ($pdo == null){
                    echo "can't find pdo";
                    return; 
                 }
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array("country_code" => $res['country_char'], "source" => $res['source'], "currentday" => $res['createdTime'], "country_id" => $res['country_id']));
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (sizeof($result) > 0) {
                    $result = $result[0];
                    $result['country'] = $res['country'];
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
                    $result['pushtime'] = $res['createdTime'];
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
                    // $country["status"],
                    $country["stop"],
                    $country["total"],
                    $country["Mstop"],
                    $country["Mstart"],
                    $country["available"],
                    $country["pushtime"],
                ]);
            }

        }

    } catch (Exception $e) {
        echo $sql . "\n\n ".json_encode($res)."\n\n";
        echo 'Caught exception: ', $e->getMessage(), "\n";

    }

}
