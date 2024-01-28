<?php


$directory = '/root/log_process';

// Check if the directory doesn't exist
if (!is_dir($directory)) {
    // Create the directory with the given permissions (0777 allows full access)
    mkdir($directory, 0777, true);
    echo "Directory created: $directory";
} 

$count = 0;
while (true){

    get_loads();
    sleep(5);

}

function get_loads(){
    
    $redisKey = 'server_loads:server2';
    $newDirectory = '/root';
    chdir($newDirectory);
    // Execute the mysql_thread.sh script
    exec('/root/get_load.sh', $output);
    $threadsConnected = 0;
    $activeConnections = 0;
    // Process the output to extract relevant information
    foreach ($output as $line) {
        try{
            if (strpos($line, 'Threads_connected') !== false) {
                preg_match('/\b\d+\b/', $line, $matches);
    
                // Check if a match is found
                if (!empty($matches)) {
                    // Extracted integer value
                    $threadsConnected = (int)$matches[0];
                }
            } elseif (strpos($line, 'Active connections') !== false) {
                // Extract the Active connections value
                $activeConnections = (int)trim(explode(':', $line)[1]);
            }
        } catch (Exception $e) {
            
        }
    }
    
     echo "Threads_connected  $threadsConnected \n\n";
     echo "Active_connections  $activeConnections \n\n";
    
    require '/var/www/smsmarket/html/backend/redisconfig.php';
    $redisKey = 'server_loads:server2';
    $redis->set($redisKey . ':Threads_connected', $threadsConnected);
    $redis->set($redisKey . ':Active_connections', $activeConnections);
    $redis->close();
    if(intval($threadsConnected) > 100){
        $timestamp = time();
        $GLOBALS['count'] +=1; 
    // Format the timestamp into a human-readable date and time
        $formattedDate = date('Y-m-d_H-i-s', $timestamp);
        $output = shell_exec("bash /root/process.sh > /root/log_process/".$formattedDate.".txt ");
        if($GLOBALS['count'] > 5){
            $GLOBALS['count'] = 0; 
            echo "kill all";    
            $output = shell_exec("bash /root/kill_my.sh");
    }
    } 
}

