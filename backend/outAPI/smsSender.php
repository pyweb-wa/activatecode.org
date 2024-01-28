<?php

function isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}





function sender($url, $object, $user_id, $sms_id)
{
    
    $failureThreshold = 3;
    for ($attempt = 1; $attempt <= $failureThreshold + 1; $attempt++) {
        // Create a curl handle
        $ch = curl_init();
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
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch)."\n";
            curl_close($ch);
            write_log("[$sms_id] _1 " . curl_error($ch), "$user_id.log");
            sleep(1);
            continue;
            // Handle the error as needed
        } else {
            // Get HTTP status code
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // Close cURL session
            curl_close($ch);
            // Check HTTP status code for success or failure
            if ($httpCode >= 200 && $httpCode < 300) {
                //echo 'Request was successful. HTTP status code: ' . $httpCode."\n";
                // Process the response data, e.g., JSON decoding
                if (isJson($response)) {
                    $responseData = json_decode($response, true);
                    if (isset($responseData["status"])) {
                        if (strtolower($responseData["status"]) == "ok") {
                            echo " successfully send [$sms_id] with response $response for user $user_id\n";
                            write_log("[$sms_id] $response", "$user_id.log");
                            return true;
                        }
                    }
                }
            }
        }
        write_log("[$sms_id]  statusCode :$httpCode  response: $response", "$user_id.log");
    }
    write_log("[$sms_id]  statusCode :$httpCode  response: $response", "$user_id.log");
    disableUserAccount($user_id);
    write_log("[$sms_id] Account disabled due to consecutive failures", "$user_id.log");
    return false;
    
}

function disableUserAccount($user_id){
    include '../config.php';
    $sql = "update `tokens` set `tokens`.`Valid` = 0 Where `tokens`.`userID` =?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    $sql = "update `users` set `callback_status` = 0 Where Id =?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    echo "-----------------------------------------------------------------";

}

function Sending_ToUser_Server($value)
{
    include '../config.php';

    //CHECK_NUMBER if in database and taked by ruAPI;
    //$sql = 'SELECT * FROM `bananaapi-number` WHERE `phone_number` = ? and taked != 0 ';
    if ($value['application']) {
        //if($value['application'] != "Unknown"){

        $sql = "SELECT
                b.id,
                r.Phone_Nb,
                b.sms,
                b.code,
                r.Id_request,
                b.application,
                r.Id_user,
                u.callback_url,
                u.callback_status,
                n.id as bn_id
            FROM
                requests_log r
            JOIN
                `bananaapi-results` b ON r.Phone_Nb = b.phone_number
            JOIN
                `bananaapi-number` n ON b.application = n.application AND n.phone_number = r.Phone_Nb
            JOIN
                users u ON r.Id_user = u.Id
            WHERE
                r.Phone_Nb = ?
                AND b.taked = 0
                AND b.timestamp >= r.TimeStmp
            ORDER BY
                b.timestamp DESC
            LIMIT 1;
    ";


        $stmt = $pdo->prepare($sql);
        $phone_number = str_replace(array('+', ' '), '', $value['phone_number']);


        $stmt->execute([$phone_number]);
        $row = $stmt->fetch();

        if ($row) {
            if (isset($row['id'])) {
                if ($row['callback_status'] == 0) {
                    return;
                } else if (strlen($row['callback_url']) < 5) {
                    return;
                }
            }

            $url = urldecode($row['callback_url']);
            // Create a new stdClass object
            $dataObject = new stdClass();

            // Set properties based on the values in the array
            $dataObject->id = $row['Id_request'];
            $dataObject->phone_number = $row['Phone_Nb'];
            $dataObject->sms = $row['sms'];
            $dataObject->application = $row['application'];
            $dataObject->code = $row['code'];


            $dataObject =  json_encode($dataObject);


         
          
            $res = sender($url, $dataObject, $row['Id_user'], $row['Id_request']);
            if ($res) {
                //Update taked 
                $stmt = $pdo->prepare('
                    UPDATE `bananaapi-results` 
                    SET `taked` = 2, `taked_time` = NOW() 
                    WHERE `id` = ?;
                    
                    UPDATE `bananaapi-number` 
                    SET `is_finished` = 1, `is_finished_time` = NOW() 
                    WHERE `id` = ?;
                    
                    UPDATE `requests_log` 
                    SET `Status` = ?, `SMSCode` = ?, `sms_content` = ? 
                    WHERE `Id_request` = ?;
                ');

                $stmt->execute([
                    $value['id'],  // id for bananaapi-results update
                    $row['bn_id'],  // id for bananaapi-number update
                    "Finished", $row['code'], $row['sms'], $row['Id_request']
                ]);




                // $stmt2 = $pdo->prepare('UPDATE `bananaapi-results` SET `taked`=1, `taked_time` = NOW() WHERE `id` = ? ');
                // $stmt2->execute([$value['id']]);

                // $stmt3 = $pdo->prepare('UPDATE `bananaapi-number` SET `is_finished` = 1, `is_finished_time` =NOW() WHERE `id` = ? ');
                // $stmt3->execute([$row['Id_request']]);
                // $stmt = $pdo->prepare('UPDATE requests_log set `Status`=? ,`SMSCode`=?  ,`sms_content`=? where Id_request=?  ');
                // $stmt->execute(["Finished", $row['code'], $row['sms'], $row['Id_request']]);
            }
        }
    }
}

function write_log($message, $filename)
{
    $log = "[-] " . (string) $message . "  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
    $filepath = "/var/www/smsmarket/logging/smssender/$filename";
    file_put_contents($filepath, $log, FILE_APPEND);
}


function FindSMS_AndSendIt2()
{
    include '../config.php';
    try {
        $threadLimit = 10;
        $activeThreads = 0;

        $stmt1 = $pdo->prepare("SELECT * FROM `bananaapi-results` where `timestamp` <= NOW() - INTERVAL 2 SECOND and `timestamp` > NOW() - INTERVAL 600 SECOND and `taked` = 0 ");
        $stmt1->execute([]);
        $results = $stmt1->fetchAll();

        if (sizeof($results) >= 1) {
            echo "Found " . sizeof($results) . " lists. Trying to send...\n";

            $startTime = microtime(true);

            foreach ($results as $res) {
                if ($activeThreads >= $threadLimit) {
                    echo "Thread limit reached. Waiting...\n";
                    sleep(2);
                    continue;
                }

                $pid = pcntl_fork();

                if ($pid == -1) {
                    die('Could not fork process');
                } elseif ($pid) {
                    // This is the parent process
                    // You can add additional logic for the parent if needed
                   
                } else {
                    // This is the child process
                    $activeThreads++;
                    Sending_ToUser_Server($res);
                    $activeThreads--;
                    exit(); // Important: Each child process should exit to avoid executing the same code multiple times
                }
            }

            // Wait for child processes to finish
            while ($activeThreads > 0) {
                pcntl_wait($status);
                $activeThreads--;
            }

            // Record the end time
            $endTime = microtime(true);
            // Calculate the execution time
            $executionTime = $endTime - $startTime;
            // Print the result
            echo "Script execution time: " . number_format($executionTime, 4) . " seconds\n\n";
        }
    } catch (Exception $e) {
        echo $e->getMessage()."\n\n";
        write_log($e->getMessage(), "smssender_err.log");
    }
}





function FindSMS_AndSendIt()
{
    include '../config.php';
    try {

        $stmt1 = $pdo->prepare("SELECT * FROM `bananaapi-results` where `timestamp` <= NOW() - INTERVAL 2 SECOND and `timestamp` > NOW() - INTERVAL 600 SECOND and `taked` = 0 ");
        $stmt1->execute([]);
        $results = $stmt1->fetchAll();
        if (sizeof($results) >= 1) {
            echo "find  lists try to send " . sizeof($results) . "\n";

            $startTime = microtime(true);
            foreach ($results as $res) {
                $pid = pcntl_fork();

                if ($pid == -1) {
                    die('Could not fork process');
                } elseif ($pid) {
                    // This is the parent process
                    // You can add additional logic for the parent if needed
                } else {
                    // This is the child process
                    Sending_ToUser_Server($res);
                    exit(); // Important: Each child process should exit to avoid executing the same code multiple times
                }
            }
            // Record the end time
            $endTime = microtime(true);
            // Calculate the execution time
            $executionTime = $endTime - $startTime;
            // Print the result
            echo "Script execution time: " . number_format($executionTime, 4) . " seconds\n\n";
        }
    } catch (Exception $e) {
        echo $e->getMessage()."\n";
        write_log($e->getMessage(), "smssender_err.log");
    }
}

function checkIfEnabled()
{
    include '../config.php';
    try {

        $stmt1 = $pdo->prepare("SELECT * FROM `users` WHERE callback_status = 1 and callback_url is not null ");
        $stmt1->execute([]);
        $results = $stmt1->fetchAll();
        if (sizeof($results) >= 1) {
            echo "find  some users has enabled callback mode " . sizeof($results) . "\n";
            return true;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        write_log($e->getMessage(), "smssender_err.log");
    }
    return false;
}


$folderPath = "/var/www/smsmarket/logging/smssender/";
if (!is_dir($folderPath)) {
    // Create the folder
    if (mkdir($folderPath, 0777, true)) {
        echo "Folder created successfully: $folderPath\n";
    }
}


// FindSMS_AndSendIt();
// die();



while (true) {
    try {
        if (!checkIfEnabled()) {
            echo "no users with callback mode found\n";
            sleep(10);
            continue;
        }
        echo "check for FindSMS_AndSendIt " . time() . PHP_EOL;
        FindSMS_AndSendIt();
        sleep(1);
    } catch (Exception $e) {
        echo ($e->getMessage());
        write_log($e->getMessage(), "smssender_err.log");
    }
}
