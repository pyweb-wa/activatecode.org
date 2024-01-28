<?php

require_once 'general_in_api.php';

class vak_sms
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
    public function getnumber($service,$country)
    {
        //$service = "wa";
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
            
             
            // $url = 'https://gsim.online/api/getNumber/'.$this->token.'?country='.$country;
            $url = "https://vak-sms.com/api/getNumber/?apiKey=$this->token&service=$service&country=$country"; 
           
          
           $response = $this->in_api->getnumber($url); 
           // $this->logger->Add($url. "Response: ".$response, basename(__FILE__),"vak-sms.log");
            
            $response = json_decode($response, true);
            if (isset($response["tel"])) { 
                    if(isset($response['idNum']))
                    {
                        $number = $response["tel"];  
                        $idNum = $response["idNum"]; 
                        $result->ResponseCode = 0;
                        $result->{"Result"} = (object) [
                        'Id' => $idNum,
                        'Number' => $number,
                        'App' => "Whatsapp",
                    ];
                    return $result;
                    } 
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"vak-sms.log");

           
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
            // $url = 'https://gsim.online/api/getMessage/'.$this->token.'/'.$id;
            $url = "https://vak-sms.com/api/getSmsCode/?apiKey=$this->token&idNum=$id";
            
            $response = $this->in_api->get_balance($url);
         //   $this->logger->Add($response, basename(__FILE__));
            $this->logger->Add($url. "Response: ".$response, basename(__FILE__),"vak-sms.log");
            $response = json_decode($response, true);
            $message = "";
            if (isset($response["smsCode"])) {
                if ($response["smsCode"]) {
                        $smsCode = $response["smsCode"];
                        $result->ResponseCode = 0;
                        $result->Result = (object) [
                            'SMS' => $smsCode,
                            'Code' => $smsCode,
                        ];
                        return $result;
                        } 
                    }
        } catch (Exception $e) {
            
            $this->logger->Add($e->getMessage(), basename(__FILE__),"vak-sms_err.log");
        }
            $result->ResponseCode = 1;
            $result->Msg = "waiting for sms";
            return $result;

    }

    private function load_Acess()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["vak-sms"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
