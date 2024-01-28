<?php

require_once 'general_in_api.php';

class simcode_api
{
    public function __construct()
    {
        include 'config.php';
        include_once "mylogger.php";

        $this->logger = new MyLogger();
        $this->in_api = new IN_API();
        $this->pdo = $pdo;
        $this->simcode_token = $this->gettoken(); //read token from db


    }
    public $simcode_token;

    private function simcode_response_validation($response, $rtn = null)
    {
        if ($response) {
            try {
                // $response = '{"ResponseCode": 0, "Msg": "OK", "Result": { "Id":1010, "Number": "399900112", "App":"Facebook", "Cost": 1000, "Balance": 99000}}';
                // # apt install php-xml
                // $response = json_decode(utf8_decode($response), true);

                $response = json_decode($response, true);

                if (isset($response["ResponseCode"])) {
                    if ($rtn) {
                        if (array_key_exists("Result", $response)) {
                            if (isset($response["Result"]["Cost"])) {
                                unset($response["Result"]["Cost"]);
                            }
                            if (isset($response["Result"]["Balance"])) {
                                unset($response["Result"]["Balance"]);
                            }

                            return $response;
                        }
                    }
                    if ($response["ResponseCode"] == 0) {
                        if (isset($response["Result"])) {
                            if (isset($response["Result"]["Cost"])) {
                                unset($response["Result"]["Cost"]);
                            }
                            if (isset($response["Result"]["Balance"])) {
                                unset($response["Result"]["Balance"]);
                            }

                            return $response["Result"];
                        }
                    }
                }
            } catch (Exception $e) {
                $this->logger->Add($e->getMessage(), basename(__FILE__));
                //echo 'Caught exception: ', $e->getMessage(), "\n";
            }

            return null;
        }
    }
    ## Admin
    public function simcode_getbalance()
    {
        try {

            $response = $this->in_api->get_balance('https://chothuesimcode.com/api?act=account&apik=' . $this->simcode_token);
            $response = $this->simcode_response_validation($response);

            if (isset($response["Balance"])) {

                return array($response["Balance"], 0);
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }

        return null;
    }
    ## Admin
    public function simcode_getapplist()
    {
        try {

            $response = $this->in_api->get_balance('https://chothuesimcode.com/api?act=app&apik=' . $this->simcode_token);
            $response = $this->simcode_response_validation($response);

            if (isset($response)) {

                return array($response, 0);
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return array(null, 1);
    }

    public function getnumber($appId, $carrier = null,$demo=false)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
            if($demo)
            {
                sleep(1);
                $this->in_api->getnumber("https://google.com");
                $result->ResponseCode = 0;
                $date = date_create();

                $result->{"Result"} = (object) [
                    'Id' => date_timestamp_get($date),
                    'Number' => "99" . date_timestamp_get($date),
                    'App' => 'Whatsapp',
                ];
                return $result;
        
            }

            if ($carrier) {
                $response = $this->in_api->getnumber('https://chothuesimcode.com/api?act=number&apik=' . $this->simcode_token . "&appId=" . $appId . "&carrier=" . $carrier);
            } else {
                $response = $this->in_api->getnumber('https://chothuesimcode.com/api?act=number&apik=' . $this->simcode_token . "&appId=" . $appId);
            }
            $this->logger->Add("Raw get number: " . $response, basename(__FILE__));
            // {"ResponseCode":0,"Msg":"OK","Result":{"Id":6405388,"Number":"345236918","App":"WhatsApp","Cost":0.3,"Balance":128.9}}
            $response = $this->simcode_response_validation($response, "full");
            if (isset($response["ResponseCode"])) {
                if ($response["ResponseCode"] == 0) {
                    $result->ResponseCode = 0;
                    $result->{"Result"} = (object) [
                        'Id' => $response["Result"]["Id"],
                        'Number' => "84" . $response["Result"]["Number"],
                        'App' => $response["Result"]["App"],
                    ];
                    return $result;
                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }

        $result->ResponseCode = 1;
        if (isset($response["Msg"])) {
            $result->Msg = $response["Msg"];
        } else {
            $result->Msg = "error";
        }
        // 
        $this->logger->Add(json_encode($result), basename(__FILE__));
        // 
        return $result;
    }

    public function simcode_getverificationcode($id)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];;

            $response = $this->in_api->get_balance('https://chothuesimcode.com/api?act=code&apik=' . $this->simcode_token . "&id=" . $id);
            $this->logger->Add("Raw getcode: " . $response, basename(__FILE__));

            $response = $this->simcode_response_validation($response, "full");

            if (isset($response["ResponseCode"])) {
                if ($response["ResponseCode"] == 0) {
                    $result->ResponseCode = 0;
                    $result->{"Result"} = (object) [
                        'SMS' => $response["Result"]["SMS"],
                        'Code' => $response["Result"]["Code"],

                    ];
                    return $result;
                } else {
                    $result->Msg = "waiting for sms";

                    return $result;
                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }

        $result->Msg = "error";


        return $result;
    }

    public function simcode_order_cancelation($id)
    {   #TODO wrong input data msg returned to user in web ui !!
        //{"ResponseCode":0,"Msg":"OK","Result":{"Refund":0,"Balance":140}}
        //{"ResponseCode":1,"Msg":"Wrong input data","Result":null}
        //{"ResponseCode":2,"Msg":"Already expired before","Result":null}
        try {

            $response = $this->in_api->get_balance('https://chothuesimcode.com/api?act=expired&apik' . $this->simcode_token . "&id=" . $id);
            $_response = $this->simcode_response_validation($response, "full");
            if (isset($_response)) {

                if (isset($_response["Refund"])) {
                    if ($_response["Refund"] >= 0) {
                        return array($response["Refund"], 0);
                    }
                }
            }
            $response = json_decode($response, true);

            if (isset($response["Msg"])) {

                return array(-1, 1);
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return array(-1, 1);
    }

    private function gettoken()
    {

        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["simcode"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
