<?php




while (true) {

    try {
        include '/var/www/smsmarket/html/backend/config.php';
        // Fetching country_stats with available < 100
        $countryStatsQuery = "SELECT
                                    cs.id,
                                    cs.country,
                                    cs.country_char,
                                    cs.source,
                                    cs.available,
                                    cc.enabled,
                                    cs.pushtime
                                FROM
                                    country_stats cs
                                JOIN
                                    countries_control cc ON cs.source = cc.source
                                WHERE
                                    cs.available < 300
                                    AND cc.enabled = 1
                                    -- AND cs.source = 'MA_35_3194'
                                    ";
        $countryStatsResult = $pdo->query($countryStatsQuery);

        while ($row = $countryStatsResult->fetch(PDO::FETCH_ASSOC)) {
           
            try{

         
            echo json_encode($row);
            echo "\n\n";
            
            $stats = updateNumbers_To_Redis($row['source']);
           # $stats = InsertIntomysql($row);

        
            echo "all is good $stats\n";
            }
            catch (Exception $e) {
                echo "". $e->getMessage() ."";
            }
        }
       
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    
    // Sleep for 5 minutes before checking again
    echo "go to sleep 20 second\n";
    sleep(20);
}

function  InsertIntomysql($row) {

    $id = $row['id'];
    $country = $row['country'];
    $countryCode = $row['country_char'];
    $source = $row['source'];
    $available = $row['available'];
   // $pushtime = $row['pushtime'];
    $application = "whatsapp";
    include '/var/www/smsmarket/html/backend/config.php';
    require '/var/www/smsmarket/html/backend/redisconfig.php';
    date_default_timezone_set('Asia/Beirut');

    // Create a DateTime object with the current time
    $datetime = new DateTime();

    // Format the DateTime object as a MySQL timestamp
    $pushtime = $datetime->format('Y-m-d H:i:s');

    $Data = fetchNumbersFromRedis($source, 10000);
    if (!$Data){
        echo "can't find data from source $source ";
        return;
    }
    $numbers = $Data[0][0];
    $newJsonValue = $Data[1];
    $newJsonValue = json_decode($newJsonValue[0], true);
    
    echo "Fetching number complete with " . count($numbers) . "\n\n";
    echo "Fetching number complete with " . count($newJsonValue) . "\n\n";
    echo "\n\n";
    
    if (!empty($numbers)) {
        // Insert fetched numbers into bananaapi-number in batches of 20
        $batchSize = 350;
        $numberChunks = array_chunk($numbers, $batchSize);
    
        foreach ($numberChunks as $chunk) {
            $retryCount = 0;
            $error = 0;
            // Retry loop in case of an error
            while ($retryCount <= 3) {
                try {
                    if (count($chunk) < $batchSize) {
                        $batchSize = count($chunk);
                    }
                    // Insert numbers into bananaapi-number
                    $insertQuery = "INSERT INTO `bananaapi-number` (phone_number, country_code, source, createdTime, application) VALUES ";
                    $placeholders = array_fill(0, $batchSize, "(?, ?, ?, ?, ?)");
                    $insertQuery .= implode(", ", $placeholders);
                    $stmt = $pdo->prepare($insertQuery);
    
                    // Bind parameters and execute the query
                    $paramIndex = 1;
                    foreach ($chunk as $number) {
                        $stmt->bindValue($paramIndex++, $number['phone_number']);
                        $stmt->bindValue($paramIndex++, $countryCode);
                        $stmt->bindValue($paramIndex++, $source);
                        $stmt->bindValue($paramIndex++, $pushtime);
                        $stmt->bindValue($paramIndex++, $application);
                    }
    
                    $stmt->execute();
                    echo "Inserted " . count($chunk) . " records into `bananaapi-number`.\n";
                    $error = 0;
                    break; // Exit retry loop if successful
                } catch (PDOException $e) {
                    // Handle the error, log it, or rethrow the exception
                    echo "Error: " . $e->getMessage() . "\n";
                    $error =1;
                    // Optionally, you can add specific error code checks
                    // to determine if a retry is needed for a particular error
    
                    // Increment the retry count
                    $retryCount++;
    
                    if ($retryCount <= 3) {
                        echo "Retrying (Attempt $retryCount of 3)...\n";
                        usleep(500000); // Sleep for 0.5 seconds before retrying
                    } else {
                        echo "Max retries reached. Exiting.\n";
                        break; // Exit the retry loop if max retries reached
                    }
                }
            }
        }
    
        echo "Insert complete successfully\n\n";
        
        if(!$error){
        echo "Delete complete successfully\n\n";
        $redis->set($source, json_encode($newJsonValue));
    
        // Update country_stats available count
        $updateQuery = "UPDATE countries_control SET created_time = :_time WHERE source = :source";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':_time', $pushtime);
        $updateStmt->bindParam(':source', $source);
        $updateStmt->execute();
        echo "Update successfully!\n\n";
        }
    }





}


function fetchNumbersFromRedis($source,$numberOfItems = 1000) {
    require '/var/www/smsmarket/html/backend/redisconfig.php';
    echo $source;

    $jsonValue = $redis->get($source);
    
    $data = json_decode($jsonValue, true);
    if($data == null) {
        return array ();
    }
    $itemsToDelete = array_slice($data, 0, $numberOfItems);
    // Remove the first 100 items from the data
    $data = array_slice($data, $numberOfItems);

    // Encode items back to JSON
    echo sizeof($data) .'\\n';
    $newJsonValue = json_encode($data);
    // Store new JSON value in Redis
    //$redis->set($source, $newJsonValue);

    return array([$itemsToDelete],[$newJsonValue]);
}


function fetchNumbers($pdo, $source, $country_code)
{
    $stmt = $pdo->prepare("SELECT phone_number FROM number_waiting_list WHERE source = :source AND country_code = :country_code LIMIT 20000");
    $stmt->bindParam(':source', $source);
    $stmt->bindParam(':country_code', $country_code);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}



function updateNumbers_To_Redis($source){
    require '/var/www/smsmarket/html/backend/redisconfig.php';
    include '/var/www/smsmarket/html/backend/config.php';

    $Data = fetchNumbersFromRedis($source, 10000);
    if (!$Data){
        echo "can't find data from source $source ";
        return;
    }
    $phoneNumbers = $Data[0][0];
    $newJsonValue = $Data[1];
    $newJsonValue = json_decode($newJsonValue[0], true);
    $redisKey = 'live_'.$source;
    foreach ($phoneNumbers as $number) {
    $redis->sadd($redisKey, $number['phone_number']);
    }

    echo "Delete complete successfully\n\n";
    $redis->set($source, json_encode($newJsonValue));
    $datetime = new DateTime();
    $pushtime = $datetime->format('Y-m-d H:i:s');

    // Update country_stats available count
    $updateQuery = "UPDATE countries_control SET created_time = :_time WHERE source = :source";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':_time', $pushtime);
    $updateStmt->bindParam(':source', $source);
    $updateStmt->execute();
    $updateQuery = "UPDATE country_stats SET available = 10000 WHERE source = :source";
    $updateStmt = $pdo->prepare($updateQuery);
    // $updateStmt->bindParam(':_time', $pushtime);
    $updateStmt->bindParam(':source', $source);
    $updateStmt->execute();
    echo "Update successfully!\n\n";

}