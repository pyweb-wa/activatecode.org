<?php

require_once 'general_in_api.php';

class smson_api
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
    public function getnumber($service_of_api)
    {

        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
           // if ($token) $this->token = $token;
            //{"status":"success","msg":"success","number":"9779846727367","time":2.09284305572509765625}
            //echo $token;
            $url = 'http://activate.smson.me/api/v1?api_key='.$this->token.'&action=getNumber&service=wa';
            //ACCESS_NUMBER:1678041634393218:639852832043
            $response = $this->in_api->getnumber($url);
            if (strpos($response, "ACCESS_NUMBER") !== false) {
                $array = explode(':', $response);
                if(sizeof($array) == 3){
                $activationId = $array[1];
                $number = $array[2];
                $number = str_replace("+", "", $number);
                $result->ResponseCode = 0;
                $result->{"Result"} = (object) [
                'Id' => $activationId,
                'Number' => $number,
                'App' => "Whatsapp",
                        ];
                $url = "http://activate.smson.me/api/v1?api_key=".$this->token."&action=setStatus&id=".$activationId."&status=1";
                $response = $this->in_api->getnumber($url);
                if (strpos($response, "ACCESS_READY") !== false) {
                    return $result;


                }
                //return $result;

                }
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
                'Msg' => 'OK',
                'Result' => null,
            ];
            $url = "http://activate.smson.me/api/v1?api_key=".$this->token."&action=setStatus&id=".$activationId."&status=1";
            $check="";
            $response = $this->in_api->getnumber($url);
            
            if (strpos($response, "ACCESS_READY") !== false) {
               $check = 1;

            }

            $url = 'http://activate.smson.me/api/v1?api_key='.$this->token.'&action=getStatus&id='.$id;

            $response = $this->in_api->get_balance($url);
            if (strpos($response, "STATUS_OK") !== false) {
                $array = explode(':', $response);
                if(sizeof($array) == 2){
                    $code = $array[1];

                    $result->ResponseCode = 0;
                    $result->Result = (object) [
                        'SMS' => "",
                        'Code' => $code,
                    ];
                    return $result;

                }
            }

        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
            $result->ResponseCode = 1;
            $result->Msg = "waiting for sms ".$check;
            return $result;

    }

    private function load_Acess()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["smson.me"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
