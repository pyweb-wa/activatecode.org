<?php

require_once 'general_in_api.php';

class gsimonline_api
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
    public function getnumber( $country)
    {

        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
           // if ($token) $this->token = $token;
            //{"status":"success","msg":"success","number":"9779846727367","time":2.09284305572509765625}
            //{"api_id":29,"name":"globalLocal","number":"23675194585","msg":"success","time":0.3159010410308837890625}
            //echo $token;
            if($country == "PS"){
                $country = 972;
            }
            $url = 'https://gsim.online/api/getNumber/'.$this->token.'?country='.$country;
            //echo $url."<br>";
            $response = $this->in_api->getnumber($url);
            //echo $response."<br>";
            $response = json_decode($response, true);
            if (isset($response["msg"])) {
                if ($response["msg"] == "success") {
                    if(isset($response['number']))
                    {
                        $number = $response["number"];
                        //$number = str_replace("+", "", $number["number"]);
                        $result->ResponseCode = 0;
                        $result->{"Result"} = (object) [
                        'Id' => $number,
                        'Number' => $number,
                        'App' => "Whatsapp",
                    ];
                    return $result;
                    }
                    
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
            $url = 'https://gsim.online/api/getMessage/'.$this->token.'/'.$id;
            //echo $url;
            $response = $this->in_api->get_balance($url);
         //   $this->logger->Add($response, basename(__FILE__));
            $response = json_decode($response, true);
            $message = "";
            if (isset($response["code"])) {
                if ($response["code"]) {
                    if (isset($response["message"])) {
                        $message = $response["message"];
                    }
                            
                        $result->ResponseCode = 0;
                        $result->Result = (object) [
                            'SMS' => $message,
                            'Code' => $response["code"],
                        ];
                        return $result;
                        } 

                       
                    }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
            $result->ResponseCode = 1;
            $result->Msg = "waiting for sms";
            return $result;

    }

    private function load_Acess()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["gsim.online"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
