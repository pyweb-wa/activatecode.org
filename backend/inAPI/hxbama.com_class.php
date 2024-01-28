<?php

require_once 'general_in_api.php';

class hxbama_api
{
    public function __construct()
    {
        include 'config.php';
        include_once "mylogger.php";

        $this->logger = new MyLogger();
        $this->in_api = new IN_API();
        $this->pdo = $pdo;
        $this->token = $this->load_Acess(); //read token from db
        $this->cnt = 0;
    }
    public $token;

    ## Admin

    public function getnumber($businessCode, $country, $token = null, $demo = false)
    {

        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'The current business is busy, no mobile number is available, please try again later',
                'Result' => null,
            ];
            
           
            if ($demo) {
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
            $url = "";
            #request
            #http://www.hxbama.com/yhapi.ashx?act=getPhone&token=edb76d52946ef7b8497b704c294a3354_1347&iid=1010&did=&country=mys&operator=&provi=&city=&seq=0&mobile=
            #response
            #1|0BAF1B569D5AC90C9C717CB6191B9A901A60BAD2A7FB2A15|2023-01-04T04:36:33|mys|60||COM184|1163833405
            if ($token) $this->token = $token;

            $url = 'http://www.hxbama.com/yhapi.ashx?act=getPhone&token=' . $this->token . '&iid=' . $businessCode . '&country=' . $country . '&operator=&provi=&city=&seq=0';
            $this->logger->Add("Phatnom request: " . $url, basename(__FILE__));

            $response = $this->in_api->getnumber($url);

            //$this->logger->Add($url, basename(__FILE__));
            $this->logger->Add("hxbama_api response: " . $response, basename(__FILE__));

            if (strpos($response, "|") == FALSE) {
               
                return $result;


            } 
            $items = explode("|", $response);
            
            if(sizeof($items) <2)
            {
              
                $result->ResponseCode = 1;
                $result->Msg = "The current business is busy, no mobile number is available, please try again later";
                return $result;
            }
            if($items[0]== 1 && sizeof($items) >=7 ){  
                               
                    $number =  $items[4].$items[7];
                    #TODO get App dynamic from db
                    $result->ResponseCode = 0;
                    $result->Msg = 'OK';
                    $result->{"Result"} = (object) [
                        'Id' => $items[1],
                        'Number' => $number,
                        'App' => "Whatsapp",
                    ];
                   ;
                    return $result;
                }
                
            
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
            
        }
        $result->ResponseCode = 1;

        $result->Msg = "The current business is busy, no mobile number is available, please try again later";
        
        return $result;
    }

    public function getverificationcode($id)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'waiting for sms',
                'Result' => null,
            ];
            $url = 'http://www.hxbama.com/yhapi.ashx?act=getPhoneCode&token='.$this->token .'&pkey='. $id;
           
            $response = $this->in_api->get_any($url);

            $this->logger->Add($response, basename(__FILE__));
           
            #{"code":"200","data":{"verificationCode":[{"businessCode":"","serialNumber":"1278798403325980672","vc":""}]},"message":"success"}

            if (strpos($response, "|") == FALSE) {
               
                return $result;


            } 
            $items = explode("|", $response);
           
            if(sizeof($items) <2)
            {
                $result->ResponseCode = 1;
                $result->Msg = "waiting for sms";
                return $result;
            }
            if($items[0]== 1 && sizeof($items) >=2 ){                   
                        $result->ResponseCode = 0;
                        $result->Result = (object) [
                            'SMS' => $items[2],
                            'Code' => $items[1],
                        ];

                    return $result;
                }
            
        }catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }

    private function get_AccessToKen()
    {

        #http://www.phantomunion.com:10023/pickCode-api/push/ticket?key=c7054b432f7e4d5c2d6ecae0a1066d5a
        $refresh = $this->get_refreshtoken();
        $this->logger->Add("refresh token: " . $refresh, basename(__FILE__));

        if ($refresh) {
            #{"code":"200","data":{"token":"8abee8d14af052cb9476dcab42046689_899"},"message":"success"}
            $response = $this->in_api->get_any('http://www.phantomunion.com:10023/pickCode-api/push/ticket?key=' . $refresh);
            $this->logger->Add("resssssssssss " . $response, basename(__FILE__));
            $response = json_decode($response, true);

            if (isset($response["code"])) {
                if ($response["code"] == "200") {
                    if (isset($response["data"])) {
                        if (isset($response["data"]["token"])) {
                            if ($response["data"]["token"]) {

                                #TODO change id dynamic from request
                                $stmt = $this->pdo->prepare('UPDATE foreignapi SET `Access_Token` =? WHERE Id_Api = 10');
                                $stmt->execute([$response["data"]["token"]]);
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

    private function get_refreshtoken()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Refresh_Token` from foreignapi where Name=? ');
        $stmt->execute(["hxbama.com"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Refresh_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }

    private function load_Acess()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["hxbama.com"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
