<?php

require_once 'general_in_api.php';

class opapi_api
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
            #request
            # http://www2.sms-5g.com/out/ext_api/getMobile?name=wa0001&pwd=123456&pid=313&cuy=cn&num=1&noblack=0
            # reaponse
            #{"code":200,"msg":"Success","data":"+8613934959256"} 
            #{"code":200,"msg":"Success","data":"+8618567633231"}
            if ($token) $this->token = $token;
            $url = 'http://www2.sms-5g.com/out/ext_api/getMobile?' . $this->token . '&num=1&cuy=' . strtolower($country) . '&noblack=0';
            $this->logger->Add("opapi request: " . $url, basename(__FILE__));

            $response = $this->in_api->getnumber($url);

            $this->logger->Add("opapi response: " . $response, basename(__FILE__));

            $response = json_decode($response, true);
            if (isset($response["code"])) {


                if ($response["code"] == 200) {
                    $nb_data = $response["data"];
                    if (strlen($nb_data)>6) {

                        $number = str_replace("+", "", $nb_data);

                        #TODO get App dynamic from db
                        $result->ResponseCode = 0;
                        $result->Result = (object) [
                            'Id' => $nb_data,
                            'Number' => $number,
                            'App' => "WhatsApp",
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

            //request
            # http://www2.sms-5g.com/out/ext_api/getMsg?name=wa0001&pwd=123456&pid=313&pn=+8613934959256&serial=2
            # response
            # {"code":200,"msg":"Success","data":"637-322"}
            $url = 'http://www2.sms-5g.com/out/ext_api/getMsg?' . $this->token . '&pn=' . $id;
            $this->logger->Add("opapi request code".$url, basename(__FILE__));

            $response = $this->in_api->get_balance($url);
            $this->logger->Add("opapi response code".$response, basename(__FILE__));
            
            $response = json_decode($response, true);

            #
            if (isset($response["code"])) {
                if ($response["code"] == 200) {

                    $nb_data = $response["data"];
                    #TODO  set whatsapp sms  content
                    $code = $response["data"];
                    if ($code) {
                        $result->ResponseCode = 0;
                        $sms = "<#> Your WhatsApp code: $code You can also tap on this link to verify your phone: v.whatsapp.com/$code Don't share this code with others 4sgLq1p5sV6";
                        $result->Result = (object) [
                            'SMS' => $sms,
                            'Code' => str_replace("-", "", $code),
                        ];
                        return $result;
                    }
                } else {
                    ##TODO check response sms if is waiting ...
                    $result->Msg = "waiting for sms";
                    return $result;
                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }
    #TODO add cancelation 
    private function load_Acess()
    {
        #TODO get by id not by name
        try {
            $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
            $stmt->execute(["opapi.lemon"]);
            $res = $stmt->fetchAll();
            $apiKey = $res[0]['Access_Token'];
            $stmt->closeCursor();
            return $apiKey;
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
    }
}
