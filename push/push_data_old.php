<?php

$logPath = "/usr/local/lsws/customers/logging/";
function Parser()
{
    $response = (object)[
        'ResponseCode' => 0,
        'Msg' => 'error',
        'Result' => null,
    ];

    $date = date('d-m-Y', time());
    $t = time();
    $time_string = $date . '-' . $t;
    
    $content = file_get_contents('php://input');
    file_put_contents($GLOBALS['logPath'].'/_data_' . $time_string . '.log', file_get_contents('php://input'));
    try {
        # for encoding > 
        $content =  preg_replace('/[[:cntrl:]]/', '', $content);
        if (isJson($content) == 1) {

            $req_dump = json_decode($content, JSON_UNESCAPED_SLASHES);
            $results = (array) [];
            $type = "";

            if (isset($req_dump["results"])) {

                if (!empty($req_dump['results'])) {
                    foreach ($req_dump['results'] as  $value) {
                        if (isset($value["sms"]) && isset($value["phone_number"]) && isset($value["sender"])) {
                            //echo 123123;
                            //die();
                            //resend($content);
                            //die();
                            $code = "";
                            if (strpos(strtolower($value["sms"]), "whatsapp") !== false || strpos(strtolower($value["sms"]), "can also tap on this link") !== false || strpos(strtolower($value["sender"]), "whatsapp") !== false) {

                            //if (strpos(strtolower($value["sms"]), "whatsapp") !== false) {
                                $pattern = '/\d{3}-\d{3}/';
                                preg_match($pattern,$value["sms"], $matches);
                                if(sizeof($matches) >=1){
                                $number = $matches[0];
                                $code = str_replace("-", "", $number);
                                
                                }



                            } else if(strpos(strtolower($value["sms"]), "google") !== false) {
                                preg_match_all('/\d+/',$value["sms"], $matches);
                                if(sizeof($matches) >=1){
                                $code = implode('', $matches[0]);   
                                $code = str_replace("-", "",  $code);
                                }
                              
                            }
                            else{
                                $new = str_replace(":>4:", "", $value["sms"]);
                                $all_digits = preg_replace('/\D/', '', $new);
                                $size = strlen($all_digits);
    
                                 if ($size >= 6) {
                                    $code = substr($all_digits, 0, 6);
                                } else {
                                   $code= "";
                                }


                            }

                            
                            $value['code'] = $code;
                            array_push($results, $value);
                            $type = "sms";
                        }
                    }
                }
            } else if (isset($req_dump["numbers"])) {

                if (!empty($req_dump['numbers'])) {
                    if (isset($req_dump['application'])) {


                        $results["application"] = $req_dump["application"];
                    } else {
                        $response->Msg = 'some keys are missing';
                        $response = json_encode($response);
                        echo $response;
                        die();
                    }

                    $results["numbers"] = (array) [];
                    foreach ($req_dump['numbers'] as  $value) {
                        if (isset($value["country_code"]) && isset($value["phone_number"]) && isset($value["source"])) {
                            $value["phone_number"] = str_replace("+", "", $value["phone_number"]);
                            if ($value["phone_number"]) {
                                    if (preg_match('/^[0-9]+$/', $value["phone_number"])) {
                            array_push($results["numbers"], $value);
                                    }
                            }
                        }
                    }
                } else {
                    // $count = delete_all();
                    // $response->Msg = 'OK';
                    // $response->Result = $count;
                    #delete all numbers

                }
            }

            if (!empty($results)) {
                $response->ResponseCode = 1;
                $response->Msg = 'OK';
                $response->Result = sizeof($results);
                saveResult($results, $type);
                $response = json_encode($response);
                echo $response;
                //echo json_encode($results);

                die();
            }
        }
    } catch (Exception $e) {
        file_put_contents($GLOBALS['logPath']."push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
       // echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    $response = json_encode($response);
    echo $response;
    

    die();
}

function isJson($string)
{

    json_decode($string, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);

    return json_last_error() === JSON_ERROR_NONE;
}

function delete_all()
{
    require_once '../backend/config.php';

    $sql = "DELETE FROM `bananaapi-number` WHERE `taked` = 0;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([]);
    $count = $stmt->rowCount();
    return $count;
    //print("Deleted $count rows.\n");
}
function saveResult($results, $type)
{
   
    require_once '../backend/config.php';

    try {

        $response = (object)[
            'ResponseCode' => 0,
            'Msg' => 'error',
            'Result' => null,
        ];
        // 
        if ($type == "sms") {
            $stmt = $pdo->prepare("INSERT INTO `bananaapi-results`(`phone_number`,  `sms`, `Sender_n`,`code`) VALUES (:phone_number, :sms, :Sender_n, :code)");
            $stmt->bindParam(':phone_number', $phone_number);
            $stmt->bindParam(':sms', $sms);
            $stmt->bindParam(':Sender_n', $Sender_n);
            $stmt->bindParam(':code', $code);

            foreach ($results  as $value) {
                $phone_number = $value['phone_number'];
                $sms = $value['sms'];
                $Sender_n = $value['sender'];
                $code = $value['code'];
                $stmt->execute();
            }

            return 1;
        } //end of if sms 
        else {

            if (isset($results["numbers"][0]['source'])) {
                $source = $results["numbers"][0]['source'];
               

            } else {
                $response->Msg = 'some keys are missing';
                $response = json_encode($response);
                echo $response;
                die();
            }

            /////////////////////////////
            $Final_results = (array) [];
            $applist = array("whatsapp", "facebook", "google", "telegram", "tiktok", "instagram", "zoom", "snapchat", "apple", "coinbase", "imo", "line", "linkedin", "microsoft", "netflix", "protonmail", "signal", "openai", "steam", "twitter", "uber", "wechat", "yahoo", "zoho", "vivo", "qsms", "lazada", "bigotv", "wesing");
            $multiapp = 0;
            if (isset($results["numbers"])) {
                if (!empty($results['numbers'])) {
                    if (isset($results['application'])) {
                        if (sizeof($results['application']) == 1) {
                            if ($results['application'][0] == "any") {
                                $results["application"] = $applist;
                                $multiapp = 1;
                            }
                        }

                        $Final_results["application"] = array_map('strtolower', $results["application"]);
                        $check = array_intersect($Final_results["application"], $applist);
                        if ($check != $Final_results["application"]) {

                            $response->Msg = "error with application name " . json_encode($results['application']);
                            $response = json_encode($response);
                            echo $response;
                           
                            die();
                        }
                    }
                   
                    $sql_del = 'DELETE FROM `bananaapi-number` WHERE `taked` = 0 AND `source` = "' . $source . '";';
                    $stmt = $pdo->prepare($sql_del);
                    $stmt->execute();

                    $sql = 'INSERT INTO  `bananaapi-number` (`phone_number`, `country_code`, `source`, `application`,`multiapp`,`type`) VALUES (:phone_number, :country_code, :source, :application,:multiapp,:_type)';

                    $stmt = $pdo->prepare($sql);

                    // Bind the parameters
                    $stmt->bindParam(':phone_number', $phone_number);
                    $stmt->bindParam(':country_code', $country_code);
                    $stmt->bindParam(':source', $source);
                    $stmt->bindParam(':application', $application);
                    $stmt->bindParam(':multiapp', $multi);
                    $stmt->bindParam(':_type', $type);

                    // Start a transaction
                    $pdo->beginTransaction();

                    try {

                        foreach ($results["numbers"] as $number) {
                            $phone_number = $number['phone_number'];
                            $country_code = $number['country_code'];
                            $source = $number['source'];
                            $type = 0;
                            
                            if (strpos($source, "hash") !== false) {
                               $type = 1;
                             
                            } else if(strpos($source, "new") !== false) {
                                $type = 2;
                            }
                            else if (strpos($source, "used") !== false) {
                                $type = 3;
                            }
                         
                            $multi = $multiapp;
                            foreach ($results['application'] as $application) {
                                $application = strtolower($application);
                                $stmt->execute();
                            }
                        }
                        // Commit the transaction
                        $pdo->commit();
                    } catch (Exception $e) {
                        file_put_contents($GLOBALS['logPath']."push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
                        //echo 'Caught exception: ',  $e->getMessage(), "\n";
                        // Rollback the transaction on error
                        $pdo->rollBack();
                        throw $e;
                    }




                    //////////////////////////////////


                }
            } // end of if numbers




        } //end of else sms




    } //end of try
    catch (Exception $e) {
        file_put_contents($GLOBALS['logPath']."push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
        
       // echo 'Caught exception: ',  $e->getMessage(), "\n";
    } //end of catch

} //end of function
function saveNumbers($numbers)
{
    require_once '../backend/config.php';
    $sql = 'INSERT INTO `bananaapi-number` (`phone_number`, `country_code`) VALUES ';
    $insertQuery = array();
    $insertData = array();
    foreach ($numbers as $row) {
        $insertQuery[] = '(?,?)';
        $insertData[] = $row['phone_number'];
        $insertData[] = $row['country_code'];
    }
    if (!empty($insertQuery)) {
        $sql .= implode(', ', $insertQuery);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($insertData);
    }
}

function getFile($source)
{
    require_once '../backend/config.php';

    try {

        $stmt = $pdo->prepare("SELECT phone_number from  `bananaapi-number` where `taked` = 0 and `source` = ?");
        $stmt->execute([$source]);
        $result = $stmt->fetchAll();
        //$stmt = $pdo->prepare("DELETE `bananaapi-number` where `taked` = 0 and `source` = ?");
        //$stmt->execute([$sournce]);
        // $file = fopen("file.csv", "w");
        // foreach ($result as $row) {
        //     fputcsv($file, $row);
        // }
        // fclose($file);
        // header('Content-Type: text/csv');
        // header('Content-Disposition: attachment; filename="file.csv"');
        // readfile("file.csv");

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $source . '.txt"');
        $file = fopen('php://output', 'w');
        foreach ($result as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    } catch (PDOException $e) {
        file_put_contents($GLOBALS['logPath']."push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
        //echo "Error: " . $e->getMessage();
    }
}

function resend($data){
    $url = "https://hash.goonline.company/receiver/hash_receiver.php";
   
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $time = time();
    $auth = md5($time."banana-api-passwordCode");
    $headers = array(
       "code: ".$time ,
       "Authorization: ".$auth,
       "Content-Type: application/json",
    );
    
   
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   
    
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $resp = curl_exec($curl);
    curl_close($curl);
    echo 123;
    var_dump($resp);
     


    
}

if (isset($_POST)) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization']) && isset($headers['Code'])) {
        $key = md5($headers['Code'] . "DijiTalSIM-@-passwordCode");
        if ($key == $headers['Authorization']) {
            Parser();
            die();
        }
    }

    echo '{"error":" POST Not Authorize"}';
    die();

}

if (isset($_GET)) {
    if (isset($_GET['Code'])) {
        if ($_GET['Code'] == "DownloadSourceByFile") {
            if (isset($_GET['source'])) {
                //echo $_GET['source'];
                getFile($_GET['source']);
                die();
            }
        }
    }
    echo '{"error":"Not Authorize"}';
    die();
}

