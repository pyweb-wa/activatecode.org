<?php

require_once 'general_in_api.php';

class social_hat
{
    public function __construct()
    {
        #include 'config.php';
        include_once "mylogger.php";

        $this->logger = new MyLogger();
        $this->in_api = new IN_API();
       # $this->pdo = $pdo;
        $this->token = $this->load_Acess(); //read token from db
        $this->cnt = 0;
    }
    public $token;

    ## Admin

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
            #http://social-hat.com/do/get?token=Njc3NTc3OTMtNTQwM&geo=CN&max_price=0
            #response
            #{"success":true,"data":{"geo":"RU","pre":7,"number":"388760127"},"msg":"success","code":0}
            if ($token) $this->token = $token;
            $url = 'http://social-hat.com/do/get?token=' . $this->token . '&geo=' . $country . '&max_price=0';
            $this->logger->Add("social_hat: " . $url, basename(__FILE__));
            
            $response = $this->in_api->getnumber($url);
            //$this->logger->Add($url, basename(__FILE__));
            $this->logger->Add("social_hat: " . $response, basename(__FILE__));
            $response = json_decode($response, true);
            if (isset($response["success"])) {


                if ($response["success"] == true) {
                    $number = $response["data"]["pre"].$response["data"]["number"];
                    #TODO get App dynamic from db
                    $result->ResponseCode = 0;
                    $result->{"Result"} = (object) [
                        'Id' =>$response["data"]["number"],
                        'Number' => $number,
                        'App' => "Whatsapp",
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

    public function getverificationcode($id,$country)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

            // http://social-hat.com/do/check?token=Njc3NTc3OTMtNTQwM&geo=CN&number=15834565604
            $url = 'http://social-hat.com/do/check?token=' . $this->token . '&number=' . $id."&geo=".$country;
           // echo $url."<br>";
            $this->logger->Add("social-hat getcode request: ". $url, basename(__FILE__));
			#echo $url;	
            $response = $this->in_api->get_balance( $url);

            $this->logger->Add($response, basename(__FILE__));
            $response = json_decode($response, true);
            #

            if (isset($response["success"])) {


                if ($response["success"] == true) {

                    $code = $response["data"]["code"];
                    if ($code) {

                        #TODO set SMS for whatsapp
                        $result->ResponseCode = 0;
                        
                        $sms = "<#> Your WhatsApp code: $code You can also tap on this link to verify your phone: v.whatsapp.com/$code Don't share this code with others";
                        $result->Result = (object) [
                            'SMS' => $sms,
                            'Code' => $code,

                        ];
                    } else {
						
						
                        $result->Msg = "waiting for sms";
                    }

                    return $result;
                }
				else {
						
                        $result->Msg = "Number Status Error,Timeout";
						return $result;
                    }

            }
			 else {

                        $result->Msg = "waiting for sms";
						return $result;
                    }
        } catch (Exception $e) {
		
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }

    public function gettotalnumbers()
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

            $url = 'http://social-hat.com/do/stock?token=' . $this->token;
         
            $this->logger->Add("social-hat gettotalnumbers request: ". $url, basename(__FILE__));
            
            $response = $this->in_api->get_balance( $url);

            $this->logger->Add($response, basename(__FILE__));
            $response = json_decode($response, true);
            #

            if (isset($response["success"])) {


                if ($response["success"] == true) {

                    $data = $response["data"];
                    if ($data) {

                        $result->ResponseCode = 0;
                        
                        $result->Result = $data;
                    } else {

                        $result->Msg = "data can't be load ";
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
        include 'config.php';
        
        #TODO get by id not by name
       # $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt = $pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["social-hat"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        $pdo = null;
        return $apiKey;
    }
}
