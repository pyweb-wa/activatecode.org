<?php

// Function to execute the SQL query and retrieve phone numbers
function fetchDataAndSend() {
  
    echo "check MySQL \n";
    try {
        // Create a PDO connection
        include '../../config.php';
        //include 'api.php';
        // SQL query
        $sql = "SELECT phone_number FROM `bananaapi-number`
                WHERE `taked_time` BETWEEN NOW() - INTERVAL 6 MINUTE AND NOW() - INTERVAL 5 MINUTE
                ORDER BY `country_code` ASC";

        // Prepare and execute the query
        $stmt = $pdo->query($sql);

        // Fetch all phone numbers
        $phoneNumbers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if(!$phoneNumbers){
            return;
        }

        // Close the database connection
       // $pdo = null;

        // Send phone numbers to another function
        sendPhoneNumbers($phoneNumbers);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function featchFromRedis(){
    require '/var/www/smsmarket/html/backend/redisconfig.php'; 
    echo "check Redis \n";
    $keys = $redis->keys('todelete:*');
    //var_dump($keys);
    if($keys){
    foreach($keys as $key){  

        #TODO 
        # to delete by source and server name

        // $parts = explode(':', $key);
        // $source = $parts[1]; 
        // $parts = explode('_', $source);

        // Check if there are enough parts
        // if (count($parts) >= 3) {
        //     $server_name = $parts[1];
        //     $list_id = $parts[2];
        
        // }
        $currentTime = time();
        $xTimeAgo = $currentTime - (6 * 60); // X minutes ago
        $phoneNumbers = $redis->zrangebyscore( $key, '-inf', $xTimeAgo);
        if(!$phoneNumbers){
            echo "no data in $key\n";
            return;
        }
       // echo "from redis\n\n";
        sendPhoneNumbers($phoneNumbers);
        foreach ($phoneNumbers as $number) {
          //  echo "delete $number\n";
            $redis->zRem($key, $number);
        }


        }
    }
}
// Function to process the retrieved phone numbers
function sendPhoneNumbers($phoneNumbers) {
    include 'api.php';
        $jsonString  = json_encode(["number_array" => $phoneNumbers]);
            // Decode the JSON string
        
            $data = json_decode($jsonString, true);

        // Convert numbers to strings
        if (isset($data['number_array'])) {
            $data['number_array'] = array_map('strval', $data['number_array']);
        }

        // Encode the array back to a JSON string
        $newJsonString = json_encode($data);

        // Display the modified JSON string

    $accounts = get_accounts();
    echo "deleting ".sizeof($phoneNumbers). "\n";
    foreach ($accounts as $server) {
        echo "response of ".$server['url'].": ";
        deletenumberArrayfromServer($server,$newJsonString);  
        echo "\n";     
        }

}

// Call the function to execute the query and retrieve phone numbers
while(true){
    featchFromRedis();
    sleep(2);
   fetchDataAndSend();
sleep(20);
}
