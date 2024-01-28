<?php

$filepath = "/var/www/smsmarket/logging/delete_data.log";
$log = "[+] Starting at datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
file_put_contents($filepath, $log, FILE_APPEND);
MoveData_requests_log_history();
//delete_data("requests_log");
$log = "[+] Finishing at datetime: " . date('m/d/Y h:i:s a', time()) . "\n\n";
file_put_contents($filepath, $log, FILE_APPEND);

function MoveData_requests_log_history(){
        try {
            $err = 0;
            include '/var/www/smsmarket/html/backend/config.php';

            // Select the last ID from requests_log_history
            $getLastIdQuery = "SELECT MAX(Id_request) AS lastId FROM requests_log_history";
            $lastIdResult = $pdo->query($getLastIdQuery);
            $lastId = $lastIdResult->fetch(PDO::FETCH_ASSOC)['lastId'];

            echo $lastId."\n";
            // Select records from requests_log
            // SELECT count(*) FROM requests_log WHERE Id_request > (SELECT max(Id_request) from requests_log_history) AND DATE(TimeStmp) < NOW() - INTERVAL 3 DAY;

            $selectQuery = "SELECT * FROM requests_log WHERE Id_request > (SELECT MAX(Id_request) AS lastId FROM requests_log_history)  AND DATE(TimeStmp) < NOW() - INTERVAL 2 DAY";
            $selectStmt = $pdo->prepare($selectQuery);
           // $selectStmt->bindParam(':lastId', $lastId, PDO::PARAM_INT);
            $selectStmt->execute();
            $results = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
            echo "getting ".sizeof($results)." from requests_log table\n";
            // Move results to requests_log_history
            // Move results to requests_log_history using batch insert
            if (!empty($results)) {
                $insertQuery = "INSERT INTO requests_log_history (Id_request, Id_user, Id_Token, Status, Phone_Nb, SMSCode, sms_content, TimeStmp, CC, service, srv_req_id) VALUES (:Id_request, :Id_user, :Id_Token, :Status, :Phone_Nb, :SMSCode, :sms_content, :TimeStmp, :CC, :service, :srv_req_id)";
                
                // Start a transaction
                $pdo->beginTransaction();

                $insertStmt = $pdo->prepare($insertQuery);

                $batchSize = 10000;
                $rowCount = count($results);
                $count = 0;
                for ($i = 0; $i < $rowCount; $i += $batchSize) {
                    $batch = array_slice($results, $i, $batchSize);
                // var_dump($batch[0]);
                    // Print batch information
                    $startId = $batch[0]['Id_request'];
                    $endId = end($batch)['Id_request'];
                    echo "Processing batch from ID $startId to $endId\n";

                    echo "i: $i\n";
                    //  if ($count ==1){
                    //     die();
                    //  }
                    // continue;
                    foreach ($batch as $row) {
                        // echo $row['Id_request'];
                        // continue;
                        // Adjust column names accordingly
                        $insertStmt->bindParam(':Id_request', $row['Id_request'], PDO::PARAM_INT);
                        $insertStmt->bindParam(':Id_user', $row['Id_user'], PDO::PARAM_INT);
                        $insertStmt->bindParam(':Id_Token', $row['Id_Token'], PDO::PARAM_INT);
                        $insertStmt->bindParam(':Status', $row['Status'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':Phone_Nb', $row['Phone_Nb'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':SMSCode', $row['SMSCode'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':sms_content', $row['sms_content'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':TimeStmp', $row['TimeStmp'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':CC', $row['CC'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':service', $row['service'], PDO::PARAM_STR);
                        $insertStmt->bindParam(':srv_req_id', $row['srv_req_id'], PDO::PARAM_INT);

                        $insertStmt->execute();
                    }
                    // Update $lastId to the last processed ID in the batch
                    $lastId = $endId;
                    // Commit the transaction after each batch
                    $pdo->commit();

                    // Start a new transaction for the next batch
                    $pdo->beginTransaction();
                    $count =$count +1;
                }


                // Commit the final transaction
                $pdo->commit();
            }


            // if (!empty($results)) {
            //     $insertQuery = "INSERT INTO requests_log_history (`Id_request`, `Id_user`, `Id_Token`, `Status`, `Phone_Nb`, `SMSCode`, `sms_content`, `TimeStmp`, `CC`, `service`, `srv_req_id`) VALUES (:Id_request, :Id_user, :Id_Token, :Status, :Phone_Nb, :SMSCode, :sms_content, :TimeStmp, :CC, :service, :srv_req_id)";
            //     $insertStmt = $pdo->prepare($insertQuery);
            //     echo "start insert into history tables, need some times\n\n";
            //     foreach ($results as $row) {
            //          // Adjust column names accordingly
            //          $insertStmt->bindParam(':Id_request', $row['Id_request'], PDO::PARAM_INT);
            //          $insertStmt->bindParam(':Id_user', $row['Id_user'], PDO::PARAM_INT);
            //          $insertStmt->bindParam(':Id_Token', $row['Id_Token'], PDO::PARAM_INT);
            //          $insertStmt->bindParam(':Status', $row['Status'], PDO::PARAM_STR);
            //          $insertStmt->bindParam(':Phone_Nb', $row['Phone_Nb'], PDO::PARAM_STR);
            //          $insertStmt->bindParam(':SMSCode', $row['SMSCode'], PDO::PARAM_STR);
            //          $insertStmt->bindParam(':sms_content', $row['sms_content'], PDO::PARAM_STR);
            //          $insertStmt->bindParam(':TimeStmp', $row['TimeStmp'], PDO::PARAM_STR);
            //          $insertStmt->bindParam(':CC', $row['CC'], PDO::PARAM_STR);
            //          $insertStmt->bindParam(':service', $row['service'], PDO::PARAM_STR);
            //          $insertStmt->bindParam(':srv_req_id', $row['srv_req_id'], PDO::PARAM_INT);
        
            //         // Bind other values similarly

            //         $insertStmt->execute();
            //     }
            // }

        } catch (PDOException $e) {
            $err = 1;
            $filepath = "/var/www/smsmarket/logging/delete_data.log";
            $log = "[+] error  datetime: " . date('m/d/Y h:i:s a', time()) . " ".$e->getMessage()."\n";
            file_put_contents($filepath, $log, FILE_APPEND);
            echo "Error: " . $e->getMessage();
        }
    if($err ==0){
        delete_data("requests_log");
    }

}

function MoveData_redis_history(){
    try {
        $err = 0;
        include '/var/www/smsmarket/html/backend/config.php';

        // Select the last ID from requests_log_history
        $getLastIdQuery = "SELECT MAX(id) AS lastId FROM requests_log_history";
        $lastIdResult = $pdo->query($getLastIdQuery);
        $lastId = $lastIdResult->fetch(PDO::FETCH_ASSOC)['lastId'];

        echo $lastId."\n";
        // Select records from requests_log
        // SELECT count(*) FROM requests_log WHERE Id_request > (SELECT max(Id_request) from requests_log_history) AND DATE(TimeStmp) < NOW() - INTERVAL 3 DAY;

        $selectQuery = "SELECT * FROM requests_log WHERE Id_request > (SELECT MAX(Id_request) AS lastId FROM requests_log_history)  AND DATE(TimeStmp) < NOW() - INTERVAL 2 DAY";
        $selectStmt = $pdo->prepare($selectQuery);
       // $selectStmt->bindParam(':lastId', $lastId, PDO::PARAM_INT);
        $selectStmt->execute();
        $results = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "getting ".sizeof($results)." from requests_log table\n";
        // Move results to requests_log_history
        // Move results to requests_log_history using batch insert
        if (!empty($results)) {
            $insertQuery = "INSERT INTO requests_log_history (Id_request, Id_user, Id_Token, Status, Phone_Nb, SMSCode, sms_content, TimeStmp, CC, service, srv_req_id) VALUES (:Id_request, :Id_user, :Id_Token, :Status, :Phone_Nb, :SMSCode, :sms_content, :TimeStmp, :CC, :service, :srv_req_id)";
            
            // Start a transaction
            $pdo->beginTransaction();

            $insertStmt = $pdo->prepare($insertQuery);

            $batchSize = 10000;
            $rowCount = count($results);
            $count = 0;
            for ($i = 0; $i < $rowCount; $i += $batchSize) {
                $batch = array_slice($results, $i, $batchSize);
            // var_dump($batch[0]);
                // Print batch information
                $startId = $batch[0]['Id_request'];
                $endId = end($batch)['Id_request'];
                echo "Processing batch from ID $startId to $endId\n";

                echo "i: $i\n";
                //  if ($count ==1){
                //     die();
                //  }
                // continue;
                foreach ($batch as $row) {
                    // echo $row['Id_request'];
                    // continue;
                    // Adjust column names accordingly
                    $insertStmt->bindParam(':Id_request', $row['Id_request'], PDO::PARAM_INT);
                    $insertStmt->bindParam(':Id_user', $row['Id_user'], PDO::PARAM_INT);
                    $insertStmt->bindParam(':Id_Token', $row['Id_Token'], PDO::PARAM_INT);
                    $insertStmt->bindParam(':Status', $row['Status'], PDO::PARAM_STR);
                    $insertStmt->bindParam(':Phone_Nb', $row['Phone_Nb'], PDO::PARAM_STR);
                    $insertStmt->bindParam(':SMSCode', $row['SMSCode'], PDO::PARAM_STR);
                    $insertStmt->bindParam(':sms_content', $row['sms_content'], PDO::PARAM_STR);
                    $insertStmt->bindParam(':TimeStmp', $row['TimeStmp'], PDO::PARAM_STR);
                    $insertStmt->bindParam(':CC', $row['CC'], PDO::PARAM_STR);
                    $insertStmt->bindParam(':service', $row['service'], PDO::PARAM_STR);
                    $insertStmt->bindParam(':srv_req_id', $row['srv_req_id'], PDO::PARAM_INT);

                    $insertStmt->execute();
                }
                // Update $lastId to the last processed ID in the batch
                $lastId = $endId;
                // Commit the transaction after each batch
                $pdo->commit();

                // Start a new transaction for the next batch
                $pdo->beginTransaction();
                $count =$count +1;
            }


            // Commit the final transaction
            $pdo->commit();
        }


    

    } catch (PDOException $e) {
        $err = 1;
        $filepath = "/var/www/smsmarket/logging/delete_data.log";
        $log = "[+] error  datetime: " . date('m/d/Y h:i:s a', time()) . " ".$e->getMessage()."\n";
        file_put_contents($filepath, $log, FILE_APPEND);
        echo "Error: " . $e->getMessage();
    }
if($err ==0){
    delete_data("requests_log");
}

}

function delete_data ($tablename){
    try {
        include '/var/www/smsmarket/html/backend/config_1.php';



        //Fetch all IDs from the temporary table
        if ($tablename == "requests_log"){
            $selectQuery = "SELECT Id_request FROM requests_log WHERE Id_request < (SELECT MAX(Id_request) AS lastId FROM requests_log_history)";
        }
        elseif($tablename == "bananaapi-number"){
            $selectQuery = "SELECT id FROM `bananaapi-number` WHERE DATE(createdTime) < NOW() - INTERVAL 2 DAY  ORDER BY  id";
        }
        else{
            return;
        }
       // $selectQuery = "select transactionID from transaction where transaction.transDate <= '2024-01-10'";
        // $selectQuery = "select  Id_request FROM requests_log WHERE Status ='Expired' AND DATE(TimeStmp) < NOW() - INTERVAL 3 DAY";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->execute();
        $allIds = $selectStmt->fetchAll(PDO::FETCH_COLUMN);

        // Sort the IDs in ascending order
        sort($allIds);

        // Delete records in batches of 100 with retry on deadlock
        $batchSize = 300;
        $offset = 0;
        $maxRetries = 5; // Maximum number of retries
        $all = count($allIds);
        echo "we found $all records\n\n";
        while ($offset < count($allIds)) {
            // Get the current batch of IDs
            $idsInBatch = array_slice($allIds, $offset, $batchSize);

            if (!empty($idsInBatch)) {
                $retryCount = 0;

                // Retry loop in case of deadlock
                while ($retryCount <= $maxRetries) {
                    try {
                        // Delete records from requests_log in the current batch
                        $placeholders = implode(',', array_fill(0, count($idsInBatch), '?'));
                        
                        if ($tablename == "requests_log"){
                            $deleteQuery = "DELETE FROM requests_log WHERE Id_request IN ($placeholders)";
                        }
                        elseif($tablename == "bananaapi-number"){
                            $deleteQuery = "DELETE FROM `bananaapi-number` WHERE id IN ($placeholders)";
                        }
                        elseif($tablename == "transaction"){
                            $deleteQuery = "DELETE FROM `transaction` WHERE transactionID IN ($placeholders)";
                        }
                        $deleteStmt = $pdo->prepare($deleteQuery);
                        $deleteStmt->execute($idsInBatch);

                        $all = $all - $batchSize;
                        echo "Deleted " . count($idsInBatch) . " records ".$all."\n";

                        break; // Exit retry loop if successful
                    } catch (PDOException $e) {
                        // echo "........". $e->getMessage()."\n";
                        // echo "....+++....". $e->getCode()."\n";

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

        // Drop the temporary table
        // $dropQuery = "DROP TEMPORARY TABLE IF EXISTS tmp_table";
        // $pdo->exec($dropQuery);

        echo "Operation completed successfully.\n";
        } catch (PDOException $e) {
            $filepath = "/var/www/smsmarket/logging/delete_data.log";
            $log = "[+] error  datetime: " . date('m/d/Y h:i:s a', time()) . " ".$e->getMessage()."\n";
            file_put_contents($filepath, $log, FILE_APPEND);

        echo "Error: " . $e->getMessage();
    }
}


    
    