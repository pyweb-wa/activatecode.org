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
                                    cs.available < 1000
                                    AND cc.enabled = 1
                                    AND cs.country_char = 'LB'
                                    ";
        $countryStatsResult = $pdo->query($countryStatsQuery);

        while ($row = $countryStatsResult->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $country = $row['country'];
            $countryCode = $row['country_char'];
            $source = $row['source'];
            $available = $row['available'];
            $pushtime = $row['pushtime'];
            $application = "whatsapp";
            // Fetch 1000 numbers from number_waiting_list
            echo json_encode($row);
            echo "\n\n";
            $stats = InsertIntomysql($source);

        
            echo "all is good $stats\n";
        }
       
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    
    // Sleep for 5 minutes before checking again
    echo "go to sleep 300\n";
    sleep(300);
}

function InsertIntomysql($source){

    include '/var/www/smsmarket/html/backend/config.php';
    $Data  = fetchNumbersFromRedis($source, 100);
    $numbers = $Data[0];
    $newJsonValue = $Data[1];

    echo "fetching number complete with ".count($numbers)."\n\n";
    echo "fetching number complete with ".count($newJsonValue)."\n\n";
    echo "\n\n";
    die();
    if (!empty($numbers)) {
        // Insert fetched numbers into bananaapi-number
        $insertQuery = "INSERT INTO `bananaapi-number` (phone_number, country_code, source, createdTime, application) VALUES ";
        $placeholders = array_fill(0, count($numbers), "(?, ?, ?, ?, ?)");
        $insertQuery .= implode(", ", $placeholders);
        $stmt = $pdo->prepare($insertQuery);
        // Bind parameters and execute the query
        $paramIndex = 1;
        foreach ($numbers as $number) {
            $stmt->bindValue($paramIndex++, $number);
            $stmt->bindValue($paramIndex++, $countryCode);
            $stmt->bindValue($paramIndex++, $source);
            $stmt->bindValue($paramIndex++, $pushtime);
            $stmt->bindValue($paramIndex++, $application);
        }

        $stmt->execute();
        echo "insert complete successfully\n\n";
       
        echo "delete complete successfully\n\n";
        // Update country_stats available count
        $updateQuery = "UPDATE country_stats SET available = available + :count WHERE id = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindValue(':count', count($numbers), PDO::PARAM_INT);
        $updateStmt->bindParam(':id', $id,  PDO::PARAM_INT);
        $updateStmt->execute();
        echo "update sucessfully!\n\n";
    }


}


function fetchNumbersFromRedis($source,$numberOfItems = 1000) {
    require '/var/www/smsmarket/html/backend/redisconfig.php';

    $jsonValue = $redis->get($source);
    
    $data = json_decode($jsonValue, true);

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
