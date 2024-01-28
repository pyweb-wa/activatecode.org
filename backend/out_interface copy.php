<?php
session_start();

require_once 'outAPI/middelwareapi_outAPI.php';
include_once "mylogger.php";
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

        $_check = $out_api->check_api_key($api_key);
        if ($_check == true || (isset($_GET['adm']) && $_GET['adm'] == 1)) {
            //var_dump( $_MY );
            if (isset($_MY['action'])) {
                switch ($_MY['action']) {

                    case "getbalance":
                        $result->Result = $_check;
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
                                
                                $result = $out_api->getnumber($api_key, $_MY['appcode'], $_MY['country'], $carrier = $carrier,$available=0,$count=$count);
                                echo json_encode($result);
                                die();
                            }
                            $result->Result="need to set count param";
                            break;
    
                    case "getnumber":
                       
       
                        if ((isset($_MY['appcode'])) && (isset($_MY['country']))) {
                            $carrier = '';
                            if (isset($_MY['carrier'])) {
                                $carrier = $_MY['carrier'];
                            }
                           
                            $result = $out_api->getnumber($api_key, $_MY['appcode'], $_MY['country'], $carrier = $carrier,$available=0,$count=1);
                            echo json_encode($result);
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

                        $result = $out_api->get_channels($api_key, $_MY['country'], $_MY['count'],$_MY['first']);

                        if(isset($result->ANY)){
                           // if($result->ANY !='ANY')
                           // {
                                req_callback($result->Id,$result->ANY);
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
                        if(isset($_GET['adm']) && $_GET['adm']==1 ){
                            $carrier = '';
                            $result = $out_api->get_availableadmin($api_key,$available = 1);
                                echo json_encode($result);
                                die();
                        }else{
                            $carrier = '';
                            $result = $out_api->get_available($api_key, "wa", "NP", $carrier = $carrier,$available = 1);
                                echo json_encode($result);
                                die();
                        }
                        
                    case "get_availablehash":
                        $carrier = '';
                        $result = $out_api->get_available($api_key, "wa", "NP", $carrier = $carrier,$available = 2);
                            echo json_encode($result);
                            die();
                    case "STATS_NUMBER":
                        $carrier = '';
                        $result = $out_api->stats_numbers($_MY['action'],$api_key);
        
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

function req_callback($id,$count)
{
    $xml = file_get_contents("http://old-channels.mixsimverify.com/receiver2.php?id=".$id."&check=".$count);
}