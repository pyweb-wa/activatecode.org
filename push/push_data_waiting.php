<?php

$logPath = "/var/www/smsmarket/logging/";

function Parser()
{
    $response = (object) [
        'ResponseCode' => 0,
        'Msg' => 'error',
        'Result' => null,
    ];

    try {
        $content = file_get_contents('php://input');
       
        ini_set('memory_limit', '512M');
        $decodedContent = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $results = [];

            if (isset($decodedContent['numbers'])) {
                $results = processNumbers($decodedContent['numbers']);
                if (!empty($results['numbers'])) {
                    $source =  $results['numbers'][0]['country_code']."_".$results['numbers'][0]['source'];
                    $timestamp = time();

                   
                    file_put_contents($GLOBALS['logPath'] . "/numbers/$source.$timestamp.json", $content . "\n\n", FILE_APPEND);
                    $count = sizeof($results['numbers']);
                    $response->ResponseCode = 1;
                    $response->Msg = 'OK';
                    $response->Result = "Receving $count numbers with source: $source";
                    echo json_encode($response);
                    die();
                    $s = saveResult($results, $decodedContent['application']);
                    if($s){
                        $response->ResponseCode = 1;
                        $response->Msg = 'OK';
                        $response->Result = count($results);
                    }
                    echo json_encode($response);
                    die();
                }
            } elseif (isset($decodedContent['source'])) {
                $count = delete_all($decodedContent['source']);
                $response->Msg = 'OK';
                $response->Result = "All numbers with source ". $decodedContent['source']. " has been deleted" ;
            }
        } else {
            $response->Msg = 'Invalid JSON format';
        }
    } catch (Exception $e) {
        handleException($e);
        
    }

    echo json_encode($response);
    die();
}

function processNumbers($numbers)
{
    $processedNumbers = [];

    foreach ($numbers as $value) {
        if (isset($value["country_code"], $value["phone_number"], $value["source"])) {
            $phoneNumber = str_replace("+", "", $value["phone_number"]);
            if ($phoneNumber && preg_match('/^[0-9]+$/', $phoneNumber)) {
                $processedNumbers[] = $value;
            }
        }
    }
    $new = [];
    $new['numbers'] = $processedNumbers;
    return $new;
}



function isJson($string)
{

    json_decode($string, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);

    return json_last_error() === JSON_ERROR_NONE;
}

function delete_all($source)
{
    require_once '../backend/config.php';

    $sql = "DELETE FROM `number_waiting_list` WHERE source = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$source]);
    $count = $stmt->rowCount();
    delete_fromCountriesControl($source);
    return $count;
    //print("Deleted $count rows.\n");
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

        if (!isset($results['numbers'][0]['source'])) {
            $response->Msg = 'some keys are missing '.$results['numbers'][0] ;
            echo json_encode($response);
            die();
        }

        $source = strtolower($results['numbers'][0]['source']);
       
        if (isset($results['numbers']) && !empty($results['numbers'])) {

            $country_n = $results['numbers'][0]['country_code'];
            $source = $country_n . "_" . $results['numbers'][0]['source'];

            // $sql_del = 'DELETE FROM `bananaapi-number` WHERE `is_finished` = 0 AND `source` = ?;';
            // $stmt_del = $pdo->prepare($sql_del);
            // $stmt_del->execute([$source]);

            $sql_insert = 'INSERT INTO `number_waiting_list` (`phone_number`, `country_code`, `source`, `createdTime`) VALUES (?, ?, ?, ?)';
            $stmt_insert = $pdo->prepare($sql_insert);

            date_default_timezone_set('Asia/Beirut');
            $datetime = new DateTime();
            $timestamp = $datetime->format('Y-m-d H:i:s');

            $pdo->beginTransaction();
            try {
                foreach ($results['numbers'] as $number) {
                    $phone_number = $number['phone_number'];
                    $country_code = $number['country_code'];
                    $source = $country_code . "_" . $number['source'];
                    $stmt_insert->execute([$phone_number, $country_code, $source,$timestamp]);
                   
                }

                $pdo->commit();
                return true;
            } catch (Exception $e) {
                handleException($e);
            }
        }
    } catch (Exception $e) {
        handleException($e);
    }
    return false;
}

function handleException($exception)
{   echo $exception->getMessage();
    date_default_timezone_set('Asia/Beirut');
    $datetime = new DateTime();
    $timestamp = $datetime->format('Y-m-d H:i:s');
    file_put_contents($GLOBALS['logPath'] . "push_errors.log", $timestamp . ' Caught exception: ' . $exception->getMessage() . "\n", FILE_APPEND);
}
        
        


// function getFile($source)
// {
//     require_once '../backend/config.php';

//     try {

//         $stmt = $pdo->prepare("SELECT phone_number from  `bananaapi-number` where `taked` = 0 and `source` = ?");
//         $stmt->execute([$source]);
//         $result = $stmt->fetchAll();
//         //$stmt = $pdo->prepare("DELETE `bananaapi-number` where `taked` = 0 and `source` = ?");
//         //$stmt->execute([$sournce]);
//         // $file = fopen("file.csv", "w");
//         // foreach ($result as $row) {
//         //     fputcsv($file, $row);
//         // }
//         // fclose($file);
//         // header('Content-Type: text/csv');
//         // header('Content-Disposition: attachment; filename="file.csv"');
//         // readfile("file.csv");

//         header('Content-Type: text/csv');
//         header('Content-Disposition: attachment; filename="' . $source . '.txt"');
//         $file = fopen('php://output', 'w');
//         foreach ($result as $row) {
//             fputcsv($file, $row);
//         }
//         fclose($file);
//     } catch (PDOException $e) {
//         file_put_contents($GLOBALS['logPath'] . "push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);

//         //echo "Error: " . $e->getMessage();
//     }
// }

if (isset($_POST)) {
    $headers = apache_request_headers();
    # print_r($headers);
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
