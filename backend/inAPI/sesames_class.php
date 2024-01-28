<?php

require_once 'general_in_api.php';

class sesames_api
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
            #http://www.sesames.online/outsideapi/gpmni?token=42ee91c075b75ce25812f412da15f25a&business_code=10000&country=IN
            #response
            #{"code":"200","message":"success","data":{"phone_number_group":[{"phone_number":"919042563753","order_id":"20200704201623328571732898217","country":"IN"}],"balance":748.26}}
            if ($token) {
                $this->token = $token;
            }

            $url = 'http://www.sesames.online/outsideapi/gpmni?token=' . $this->token . '&business_code=' . $businessCode . '&country=' . $country;
            $this->logger->Add("Raw request: " . $url, basename(__FILE__));

            $response = $this->in_api->getnumber($url);
            $this->logger->Add("Raw response: " . $response, basename(__FILE__));

            //$this->logger->Add($url, basename(__FILE__));
            $this->logger->Add("sesames response: " . $response, basename(__FILE__));

            $response = json_decode($response, true);
            if (isset($response["code"])) {
                if ($response["code"] == 200) {
                    $nb_data = $response["data"]["phone_number_group"][0];
                    $number = $nb_data["phone_number"];
                    $number = str_replace("+", "", $nb_data["phone_number"]);
                    #TODO get App dynamic from db
                    $result->ResponseCode = 0;
                    $result->Result = (object) [
                        'Id' => $nb_data["order_id"],
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

            //
            $url = 'http://www.sesames.online/outsideapi/gvc?token=' . $this->token . '&order_id=' . $id;
            $this->logger->Add("seseams getcode request: ". $url, basename(__FILE__));
            
            $response = $this->in_api->get_balance( $url);

            $this->logger->Add($response, basename(__FILE__));
            $response = json_decode($response, true);
            #{"code":"200","message":"success","data":{"business_price":0,"phone_number":"","order_id":"","verification_code":""}}

            if (isset($response["code"])) {
                if ($response["code"] == 200) {

                    $code = $response["data"]["verification_code"];
                    if ($code) {

                        #TODO set SMS for whatsapp
                        $result->ResponseCode = 0;
                        
                        $sms = "<#> Your WhatsApp code: $code You can also tap on this link to verify your phone: v.whatsapp.com/$code Don't share this code with others 4sgLq1p5sV6";
                        $result->Result = (object) [
                            'SMS' => $sms,
                            'Code' => $code,

                        ];
                    } else {

                        $result->Msg = "waiting for sms";
                    }

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

    private function load_Acess()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["Sesames"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
