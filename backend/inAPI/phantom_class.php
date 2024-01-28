<?php

require_once 'general_in_api.php';

class phantom_api
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
            #http://www.phantomunion.com:10023/pickCode-api/push/buyCandy?token=8abee8d14af052cb9476dcab42046689_899&businessCode=10007&quantity=1&country=IR&effectiveTime=10
            #response
            #{"code":"200","data":{"phoneNumber":[{"number":"+989038222706","businessCode":"10007","serialNumber":"1278769636796854272","imsi":"432350457207609"}],"balance":"5899.3"},"message":"success"}
            if ($token) $this->token = $token;
            $url = 'http://www.phantomunion.com:10023/pickCode-api/push/buyCandy?token=' . $this->token . '&businessCode=' . $businessCode . '&quantity=1&country=' . $country . '&effectiveTime=10';
            
           // echo $url."<br>";
            $response = $this->in_api->getnumber($url);
            // print_r($response);
            
            // echo "<br>";
            $this->logger->Add($url, basename(__FILE__),"phantom.log");
            $this->logger->Add("Phatnom response: " . $response, basename(__FILE__),"phantom.log");

            $response = json_decode($response, true);
            if (isset($response["code"])) {


                if ($response["code"] == 200) {
                    
                    $nb_data = $response["data"]["phoneNumber"][0];
                    $number = $nb_data["number"];
                    $number = str_replace("+", "", $nb_data["number"]);
                    #TODO get App dynamic from db
                    $result->ResponseCode = 0;
                    $result->{"Result"} = (object) [
                        'Id' => $nb_data["serialNumber"],
                        'Number' => $number,
                        'App' => "Whatsapp",
                    ];
                    //echo json_encode($result);
                    return $result;
                } 
                //else if (isset($response["message"])) {

                 //   if (strpos($response["message"], "Token") !== false || strpos($response["message"], "token") !== false) {

                  
                       // echo $response;
                        if ($response["code"] == 400){
                          //  echo "update token";
                        //   if (strpos($response["message"], "Token") !== false || strpos($response["message"], "token") !== false) {

                        $check = $this->get_AccessToKen();
                        //$this->logger->Add("check token: " . $check, basename(__FILE__),"phantom.log");

                        if ($check) {
                            $this->load_Acess();
                           //$this->logger->Add("new refresh:" . $this->token, basename(__FILE__),"phantom.log");
                            $this->cnt += 1;
                            if ($this->cnt < 3)
                                $this->getnumber($businessCode, $country, $this->load_Acess());
                        }
                    
                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"phantom_err.log");
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

            //{"code":"200","data":{"verificationCode":[{"businessCode":"10007","serialNumber":"201807201541332611281","vc":"Your Viber code: 905060\nYou can also tap this link to finish your activation:\nhttps://unv.viber.com/a/905060\u0000\u0000\u0000\u0000"}]},"message":"succ"}
            $response = $this->in_api->get_balance('http://www.phantomunion.com:10023/pickCode-api/push/sweetWrapper?token=' . $this->token . '&serialNumber=' . $id);

           // $this->logger->Add($response, basename(__FILE__),"phantom.log");
            $response = json_decode($response, true);
            #{"code":"200","data":{"verificationCode":[{"businessCode":"","serialNumber":"1278798403325980672","vc":""}]},"message":"success"}

            if (isset($response["code"])) {
                if ($response["code"] == 200) {

                    $nb_data = $response["data"]["verificationCode"][0];
                    if (isset($nb_data["vc"])) {
                        if ($nb_data["vc"] != null || $nb_data["vc"] != "") {
                            #TODO try catch for offset
                            try{

                                // $code  =  str_replace("-", "", $nb_data["vc"]);
                                // preg_match_all('!\d+!', $code, $matches);
                                
                                // $code = $matches[0][0];

                                $all_digits = preg_replace('/\D/', '', $nb_data["vc"]);
                                $size = strlen($all_digits);

                                if ($size >= 6) {
                                    $code = substr($all_digits, 0, 6);
                                
                                
                                } else {
                                    $code = "";
                                }
                               



                            }
                            catch (Exception $e) {
                                $this->logger->Add($e->getMessage(), basename(__FILE__),"phantom_err.log");
                                $code = "";
                            }
                            
                            $result->ResponseCode = 0;
                            $result->Result = (object) [
                                'SMS' => $nb_data["vc"],
                                'Code' => $code,
                            ];
                        } else {

                            $result->Msg = "waiting for sms";
                        }

                        return $result;
                    }
                } else {
                    ##TODO check response sms if is waiting ...
                    $result->Msg = "waiting for sms";
                    return $result;
                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"phantom_err.log");
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }

    private function get_AccessToKen()
    {
        #http://www.phantomunion.com:10023/pickCode-api/push/ticket?key=c7054b432f7e4d5c2d6ecae0a1066d5a
        $refresh = $this->get_refreshtoken();
        //$this->logger->Add("refresh token: " . $refresh, basename(__FILE__),"phantom.log");

        if ($refresh) {
            #{"code":"200","data":{"token":"8abee8d14af052cb9476dcab42046689_899"},"message":"success"}
            $response = $this->in_api->get_any('http://www.phantomunion.com:10023/pickCode-api/push/ticket?key=' . $refresh);
            //$this->logger->Add("resssssssssss " . $response, basename(__FILE__),"phantom.log");
            $response = json_decode($response, true);

            if (isset($response["code"])) {
                if ($response["code"] == "200") {
                    if (isset($response["data"])) {
                        if (isset($response["data"]["token"])) {
                            if ($response["data"]["token"]) {
                                //echo "update: ".$response["data"]["token"];
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
        $stmt->execute(["phantom"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Refresh_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }

    private function load_Acess()
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('select `Access_Token` from foreignapi where Name=? ');
        $stmt->execute(["phantom"]);
        $res = $stmt->fetchAll();
        $apiKey = $res[0]['Access_Token'];
        $stmt->closeCursor();
        return $apiKey;
    }
}
