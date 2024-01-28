<?php

include '/var/www/smsmarket/html/backend/config_1.php';
require '/var/www/smsmarket/html/backend/redisconfig.php';
first_launch();
while(true){
$currentStartDate = date("Y-m-d");
today_data();
sleep(10*60);
$DateNow = date("Y-m-d");
if ($currentStartDate < $DateNow) {
    ## need to move append rediskey users_rate:old with data of $currentStartDate
    $liveRedisKey = "users_rate:live";
    $oldRedisKey = "users_rate:old";
    $live_data = $redis->get($liveRedisKey);
    $live_data = json_decode($live_data,true);
    $old_data =$redis->get($oldRedisKey);
    $old_data = json_decode($old_data,true);
    $mergedArray = array_merge($live_data, $old_data);
    $mergedArray = json_encode($mergedArray);
    $redis->set($oldRedisKey,$mergedArray);
}
}

function first_launch(){
    $startTime = microtime(true);
    $resultArray = []; // Initialize an array to store results

    $sql = "SELECT
                `requests_log`.`service` AS service,
                Id_user AS Id_user,
                DATE(`requests_log`.`TimeStmp`) AS date
                FROM
                `requests_log`
                WHERE
                DATE(`requests_log`.`TimeStmp`) >= DATE_SUB(CURDATE(), INTERVAL 4 DAY)
                AND date(TimeStmp) < CURDATE()
                GROUP BY
                service, Id_user, date;";
    $selectStmt = $GLOBALS['pdo']->prepare($sql);
    $selectStmt->execute();
    $results = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
    echo count($results)."\n\n";
    foreach ($results as $row) {
        echo  json_encode($row)."\n\n";
        $startTime_2 = microtime(true);
        $selectQuery = "SELECT
                users.name AS user,
                COUNT(requests_log.Id_request) AS total,
                COUNT(CASE WHEN requests_log.smsCode IS NOT NULL THEN 1 END) AS has_sms,
                foreignapiservice.country
                FROM
                requests_log
                JOIN
                users ON requests_log.Id_user = users.Id
                JOIN
                foreignapiservice ON foreignapiservice.Id_Service_Api = requests_log.service
                WHERE
                requests_log.service = :service
                AND DATE(requests_log.TimeStmp) = :date
                AND requests_log.Id_user = :Id_user
                GROUP BY
                users.name, foreignapiservice.country;";
        $selectStmt = $GLOBALS['pdo']->prepare($selectQuery);
        $selectStmt->bindParam(':service', $row['service']);
        $selectStmt->bindParam(':Id_user', $row['Id_user']);
        $selectStmt->bindParam(':date', $row['date']);
        $selectStmt->execute();
        $data = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        $data = $data[0];
        $data['date'] = $row['date'];
        $percentage = (intval($data['has_sms']) / intval($data['total'])) * 100;
        $data['percentage'] = $percentage;
        echo $data['user']." ==> ".$data['country'] ." ==> "." $percentage % \n\n";
        // Append the data to the result array
        $resultArray[] = $data;
        $endTime = microtime(true);
        // Calculate the execution time in seconds
        $executionTime = $endTime - $startTime_2;
        // Display the result
        echo "Execution time: " . number_format($executionTime, 5) . " seconds\n\n";
    }

    // Display or use the resultArray as needed
    $jsonData =  json_encode($resultArray);
    $redisKey = "users_rate:old";
    $GLOBALS['redis']->set($redisKey, $jsonData);


    // Record the end time
    $endTime = microtime(true);
    // Calculate the execution time in seconds
    $executionTime = $endTime - $startTime;
    // Display the result
    echo "Execution time: " . number_format($executionTime, 5) . " seconds";

}

function today_data(){
    $startTime = microtime(true);
    $resultArray = []; // Initialize an array to store results

    $sql = "SELECT
                `requests_log`.`service` AS service,
                Id_user AS Id_user,
                DATE(`requests_log`.`TimeStmp`) AS date
                FROM
                `requests_log`
                WHERE
                DATE(`requests_log`.`TimeStmp`) = CURDATE()
                GROUP BY
                service, Id_user, date;";

    $selectStmt = $GLOBALS['pdo']->prepare($sql);
    $selectStmt->execute();
    $results = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
    echo count($results)."\n\n";
    foreach ($results as $row) {
        echo  json_encode($row)."\n\n";
        $startTime_2 = microtime(true);
        $selectQuery = "SELECT
                users.name AS user,
                COUNT(requests_log.Id_request) AS total,
                COUNT(CASE WHEN requests_log.smsCode IS NOT NULL THEN 1 END) AS has_sms,
                foreignapiservice.country
                FROM
                requests_log
                JOIN
                users ON requests_log.Id_user = users.Id
                JOIN
                foreignapiservice ON foreignapiservice.Id_Service_Api = requests_log.service
                WHERE
                requests_log.service = :service
                AND DATE(requests_log.TimeStmp) = :date
                AND requests_log.Id_user = :Id_user
                GROUP BY
                users.name, foreignapiservice.country;";
        $selectStmt = $GLOBALS['pdo']->prepare($selectQuery);
        $selectStmt->bindParam(':service', $row['service']);
        $selectStmt->bindParam(':Id_user', $row['Id_user']);
        $selectStmt->bindParam(':date', $row['date']);
        $selectStmt->execute();
        $data = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        $data = $data[0];
        $data['date'] = $row['date'];
        $percentage = (intval($data['has_sms']) / intval($data['total'])) * 100;
        $data['percentage'] = $percentage;
        echo $data['user']." ==> ".$data['country'] ." ==> "." $percentage % \n\n";
        // Append the data to the result array
        $resultArray[] = $data;
        $endTime = microtime(true);
        // Calculate the execution time in seconds
        $executionTime = $endTime - $startTime_2;
        // Display the result
        echo "Execution time: " . number_format($executionTime, 5) . " seconds\n\n";
    }

    // Display or use the resultArray as needed
    $jsonData =  json_encode($resultArray);
    $redisKey = "users_rate:live";
    $GLOBALS['redis']->set($redisKey, $jsonData);
    // Record the end time
    $endTime = microtime(true);
    // Calculate the execution time in seconds
    $executionTime = $endTime - $startTime;
    // Display the result
    echo "Execution time: " . number_format($executionTime, 5) . " seconds";

}
