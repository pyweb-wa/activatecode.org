<?php
// echo '{"ResponseCode":5,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
//  die();
session_start();

require_once 'outAPI/middelwareapi_outAPI.php';
include_once "mylogger.php";
include_once "redisconfig.php"; 
$logger = new MyLogger();
$out_api = new out_api(); 
$time_period_expire = 1;
$max_calls_limit = 2;

$_MY = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_MY = $_POST;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $_MY = $_GET;
    }
}

if ($_MY) {
    $result = (object) [
        'ResponseCode' => 0,
        'Msg' => 'OK',
        'Result' => null,
    ];

    $api_key = null;
    if (isset($_MY['api_key'])) {
        $api_key = $_MY['api_key'];
    } else if (isset($_SESSION['api_key'])) {
        $api_key = $_SESSION['api_key'];
    }
    if ($api_key != null) {
        $blacklist = "blacklist.txt";
        $file_array = file($blacklist, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (in_array($api_key, $file_array)) {
            $result = (object) [
                'ResponseCode' => 2,
                'Msg' => "your account has been disabled, contact us !!",
                'Result' => null,
            ];

            echo json_encode($result);
            die();
        }
        $_check = true;
        if ($_check == true || (isset($_GET['adm']) && $_GET['adm'] == 1)) {
            if (isset($_MY['action'])) {
                switch ($_MY['action']) {

                    case "getbalance":
                        $result->Result = $_check;
                        echo json_encode($result);
                        die();
                        break;

                    case "getapplist":
                        $result->Result = $out_api->getapplist($api_key);
                        echo json_encode($result);
                        die();
                        break;

                    case "getbulk_numbers":
                        if ((isset($_MY['appcode'])) && (isset($_MY['country'])) && (isset($_MY['count']))) {
                            $carrier = '';
                            if (isset($_MY['carrier'])) {
                                $carrier = $_MY['carrier'];
                            }

                            if (ctype_digit($_MY['count'])) {
                                $count = $_MY['count'];
                            } else {
                                $result = (object) [
                                    'ResponseCode' => 2,
                                    'Msg' => "count params must be integer",
                                    'Result' => null,
                                ];

                                echo json_encode($result);
                                die();
                            }
                            if ($count < 5 || $count > 100) {
                                $result = (object) [
                                    'ResponseCode' => 2,
                                    'Msg' => "count  must be between 5 and 100 only",
                                    'Result' => null,
                                ];

                                echo json_encode($result);
                                die();
                            }
                            if ($existingTimestamp !== null && ($timestamp - $existingTimestamp) < 3) {
                                $result = (object) [
                                    'ResponseCode' => 2,
                                    'Msg' => "Request rate limit exceeded. Please wait before making another request.",
                                    'Result' => null,
                                ];

                                echo json_encode($result);
                                die();
                            }
                            die();
                            $redis->setex($key, 30, $timestamp);
                            $result = $out_api->getnumber($api_key, $_MY['appcode'], $_MY['country'], $carrier = $carrier, $available = 0, $count = $count);
                            echo json_encode($result);
                            die();
                        }
                        $result->Result = "need to set count param";
                        break;

                    case "getnumber":   
                        
                        
                    // $key = "CountryPerm:" . $api_key;
                    // $field = "country";
                    // $country = $redis->hGet($key, $field);
                    // if ($country !== false)
                        
                        if ($redis){
                            $counter_key = 'counter:' . $api_key;
                            if (!$redis->exists($counter_key)) {                               
                                $redis->set($counter_key, 1);                               
                                $redis->expire($counter_key, $time_period_expire); 
                            } else {
                                $redis->incr($counter_key);
                                $total_user_calls = $redis->get($counter_key);                               
                                if ($total_user_calls > $max_calls_limit) {
                                    echo '{"ResponseCode":2,"Msg":"Too many requests","Result":'.$total_user_calls.'}';
                                    die();
                                } 
                            } 
                            echo '{"ResponseCode":0,"Msg":"Request processed successfully","Result":'.$total_user_calls.'}';
                        }
                        die();



                        if ((isset($_MY['appcode'])) && (isset($_MY['country']))) {
                            $carrier = '';
                            if (isset($_MY['carrier'])) {
                                $carrier = $_MY['carrier'];
                            }
                            if ($api_key == "e7414e75fb8acc938ad625f2830d0b4bef216e4d6ceecebcd6b1987fe483772a") {
                                $currentSecond = date('s');
                                $lastDigit = $currentSecond % 10;
                                if ($lastDigit == 3 || $lastDigit != 9) {
                                    echo '{"ResponseCode":5,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
                                    die();
                                }
                            }
                            $status = 1;
                            $tableName = 'country_status';
                            $country = $_MY['country'];
                            if ($redis) {
                                $status = $redis->get("$tableName:$country");
                            }
                            if ($status) {
                                $result = $out_api->getnumber($api_key, $_MY['appcode'], $_MY['country'], $carrier = $carrier, $available = 0, $count = 1);
                                echo json_encode($result);
                                die();
                            } else echo '{"ResponseCode":2,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
                            die();
                        }

                        break;

                    case "getcode":
                        if (isset($_MY['id'])) {
                            $result = $out_api->get_verificationcode($api_key, $_MY['id']);
                            echo json_encode($result);
                            die();
                        }
                        break;

                    case "getbulkcode":
                        if (isset($_MY['array'])) {

                            $result = $out_api->get_verificationcode($api_key, $_MY['id']);
                            echo json_encode($result);
                            die();
                        }
                        break;

                    case "expired":
                        if (isset($_MY['id'])) {
                            $result->Result = $out_api->order_cancelation($api_key, $_MY['id']);
                            echo json_encode($result);
                            die();
                        }
                        break;

                    case "restnumber":
                        $result = $out_api->rest_number($api_key);
                        echo json_encode($result);
                        die();
                        break;

                    case "getchannels":
                        $result = $out_api->get_channels($api_key, $_MY['country'], $_MY['count'], $_MY['first']);
                        if (isset($result->ANY)) {
                            req_callback($result->Id, $result->ANY);
                            unset($result->ANY);
                        }
                        unset($result->Id);
                        echo json_encode($result);
                        die();
                        break;
                    case "down_result":
                        $result = $out_api->down_result($api_key, $_MY['id']);
                        echo ($result);
                        die();
                        break;
                    case "down_hashs":
                        $result = $out_api->down_hashs($api_key, $_MY['id']);
                        echo ($result);
                        die();
                        break;
                    case "wrong_code":
                        $result = $out_api->worng_code($api_key, $_MY['id']);
                        echo json_encode($result);
                        die();
                        break;

                    case "get_available":

                        if ($redis) {
                            $key = "get_available:$api_key";
                            if ($redis->exists($key)) {
                                $result = $redis->get($key);
                                $result = json_decode($result, true);
                            } else {
                                $carrier = '';
                                $result = $out_api->get_available($api_key, "wa", "NP", $carrier = $carrier, $available = 1);
                            }
                        } else {
                            $carrier = '';
                            $result = $out_api->get_available($api_key, "wa", "NP", $carrier = $carrier, $available = 1);
                        }
                        echo json_encode($result);
                        die();


                        break;
                    case "get_availablehash":
                        $carrier = '';
                        $result = $out_api->get_available($api_key, "wa", "NP", $carrier = $carrier, $available = 2);
                        echo json_encode($result);
                        die();
                    case "STATS_NUMBER":
                        $carrier = '';
                        $result = $out_api->stats_numbers($_MY['action'], $api_key);

                        echo json_encode($result);
                        die();

                    default:
                        $result->ResponseCode = 1;
                        $result->Msg = 'ERROR Wrong Action';
                        $result->Result = '';
                        break;
                }
            } else {
                $result->ResponseCode = 1;
                $result->Msg = 'ERROR Action required';
                $result->Result = '';
            }
        } else {
            if (isset($_MY['action'])) {
                switch ($_MY['action']) {
                    case "getcode":
                        if (isset($_MY['id'])) {
                            $result = $out_api->get_verificationcode($api_key, $_MY['id']);
                            echo json_encode($result);
                            die();
                        }
                        break;

                    default:
                        $result->ResponseCode = 1;
                        $result->Msg = 'ERROR Wrong Action';
                        $result->Result = '';
                        break;
                }
            }
            $result->ResponseCode = 1;
            $result->Msg = 'API Key Error: Invalid API key or your API has been disabled.';
            $result->Result = null;
        }
    } else {
        $result->ResponseCode = 1;
        $result->Msg = 'ERROR Api key required';
        $result->Result = '';
    }
    echo json_encode($result);
}

function req_callback($id, $count)
{
    $xml = file_get_contents("http://old-channels.mixsimverify.com/receiver2.php?id=" . $id . "&check=" . $count);
}
