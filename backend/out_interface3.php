<?php
// echo '{"ResponseCode":5,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
//  die();
//session_start();

require_once 'outAPI/middelwareapi_outAPI2.php';
include_once "mylogger.php";
include_once "redisconfig.php";
$logger = new MyLogger();
$out_api = new out_api();

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
        // echo"api okk ".$api_key;
    }
    if ($api_key != null) {
        if ($redis) {
            $redisKey = 'check_api:' . $api_key;           
            if ($redis->exists($redisKey)) {
                $_check = $redis->get($redisKey);
            } else {
                $_check = $out_api->check_api_key($api_key);
                $redis->set($redisKey, $_check ? 1 : 0);
            }
        } else {

            $_check = $out_api->check_api_key($api_key);
        }
 
        //$_check = $out_api->check_api_key($api_key);
        //$_check = true;
        if ($_check == true || (isset($_GET['adm']) && $_GET['adm'] == 1)) {
            //var_dump( $_MY );
            if (isset($_MY['action'])) {
                switch ($_MY['action']) {

                    case "getbalance":
                        $_check = $out_api->check_api_key($api_key);
                        $result->Result = $_check;
                        // $result = $_check;
                        echo json_encode($result);
                        die();
                        //break;

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

                            $timestamp = time();
                            $key = "bulk_numbers:$api_key";
                            $existingTimestamp = $redis->get($key);
                            // If the key exists and the timestamp is within the time limit, deny the request
                            if ($existingTimestamp !== null) {
                                $test = intval($timestamp) - intval($existingTimestamp);
                                if ($test < 3) {
                                    $result = (object) [
                                        'ResponseCode' => 2,
                                        'Msg' => "Request rate limit exceeded. Please wait before making another request",
                                        'Result' => null,
                                    ];
                                    echo json_encode($result);
                                    die();
                                }
                            }
                            $redis->setex($key, 5, $timestamp);
                            //  $status = 1;
                            // $tableName = 'country_status';
                            // $country = $_MY['country'];
                            // $status = $redis->get("$tableName:$country");


                            // $status = 1;
                            // //$tableName = 'country_status';
                            

                            $country = $_MY['country'];
                            if ($redis) {
                                $tableName = 'countries_box';
                                $status = $redis->sMembers("$tableName:$country");
                                // $status = $redis->get("$tableName:$country");
                            }
                            if ($status) {
                                $sources = $redis->sMembers("CountryPerm:$api_key");
                                // echo json_encode($sources);
                                // die();
                                if (array_intersect($sources, $status)) {
                                    $result = $out_api->getnumber($api_key, $_MY['appcode'], $_MY['country'], $carrier = $carrier, $available = 0, $count = $count);
                                    echo json_encode($result);
                                    die();
                                }
                                else echo '{"ResponseCode":2,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
                                die();

                            } else echo '{"ResponseCode":2,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
                            die();
                        }
 
                        $result->Result = "need to set count param";
                        break;

                    case "getnumber":
                        # developer token
                        // if($api_key != "4ae612e4dacda084e7da8115937599482774b4ebc894d27130858c6d92f50f35"  && $api_key != "cb3f58bc6fa770c27f3472ad17f6f5f0349b708b9fa07f6fec396c2042e5a83e")
                        // {
                        //     echo '{"ResponseCode":5,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
                        //     die();     
                        // }


                        if ((isset($_MY['appcode'])) && (isset($_MY['country']))) {
                            $carrier = '';
                            if (isset($_MY['carrier'])) {
                                $carrier = $_MY['carrier'];
                            }

                            $status = 1;
                            //$tableName = 'country_status';
                            

                            $country = $_MY['country'];
                            if ($redis) {
                                $tableName = 'countries_box';
                                $status = $redis->sMembers("$tableName:$country");
                                // $status = $redis->get("$tableName:$country");
                            }
                            if ($status) {
                                $sources = $redis->sMembers("CountryPerm:$api_key");
                                // echo json_encode($sources);
                                // die();
                                if (array_intersect($sources, $status)) {
                                   
                                    $result = $out_api->getnumber($api_key, $_MY['appcode'], $_MY['country'], $carrier = $carrier, $available = 0, $count = 1);
                                    echo json_encode($result);
                                    die();
                                }
                                else echo '{"ResponseCode":2,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
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
                        //echo '{"Result":"1"}';

                        $result = $out_api->get_channels($api_key, $_MY['country'], $_MY['count'], $_MY['first']);

                        if (isset($result->ANY)) {
                            // if($result->ANY !='ANY')
                            // {
                            req_callback($result->Id, $result->ANY);
                            unset($result->ANY);

                            //  }
                        }
                        unset($result->Id);
                        echo json_encode($result);
                        die();
                        break;
                    case "down_result":
                        //echo '{"Result":"1"}';
                        $result = $out_api->down_result($api_key, $_MY['id']);
                        echo ($result);
                        die();
                        break;
                    case "down_hashs":
                        //echo '{"Result":"1"}';
                        $result = $out_api->down_hashs($api_key, $_MY['id']);
                        echo ($result);
                        die();
                        break;
                    case "wrong_code":
                        // echo '{"Result":"1"}';
                        $result = $out_api->worng_code($api_key, $_MY['id']);
                        echo json_encode($result);
                        die();
                        break;

                    case "get_available":

                        if ($redis) {
                            $key = "get_available:$api_key";
                            // $result = $out_api->get_available($api_key, "wa", "NP", $carrier = $carrier, $available = 1);

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

                        // Add other processing if needed
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
