<?php

$logPath = "/var/www/smsmarket/logging/";

function Parser()
{
    $response = (object) [
        'ResponseCode' => 0,
        'Msg' => 'error',
        'Result' => null,
    ];

    $date = date('d-m-Y', time());
    $t = time();
    $time_string = $date . '-' . $t;

    $content = file_get_contents('php://input');

    file_put_contents($GLOBALS['logPath'] . '/_data_.log', file_get_contents('php://input'), FILE_APPEND);

    try {
        # for encoding >
        $content = preg_replace('/[[:cntrl:]]/', '', $content);
        if (isJson($content) == 1) {

            $req_dump = json_decode($content, JSON_UNESCAPED_SLASHES);
            $results = (array) [];
            $type = "";

            if (isset($req_dump["results"])) {

                if (!empty($req_dump['results'])) {
                    foreach ($req_dump['results'] as $value) {
                        if (isset($value["sms"]) && isset($value["phone_number"]) && isset($value["sender"])) {
                            //echo 123123;
                            //die();
                            //resend($content);
                            //die();
                            $code = "";
                            if (strpos(strtolower($value["sms"]), "whatsapp") !== false || strpos(strtolower($value["sms"]), "can also tap on this link") !== false || strpos(strtolower($value["sender"]), "whatsapp") !== false) {

                                //if (strpos(strtolower($value["sms"]), "whatsapp") !== false) {
                                $pattern = '/\d{3}-\d{3}/';
                                preg_match($pattern, $value["sms"], $matches);
                                if (sizeof($matches) >= 1) {
                                    $number = $matches[0];
                                    $code = str_replace("-", "", $number);

                                }

                            } else if (strpos(strtolower($value["sms"]), "google") !== false) {
                                preg_match_all('/\d+/', $value["sms"], $matches);
                                if (sizeof($matches) >= 1) {
                                    $code = implode('', $matches[0]);
                                    $code = str_replace("-", "", $code);
                                }

                            }


                            else if (strpos($value["sms"], 'p5sV6') !== false || strpos($value["sms"], ':') !== false) {
                                // Split the string on "/:"
                                $parts = explode(':', $value["sms"]);

                                // Check if there is a part containing the code
                                if (count($parts) > 1) {
                                    $code = $parts[1];

                                    // Use regular expression to extract the code
                                    if (preg_match('/(\d{3}-\d{3})/', $code, $matches)) {
                                        $code = str_replace('-', '', $matches[1]);
                                       //echo $code;
                                    }
                                }

                            }
                             else {
                                $new = str_replace(":>4:", "", $value["sms"]);
                                $all_digits = preg_replace('/\D/', '', $new);
                                $size = strlen($all_digits);

                                if ($size >= 6) {
                                    $code = substr($all_digits, 0, 6);
                                } else {
                                    $code = "";
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
                    foreach ($req_dump['numbers'] as $value) {
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
                    if (isset($req_dump["source"])) {
                        $source = $req_dump["source"];
                        $count = delete_all($source);
                        // $response->Msg = 'OK';
                        // $response->Result = $count;
                    }
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
        file_put_contents($GLOBALS['logPath'] . "push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);

        // echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    $response = json_encode($response);
    echo $response;

    die();
}

function classificator($res)
{
    // Convert SMS to lowercase
    $sms = strtolower($res['sms']);
    $sender = strtolower($res['sender']);
    // Search for keywords and return the type

    $classifier = [
        [
            "type" => "whatsapp",
            "match" => [
                "whatsapp",
                "واتساب",
            ],
        ],
        [
            "type" => "facebook",
            "match" => [
                "facebook",
            ],
        ],
    ];
    switch (true) {
        case (strpos($sms, 'whatsapp') !== false ||
                strpos($sms, 'واتساب') !== false) ||
            strpos($sender, 'whatsapp') !== false:
            $type = 'WhatsApp';
            if ($sender == "wom") {
                $type = 'Unknown';
            }

            break;
        case strpos($sms, 'facebook') !== false ||
            strpos($sender, 'facebook') !== false:
            $type = 'Facebook';
            break;
        case strpos($sms, 'viber') !== false ||
            strpos($sender, 'viber') !== false:
            $type = 'Viber';
            break;
        case strpos($sms, 'instagram') !== false:
            $type = 'Instagram';
            break;
        case strpos($sms, 'telegram') !== false:
            $type = 'Telegram';
            break;
        case strpos($sms, 'twitter') !== false:
            $type = 'Twitter';
            break;
        case strpos($sms, 'yalla') !== false:
            $type = 'Yalla';
            break;
        case strpos($sms, 'astropay') !== false:
            $type = 'astropay';
            break;
        case strpos($sms, 'wechat') !== false:
            $type = 'WeChat ';
            break;
        case strpos($sms, 'vk:') !== false:
            $type = 'vk';
            break;
        case strpos($sms, 'paypal') !== false:
            $type = 'PayPal';
            break;
        case strpos($sms, 'uber') !== false:
            $type = 'Uber';
            break;
        case strpos($sms, 'talana') !== false:
            $type = 'talana';
            break;
        case strpos($sms, 'microsoft') !== false:
            $type = 'Microsoft';
            break;
        case strpos($sms, 'tinder') !== false:
            $type = 'tinder';
            break;
        case strpos($sms, 'google') !== false
            || preg_match('/G-\d{6}/', $sms)
            || strpos($sender, 'google') !== false:
            $type = 'Google';
            break;
        case strpos($sms, 'naver') !== false:
            $type = 'Naver';
            break;
        case strpos($sms, 'openai') !== false:
            $type = 'OpenAI';
            break;
        case strpos($sms, 'amazon') !== false:
            $type = 'Amazon';
            break;
        case strpos($sms, 'dott') !== false:
            $type = 'Dott';
            break;
        case strpos($sms, 'locket widget') !== false:
            $type = 'locket widget';
            break;
        case strpos($sms, 'happn') !== false:
            $type = 'happn';
            break;
        case strpos($sms, 'hinge dating') !== false:
            $type = 'Hinge Dating';
            break;
        case strpos($sms, 'netease') !== false:
            $type = 'Netease';
            break;
        case strpos($sms, 'cabify') !== false:
            $type = 'Cabify';
            break;
        case strpos($sms, 'tiktok') !== false:
            $type = 'Tiktok';
            break;
        case strpos($sms, 'bereal') !== false:
            $type = 'BeReal';
            break;
        case strpos($sms, 'vulkanvegas') !== false:
            $type = 'VulkanVegas';
            break;
        case strpos($sms, 'mismo') !== false:
            $type = 'mismo';
            break;
        case strpos($sms, 'didi') !== false:
            $type = 'didi';
            break;
        case strpos($sms, 'grab') !== false:
            $type = 'grab';
            break;
        case strpos($sms, 'your messenger verification code') !== false:
            $type = 'Google';
            break;
        case strpos($sms, 'wishpost') !== false:
            $type = 'WishPost';
            break;
        case strpos($sms, 'truecaller') !== false:
            $type = 'Truecaller';
            break;
        case strpos($sms, 'hopper') !== false:
            $type = 'Hopper';
            break;
        case strpos($sms, 'movistar') !== false:
            $type = 'Movistar';
            break;
        case strpos($sms, 'wert') !== false:
            $type = 'Wert';
            break;

        case strpos($sms, 'community gaming') !== false:
            $type = 'CommunityGaming';
            break;
        // case strpos($sms, 'hopper') !== false:
        //     $type = 'Hopper';
        //     break;
        default:
            $type = 'Unknown';
            break;
    } //grab

    return $type;

}

function resend($data)
{
    $url = "https://hash.goonline.company/receiver/hash_receiver.php";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $time = time();
    $auth = md5($time . "banana-api-passwordCode");
    $headers = array(
        "code: " . $time,
        "Authorization: " . $auth,
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

function delete_fromCountriesControl($source){
    require_once '../backend/config.php';
    //$sql = "DELETE FROM `countries_control` where source not in (select source from country_stats) AND countries_control.`created_time` <= NOW() - INTERVAL 30 MINUTE;";
    $sql = "DELETE FROM `countries_control` where source = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$source]);
}
function isJson($string)
{

    json_decode($string, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);

    return json_last_error() === JSON_ERROR_NONE;
}

function delete_all($source)
{
    require_once '../backend/config.php';

    $sql = "DELETE FROM `bananaapi-number` WHERE `is_finished` = 0 and source = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$source]);
    $count = $stmt->rowCount();
    delete_fromCountriesControl($source);
    return $count;
    //print("Deleted $count rows.\n");
}

function deletenumberfromServer($number){
    $number = str_replace("+","",$number);
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_PORT => "8085",
      CURLOPT_URL => "http://35.195.32.6:8085/number_list/delete_number",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "DELETE",
      CURLOPT_POSTFIELDS => '{"number": "'.$number.'"}',
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer 422ed1d6532611eead4e42010a840005",
        "Content-Type: application/json"
      ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    file_put_contents($GLOBALS['logPath'] . "sms_delete.log",  $number. ' response_myroutetester => ' . $response  . "\n", FILE_APPEND);

    curl_close($curl);
    deletenumberfromServer2($number);
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }

    }

    function deletenumberfromServer2($number){
        $number = str_replace("+","",$number);
        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_PORT => "8085",
          CURLOPT_URL => "http://34.105.173.84:8085/number_list/delete_number",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 10,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "DELETE",
          CURLOPT_POSTFIELDS => '{"number": "'.$number.'"}',
          CURLOPT_HTTPHEADER => [
            "Authorization: Bearer fb398933608c11eead4e42010a840005",
            "Content-Type: application/json"
          ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        file_put_contents($GLOBALS['logPath'] . "sms_delete.log",  $number. ' response_jikatel => ' . $response  . "\n", FILE_APPEND);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }

        }




function saveResult($results, $type)
{

    require_once '../backend/config.php';

    try {

        $response = (object) [
            'ResponseCode' => 0,
            'Msg' => 'error',
            'Result' => null,
        ];
        //
        if ($type == "sms") {

            $stmt = $pdo->prepare("INSERT INTO `bananaapi-results`(`phone_number`,  `sms`, `Sender_n`,`code`,`application`) VALUES (:phone_number, :sms, :Sender_n, :code, :application)");
            $stmt->bindParam(':phone_number', $phone_number);
            $stmt->bindParam(':sms', $sms);
            $stmt->bindParam(':Sender_n', $Sender_n);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':application', $application);

            foreach ($results as $value) {
                $application = strtolower(classificator($value));
                $phone_number = $value['phone_number'];
                $sms = $value['sms'];
                $Sender_n = $value['sender'];
                $code = $value['code'];
                $stmt->execute();
                deletenumberfromServer($phone_number);
            }

            return 1;
        } //end of if sms
        else {
            echo '{"message":"you can add box by controller page in Box Info tab"}';
            die();
            if (isset($results["numbers"][0]['source'])) {
                $source = strtolower($results["numbers"][0]['source']);
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
                    
                    $country_n = $results["numbers"][0]['country_code'];
                    $source = $country_n . "_" . $results["numbers"][0]['source'];
                   
                    $sql_del = 'DELETE FROM `bananaapi-number` WHERE `is_finished` = 0 AND `source` = ?;';
                    $stmt = $pdo->prepare($sql_del);
                    $stmt->execute([$source]);
                    date_default_timezone_set('Asia/Beirut');
                    
                    // Create a DateTime object with the current time
                    $datetime = new DateTime();

                    // Format the DateTime object as a MySQL timestamp
                    $timestamp = $datetime->format('Y-m-d H:i:s');

                    $sql = 'INSERT INTO  `bananaapi-number` (`phone_number`, `country_code`, `source`, `application`,`multiapp`,`type`,`createdTime`) VALUES (:phone_number, :country_code, :source, :application,:multiapp,:_type,:createdTime)';

                    $stmt = $pdo->prepare($sql);

                    // Bind the parameters
                    $stmt->bindParam(':phone_number', $phone_number);
                    $stmt->bindParam(':country_code', $country_code);
                    $stmt->bindParam(':source', $source);
                    $stmt->bindParam(':application', $application);
                    $stmt->bindParam(':multiapp', $multi);
                    $stmt->bindParam(':_type', $type);
                    $stmt->bindParam(':createdTime', $timestamp);

                    // Start a transaction
                    $pdo->beginTransaction();

                    try {

                        foreach ($results["numbers"] as $number) {
                            $phone_number = $number['phone_number'];
                            $country_code = $number['country_code'];
                            $source = $country_code . "_" . $number['source'];
                            $type = 0;

                            if (strpos($source, "hash") !== false) {
                                $type = 1;

                            } else if (strpos($source, "new") !== false) {
                                $type = 2;
                            } else if (strpos($source, "used") !== false) {
                                $type = 3;
                            }
                            //echo $type;
                            $multi = $multiapp;
                            foreach ($results['application'] as $application) {
                                $application = strtolower($application);
                                $stmt->execute();
                            }
                        }
                        $sql = "INSERT INTO `countries_control` (country_id, source)
                        SELECT `countryList`.id, ?
                        FROM `countryList`
                        WHERE `countryList`.`country_char` = ?
                        ON DUPLICATE KEY UPDATE source = VALUES(source);";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$source, $country_code]);

                        // Commit the transaction
                        $pdo->commit();
                    } catch (Exception $e) {
                        date_default_timezone_set('Asia/Beirut');

                        // Create a DateTime object with the current time
                        $datetime = new DateTime();

                        // Format the DateTime object as a MySQL timestamp
                        $timestamp = $datetime->format('Y-m-d H:i:s');
                        file_put_contents($GLOBALS['logPath'] . "push_errors.log", $timestamp . ' Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);

                        //echo 'Caught exception: ',  $e->getMessage(), "\n";
                        // Rollback the transaction on error
                        $pdo->rollBack();
                        throw $e;
                    }

                    //////////////////////////////////

                }
            } // end of if numbers

        } //end of else sms

    } catch (Exception $e) { // end of try

        date_default_timezone_set('Asia/Beirut');

        // Create a DateTime object with the current time
        $datetime = new DateTime();

        // Format the DateTime object as a MySQL timestamp
        $timestamp = $datetime->format('Y-m-d H:i:s');
        file_put_contents($GLOBALS['logPath'] . "push_errors.log", $timestamp . ' Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);

        // echo 'Caught exception: ',  $e->getMessage(), "\n";
    } //end of catch
}
//end of function
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
        file_put_contents($GLOBALS['logPath'] . "push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);

        //echo "Error: " . $e->getMessage();
    }
}

if (isset($_POST)) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization']) && isset($headers['code'])) {
        $key = md5($headers['code'] . "ActivateC0de@-passwordCode");
        if ($key == $headers['Authorization']) {
            Parser();
            die();
        }
    }

    echo '{"error":" POST Not Authorize"}';
    die();

}

if (isset($_GET)) {
    if (isset($_GET['code'])) {
        if ($_GET['code'] == "DownloadSourceByFile") {
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
