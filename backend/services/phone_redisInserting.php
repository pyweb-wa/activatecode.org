<?php



require '/var/www/smsmarket/html/backend/redisconfig.php';



function readJsonDataFromFile($filePath)
{
    // Read JSON data from the file
    $jsonData = file_get_contents($filePath);
    $jsonData = json_decode($jsonData, true);

    if(isset($jsonData['numbers'])){
        return $jsonData['numbers'];
    }
    return false;
}

function insertDataIntoRedis($jsonData, $redisTableName)
{
   require '/var/www/smsmarket/html/backend/redisconfig.php';

   if(!$redis)
   {
       echo "problem with redis connections\n\n";
       return false;
   }

    $redisKey = $jsonData[0]['country_code']."_".$jsonData[0]['source'];
   
    //$encodedJsonData = '{"phone_number":22222222222222222,"country_code":"LB","source":"35_1998_test"}';
    // Encode the JSON data
   
   echo $redisKey;

   // Check if the key already exists
        if ($redis->exists($redisKey)) {
            $existing = $redis->get($redisKey);
            $existing = json_decode($existing,true);
            // Merge and remove duplicates
            $mergedList = array_merge($existing, $jsonData);
            $jsonData = json_encode($mergedList);
            $redis->set($redisKey, $jsonData);
            // $redis->append($redisKey, $jsonData);
            echo "Data appended to Redis key '$redisKey'.\n";
        } else {
            // Set the initial value if the key doesn't exist
            $jsonData = json_encode($jsonData);
            $redis->set($redisKey, $jsonData);
            echo "Initial data set in Redis key '$redisKey'.\n";
        }
        if (!$redis->exists("source_list")) {
            $redis->set("source_list","[]");
        }
        
        $status = $redis->get("source_list");
        $status = json_decode($status,true);
      
        if (!in_array($redisKey, $status)) {
            // Add $redisKey to the status array
            $status[] = $redisKey;
        
            // Update the status in Redis
            $redis->set("source_list", json_encode($status));
        
            echo "Added $redisKey to the source list.\n";
        } else {
            echo "$redisKey is already in the source list.\n";
        }


        
        
   echo "Data inserted into Redis for table '$redisTableName'.\n";
   return true;
}



      
//   // $redis->del($redisTableName);
//    $redis->flushdb();
//   die();
   

function getLatestRecords($key,$numberOfItems = 1000) {
    require '/var/www/smsmarket/html/backend/redisconfig.php';

    $jsonValue = $redis->get($key);
    
    $data = json_decode($jsonValue, true);

    $itemsToDelete = array_slice($data, 0, $numberOfItems);
    // Remove the first 100 items from the data
    $data = array_slice($data, $numberOfItems);

    // Encode items back to JSON
    echo sizeof($data) .'\\n';
    $newJsonValue = json_encode($data);
    // Store new JSON value in Redis
    //$redis->set($key, $newJsonValue);

    return $itemsToDelete;
}
      
// Redis table name
$redisTableName = 'waiting_list';

// Folder path containing JSON files
$folderPath = '/var/www/smsmarket/logging/numbers';
// Backup folder path
$backupFolderPath = '/var/www/smsmarket/logging/numbers_backup';

// Create the backup folder if it doesn't exist
if (!file_exists($backupFolderPath)) {
    mkdir($backupFolderPath, 0755, true);
}



// getLatestRecords($redisTableName,$source);
// die();

// insertDataIntoRedis("", $redisTableName);

while (true) {
    $files = glob("$folderPath/*.json");

    foreach ($files as $file) {
        // Check if the file has been processed before
        $backupFile = "$backupFolderPath/" . basename($file);

        if (!file_exists($backupFile)) {
            // Read JSON data from the file
            $jsonData = readJsonDataFromFile($file);
            if($jsonData)
            {
                // Insert data into Redis
                $status = insertDataIntoRedis($jsonData, $redisTableName);

                if($status) rename($file, $backupFile);

                echo "File processed: $file\n";
            }
            else{
                echo "problem with file $file\n";
            }
           
        }
    }

    // Sleep for a while before checking again
    sleep(10); // You can adjust the sleep duration based on your needs
}

?>
