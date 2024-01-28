<?php

require_once 'general_in_api.php';

class digitalsim_api
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
    public function getnumber( $app,$country)
    {

        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
            
            $url = 'https://digitalsim.goonline.company/backend/out_interface.php?api_key='.$this->token.'&action=getnumber&appcode='.$app.'&country='.$country;
           // echo $url."<br>";die();
            $response = $this->in_api->getnumber($url);
            $this->logger->Add("GET_Number: ".$url." ||  " . $response, basename(__FILE__),"digitalsim.log");
            //$this->logger->Add("digitalsim response: " . $response, basename(__FILE__),"digitalsim.log");

            //echo $response."<br>";
            $response = json_decode($response, true);
            if (isset($response["ResponseCode"])) {
                if ($response["ResponseCode"] == 0) {
                    if(isset($response['Result']))
                    { 
                        if(isset($response['Result']['Number'])) {
                            $number = $response['Result']['Number'];
                            //$number = str_replace("+", "", $number["number"]);
                            $result->ResponseCode = 0;
                            $result->{"Result"} = (object) [
                            'Id' => $response['Result']['id'],
                            'Number' => $number,
                            'App' => $response['Result']['App'],
                        ];
                        return $result;
                        }
                      
                    }
                    
                } 
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"digitalsim_err.log");
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

            $url = 'https://digitalsim.goonline.company/backend/out_interface.php?api_key='.$this->token.'&action=getcode&id='.$id;
           
            $response = $this->in_api->getnumber($url);
            $this->logger->Add("GET_SMS: ".$url." ||  " . $response, basename(__FILE__),"digitalsim.log");

           
            //echo {"ResponseCode":0,"Msg":"OK","Result":{"SMS":" <#> Your WhatsApp code: 123456 You can also tap on this link to verify your phone: v.whatsapp.com/123456 Don't share this code with others 4sgLq1p5sV6","Code":"123456"}}
            $response = json_decode($response, true);
            if (isset($response["ResponseCode"])) {
                if ($response["ResponseCode"] == 0) {
                    if(isset($response['Result']))
                    { 
                        if(isset($response['Result']['Code'])) {
                            $Code = $response['Result']['Code'];
                          
                            $result->ResponseCode = 0;
                            $result->Result = (object) [
                            'SMS' => $response['Result']['SMS'],
                            'Code' =>$Code,
                        ];
                        return $result;
                        }
                      
                    }
                    
                }
            }
         
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"digitalsim_err.log");

            
        }
            $result->ResponseCode = 1;
            $result->Msg = "waiting for sms";
            return $result;

    }

    public function get_avialable($i)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

            $url = 'https://digitalsim.goonline.company/backend/out_interface.php?api_key='.$this->token.'&action=get_available';
           
            $response = $this->in_api->getnumber($url);
            $this->logger->Add($url, basename(__FILE__),"digitalsim.log");
            $this->logger->Add("digitalsim response: " . $response, basename(__FILE__),"digitalsim.log");
            $response = json_decode($response, true);
            if (isset($response["ResponseCode"])) {
                if ($response["ResponseCode"] == 0) {
                    if(isset($response['Result']))
                    { 
                        if(sizeof($response['Result']) >=1) {
                            return $response;
                            echo json_encode($response);
                            die();
                        }
                      
                    }
                    
                }
            }
         
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"digitalsim_err.log");

            
        }
            $result->ResponseCode = 1;
            $result->Msg = "waiting for sms";
            return $result;

    }

    private function load_Acess()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["digitalsim"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
