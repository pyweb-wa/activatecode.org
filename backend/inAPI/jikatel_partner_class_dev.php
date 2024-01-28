<?php

class jikatel_partner
{
    public function __construct()
    {
        include 'config.php';
        include_once "mylogger.php";

        $this->logger = new MyLogger();
        $this->in_api = new IN_API();
        $this->pdo = $pdo;
        $this->cnt = 0;
    }
    public $token;

    public function checkapi($api_key = null) // function to get admin token and validate user token 
    {
        if ($api_key == null) {
            return true;
        }

        $sql = "SELECT ad.token as admintoken FROM tokens t inner join users u on t.userID = u.Id inner join cms_users ad on u.admin_id = ad.Id_User where t.access_Token =? and u.Is_Activated = 1 and u.is_deleted=0;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$api_key]);
        $res = $stmt->fetchAll();
        if ($res > 0) {
            return $res[0]['admintoken'];
        } else {
            return false;
        }

    }

    public function getnumber($service, $country, $api_key)
    {

        try {
            
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
            
            $key = $this->checkapi($api_key);
            if ( $key == false) {
                $result->ResponseCode = 1;
                $result->Msg = "The current business is busy, no mobile number is available, please try again later";
                return $result;
            }
           
         
            $country = $this->load_country($country);
        
            $obj = array('action' => 'GET_NUMBER', 'sum' => 10, 'country' => $country, 'operator' => 'Any', 'service' => $service);
            $response = $this->request($obj,$key); 
            $response = json_decode($response, true);
            if (isset($response["status"])) {
                if ($response["status"] == "SUCCESS") {
                   
                    if (isset($response['number'], $response['activationId'])) {
                        $number = $response["number"];
                        $number = str_replace("+", "", $number);
                        $result->ResponseCode = 0;
                        $result->{"Result"} = (object) [
                            'Id' => $response['activationId'],
                            'Number' => $number,
                            'App' => $service,
                        ];
                       
                        return $result;
                    }

                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"jikatel_error.log");

        }
        $result->ResponseCode = 1;
        $result->Msg = "The current business is busy, no mobile number is available, please try again later";
        return $result;
    }

    public function getverificationcode($id,$api_key)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];


            $key = $this->checkapi($api_key);
            if ( $key == false) {
                $result->ResponseCode = 1;
                $result->Msg = "The current business is busy, no mobile number is available, please try again later";
                return $result;
            }
           
            $sql = "SELECT jikatel_partnerResults.id, requests_log.Phone_Nb, jikatel_partnerResults.sms, jikatel_partnerResults.code
            FROM requests_log
            INNER JOIN jikatel_partnerResults ON requests_log.Phone_Nb = jikatel_partnerResults.phone_number
            INNER JOIN foreignapiservice ON foreignapiservice.Id_Service_Api = requests_log.service
            WHERE requests_log.srv_req_id = ?
            AND jikatel_partnerResults.taked = 0
            AND jikatel_partnerResults.application = foreignapiservice.Name
            ORDER BY jikatel_partnerResults.timestamp DESC
            LIMIT 1;";

            // $sql = "SELECT `id`,`Phone_Nb`,`sms`,`jikatel_partnerResults`.`code` as code FROM `requests_log`,`jikatel_partnerResults`,`foreignapiservice` WHERE requests_log.srv_req_id = ? and `requests_log`.`Phone_Nb` = `jikatel_partnerResults`.`phone_number` and `jikatel_partnerResults`.`taked` = 0 and `foreignapiservice`.`Id_Service_Api` = `requests_log`.`service` and `jikatel_partnerResults`.`application` = `foreignapiservice`.`Name` ORDER BY `jikatel_partnerResults`.`timestamp` DESC limit 1;";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            if (sizeof($res) > 0) {
                $result->ResponseCode = 0;
                $result->Result = (object) [
                    'SMS' => $res[0]["sms"],
                    'Code' => $res[0]["code"],
                ];
                $stmt2 = $this->pdo->prepare('UPDATE `jikatel_partnerResults` SET `taked` = 1, `taked_time` = NOW() WHERE `id` = ?');
                $stmt2->execute([$res[0]['id']]);
            } else {
                $result->Msg = "waiting for sms";
            }
            return $result;
        } catch (Exception $e) {
           // echo 'Caught exception: ', $e->getMessage(), "\n";
            $this->logger->Add($e->getMessage(), basename(__FILE__),"jikatel_error.log");
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }
    public function get_avialableadmin($available,$admin_key){
        try{
        $result = (object) [
            'ResponseCode' => 1,
            'Msg' => 'OK',
            'Result' => null,
        ];

        if ( $admin_key == false) {
            $result->ResponseCode = 1;
            $result->Msg = "The current business is busy, no mobile number is available, please try again later,no admin ";
            return $result;
        }

       
        $obj = array('action' => 'GET_SERVICES');
       
        $response = $this->request($obj,$admin_key);
        $data = json_decode($response, true);

        $countryStmt = $this->pdo->prepare("SELECT country_char, country FROM countryList");
        $appStmt = $this->pdo->prepare("SELECT id , application  FROM application_code");
        $countryStmt->execute();
        $appStmt->execute();
        $countryMap = $countryStmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $appMap = $appStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        
    $countryList = $data['countryList'];
    $applications = array_values($appMap);

    $res = array();
    foreach ($countryList as $countryData) {
        $country = $countryData['country'];
        $operatorMap = $countryData['operatorMap'];
        foreach ($operatorMap as $operator => $appData) {
            foreach ($appData as $appCode => $count) {
                // Step 2: Use the extracted information to generate the desired output.
                $app = $applications[array_search($appCode, array_keys($appMap))];
                $countryCode = array_search($country, $countryMap);
                $res[] = array(
                    'count' => $count,
                    'application' => $app,
                    'country_code' => $countryCode,
                    'app_code' => $appCode,
                );
            }
        }
    }

    
        $result->ResponseCode = 0;
        $result->Result = $res;
        //echo json_encode($result);
        //die();
        return $result;
    } catch (Exception $e) {
            
        $this->logger->Add($e->getMessage(), basename(__FILE__),"jikatel_error.log");

       
    }
    $result->ResponseCode = 1;
    $result->Msg = "The current business is busy, no mobile number is available, please try again later";
    return $result;


    }
    public function get_avialable($available,$api_key)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

            
            $key = $this->checkapi($api_key);
            
            if ( $key == false) {
                $result->ResponseCode = 1;
                $result->Msg = "The current business is busy, no mobile number is available, please try again later,no admin ";
                return $result;
            }

           
            $obj = array('action' => 'GET_SERVICES');
           
            $response = $this->request($obj,$key);
            $data = json_decode($response, true);
            
            // Convert Linood server format to myserver format
            // $countryStmt = $this->pdo->prepare("SELECT country_char, country FROM countryList");
            // $appStmt = $this->pdo->prepare("SELECT id , application  FROM application_code");
            // $countryStmt->execute();
            // $appStmt->execute();
            // $countryMap = $countryStmt->fetchAll(PDO::FETCH_KEY_PAIR);
            // $appMap = $appStmt->fetchAll(PDO::FETCH_KEY_PAIR);
            // $res = array();
            // foreach ($data['countryList'] as $countryData) {
            //     $countryCode = array_search($countryData['country'], $countryMap);
            //     foreach ($countryData['operatorMap'] as $appCode => $countMap) {
            //         $appCode = key((array) $countMap);
            //         $application = $appMap[$appCode];
            //         foreach ($countMap as $count) {
            //             $res[] = array(
            //                 'count' => $count,
            //                 'application' => $application,
            //                 'country_code' => $countryCode,
            //                 'app_code' => $appCode,
            //             );
            //         }
            //     }
            // }

            
            $countryStmt = $this->pdo->prepare("SELECT country_char, country FROM countryList");
            $appStmt = $this->pdo->prepare("SELECT id , application  FROM application_code");
            $countryStmt->execute();
            $appStmt->execute();
            $countryMap = $countryStmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $appMap = $appStmt->fetchAll(PDO::FETCH_KEY_PAIR);

            
        $countryList = $data['countryList'];
        $applications = array_values($appMap);

        $res = array();
        foreach ($countryList as $countryData) {
            $country = $countryData['country'];
            $operatorMap = $countryData['operatorMap'];
            foreach ($operatorMap as $operator => $appData) {
                foreach ($appData as $appCode => $count) {
                    // Step 2: Use the extracted information to generate the desired output.
                    $app = $applications[array_search($appCode, array_keys($appMap))];
                    $countryCode = array_search($country, $countryMap);
                    $res[] = array(
                        'count' => $count,
                        'application' => $app,
                        'country_code' => $countryCode,
                        'app_code' => $appCode,
                    );
                }
            }
        }

        
            $result->ResponseCode = 0;
            $result->Result = $res;
            //echo json_encode($result);
            //die();
            return $result;


        } catch (Exception $e) {
            
            $this->logger->Add($e->getMessage(), basename(__FILE__),"jikatel_error.log");

        }
        $result->ResponseCode = 1;
        $result->Msg = "The current business is busy, no mobile number is available, please try again later";
        return $result;
    }

    private function request($obj,$api=null)
    {
        $key = "8c203af7fe0f1496d5dfe5567a63301ab49fe3f9";
        if($api){
            $key = $api;
        }
       

        $obj['key'] = $key;
        // if($api != null)
        // $obj['key'] = $api;

        $json = json_encode($obj);
        $ch = curl_init();
        $url = 'https://45-79-8-214.ip.linodeusercontent.com/ruapi/ruapi_pro.php';
        // Set the URL and other curl options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        // Execute the request
        $response = curl_exec($ch);
        $this->logger->Add($url . " req: ".$json." resp:".$response, basename(__FILE__),"jikatel.log");
       
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
        } else {
            return $response;
        }
        // Close the curl handle
        curl_close($ch);
        return null;

    }

    private function load_country($country)
    {
        #TODO get by id not by name
        $stmt = $this->pdo->prepare('SELECT  `country` FROM `countryList` where `country_char` = ?');
        $stmt->execute([$country]);
        $res = $stmt->fetchAll();
        $country = $res[0]['country'];
        $stmt->closeCursor();
        return $country;
    }
}
