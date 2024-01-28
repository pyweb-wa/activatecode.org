<?php
//http://web.520mqn.com/apihelp.aspx
//ID:adamtan8892
//pass:AAaa9999
require_once 'general_in_api.php';

class milktea_api
{
    public function __construct()
    {
        include 'config.php';
        include_once "mylogger.php";

        $this->logger = new MyLogger();
        $this->in_api = new IN_API();
        $this->pdo = $pdo;
        $this->token = $this->load_Acess(); //read token from db

    }
    public $token;

    public function getnumber($businessCode, $country, $token = null,$demo=false)
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
            $url = "";
            $country_of_api= $this->load_chinese_name($businessCode,$country);
            #request
            # get number http://web.520mqn.com/yhapi.ashx?act=getPhone&token=07d3b77e4170ceb32e2ddbf22ea5866a_6402&iid=1083&provi=英国
            #  
            # 1|2021-01-07T05:25:01|COM31|7404624754|44英国|英国  ba3d l 44 united kingdom -->country
            #
            $this->logger->Add("milktea : ||" .$businessCode."||". $country."||".$country_of_api."||", basename(__FILE__)); 

            if ($token) $this->token = $token;
            $url = 'http://web.520mqn.com/yhapi.ashx?act=getPhone&token=' . $this->token . '&iid='.$businessCode."&provi=".$country_of_api; //&provi=英国  . strtolower($country) ;
            $this->logger->Add("milktea request: " . $url, basename(__FILE__)); 
            $response = $this->in_api->getnumber($url); 
            $this->logger->Add("milktea response: " . $response, basename(__FILE__)); 
            $res_contents=explode("|",$response);
            if ($res_contents[0]=='1') { 
                if (count($res_contents)>5) { 
                        $number = "+". (int)$res_contents[4].$res_contents[3]; 
                        #TODO get App dynamic from db
                        $result->ResponseCode = 0;
                        $result->Result = (object) [
                            'Id' => $res_contents[3],
                            'Number' => $number,
                            'App' => "WhatsApp",
                        ]; 
                        return $result; 
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

            //request
            # get sms http://web.520mqn.com/yhapi.ashx?act=getPhoneCode&token=07d3b77e4170ceb32e2ddbf22ea5866a_6402&iid=1083&mobile=12675108385
            # response 
            #Successful return: 1|Verification code number|Complete SMS content
            #Failure return: 0|Failure code
            $url = 'http://web.520mqn.com/yhapi.ashx?act=getPhoneCode&token=' . $this->token . '&iid=1083&mobile=' . $id;
            $this->logger->Add("milktea request code".$url, basename(__FILE__)); 
            $response = $this->in_api->get_any($url);
            $this->logger->Add("milktea response code".$response, basename(__FILE__)); 
            $res_contents=explode("|",$response);
            if ($res_contents[0]=='1') {   
                if (count($res_contents)>5) {  
                    $code = $res_contents[1];
                    if ($code) {
                        $result->ResponseCode = 0;
                         #TODO  WHY we set sms content ?
                        $sms = "<#> Your WhatsApp code: $code You can also tap on this link to verify your phone: v.whatsapp.com/$code Don't share this code with others 4sgLq1p5sV6";
                        $sms=$res_contents[2];
                        $result->Result = (object) [
                            'SMS' => $sms,
                            'Code' => str_replace("-", "", $code),
                        ];
                        return $result;
                    }
                }
            } else if($res_contents[0]=='0'){
                if (count($res_contents)>2) {  
                   if($res_contents[1]=='-3'){
                        #  response sms if is waiting ... 
                        $result->Msg = "waiting for sms";
                        return $result;
                   }  
                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }
    public function order_cancelation($id)
    {    
        #cancel order
        #TODO comfirm that this is the working request.
        #http://web.520mqn.com/yhapi.ashx?act=setRel&token=b7c94daad5e3dd71ffca9298976ec0d4_3&iid=1083&mobile=12675108385
        // try {
        //     $response = $this->in_api->get_balance('http://web.520mqn.com/yhapi.ashx?act=setRel&token=' . $this->token. "&iid=1083&mobile=" . $id);  
        // } catch (Exception $e) {
        //     $this->logger->Add($e->getMessage(), basename(__FILE__));
        // }
         return array(-1, 1);
    }
    private function load_Acess()
    { 
        try {
            $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
            $stmt->execute(["milktea"]);
            $res = $stmt->fetchAll();
            if(!$res) return;
            $apiKey = $res[0]['Access_Token'];
            $stmt->closeCursor();
            return $apiKey;
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
    }
    private function load_chinese_name($service_of_api,$country)
    { 
        try {
            $stmt = $this->pdo->prepare('select `country_of_api` from foreignapiservice where service_of_api=? and country=? ');
            $stmt->execute([$service_of_api,$country]);
            $res = $stmt->fetchAll();
            $country_of_api = $res[0]['country_of_api'];
            $stmt->closeCursor();
            return $country_of_api;
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
    }
}
