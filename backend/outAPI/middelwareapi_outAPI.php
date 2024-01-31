<?php

require_once 'inAPI/banana-api_class.php';
//require_once 'inAPI/jikatel_partner_class.php';
//require_once 'inAPI/digitalsim_api_class.php';


class out_api
{
    public function iprint($msg)
    {
        //echo "$msg <br>";
        $this->logger->Add($msg, basename(__FILE__));
    }

    public function __construct()
    {
        include 'config.php';
        include_once "mylogger.php";
        
        $this->logger = new MyLogger();
        $this->pdo = $pdo;
        
        $this->banana_api = new banana_api();
       // $this->jikatel_partner = new jikatel_partner();
       // $this->digitalsim = new digitalsim_api();


        $this->result = (object) [
            'ResponseCode' => 1,
            'Msg' => 'OK',
            'Result' => null,
        ];
    }

    public function check_api_key($api_key, $myflag = 1)
    {
        try {

            if (strlen($api_key) != 64) {
                return false;
            }
            //check validity of token
            //can be replaced by :
            //if (isnull(validity) or validity =0 ) line 6 of check_token function in mysql
            $stmt0 = $this->pdo->prepare('SELECT `valid`  FROM `tokens`    WHERE `access_token` =? limit 1;  ');
            $stmt0->execute([$api_key]);
            $validity = $stmt0->fetch();
            if (isset($validity['valid'])) {
                if ($validity['valid'] != 1) {
                    return false;
                }
            } else {
                return false;
            }


            ///////////////////////////
            // $stmt0 = $this->pdo->prepare("SELECT userID as 'id', is_refunding from tokens , users  where tokens.userID =users.Id and  access_token=?  ");
            // $stmt0->execute([$api_key]);
            // $res = $stmt0->fetch();
            // $uid = $res['id'];
            // $isrefunding = $res['is_refunding'];

            // //echo $isrefunding;
            // if(!$isrefunding){
            //     $stmt = $this->pdo->prepare('UPDATE users set is_refunding=1 where Id=?  ');
            //     $stmt->execute([ $uid]);

            //     $stmt1 = $this->pdo->prepare("SELECT `Id_request` FROM  requests_log  WHERE `TimeStmp` <= DATE_SUB(NOW(), INTERVAL 5 MINUTE) and `Id_user`=?   and  `Status`='pending' limit 1000");
            //     $stmt1->execute([$uid]);
            //     $req_to_refund = $stmt1->fetchAll();

            //     foreach ($req_to_refund as $reqID) {

            //         $stmt = $this->pdo->prepare('call `get_refund`(?,?) ');
            //         $stmt->execute([$reqID['Id_request'], $api_key]);
            //         $res = $stmt->fetchAll();
            //         $stmt->closeCursor();
            //     }
            //     $stmt = $this->pdo->prepare('UPDATE users set is_refunding=0 where Id=?  ');
            //     $stmt->execute([ $uid]);
            // }
            // else{
            //     file_put_contents("/home/pyweb/web/sms-platform/backend/logging/refunds.log", 'user_id: '.$uid.' ==> time:'.date("m-d-Y G:i"). "\n", FILE_APPEND);


            // }
            //////////////////////

            $stmt = $this->pdo->prepare('SELECT `check_token`(?)  as `balance`  ');
            $stmt->execute([$api_key]);
            $balance = $stmt->fetch();
            $result = (object) ['balance' => $balance['balance']];
            if (strpos($balance['balance'], "unvalid token") !== false) {
                return false;
            } else if (strpos($balance['balance'], "zero") !== false) {
                $result = (object) ['balance' => 0.0];
            }

            return $result;
        } catch (Exception $e) {
            $this->logger->Add("check_api_key: " . $e->getMessage(), basename(__FILE__));
            echo ("check_api_key: " . $e->getMessage() . basename(__FILE__));
        }
        return false;
    }

    public function getbalance($api_key)
    {


        return $this->check_api_key($api_key);
    }

    public function getapplist($api_key)
    {
        try {

            if ($this->check_api_key($api_key) == false) {
                return null;
            }
            $stmt = $this->pdo->prepare('call get_applist()  ');
            $stmt->execute();
            $res = $stmt->fetchAll();
            $out = array_values($res);
            return $out;
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return null;
    }

    public function rest_number($api_key)
    {
        try {

            if ($this->check_api_key($api_key) == false) {
                return null;
            }
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

            $stmt = $this->pdo->prepare('select  api_id from  user_allowed_api  where user_id =(select userID FROM tokens WHERE access_Token=?)');
            $stmt->execute([$api_key]);
            $res = $stmt->fetchAll();
            $out = array_values($res);
            if (sizeof($out) == 1) {
                if ($out[0]["api_id"] == 13) {
                    $result = $this->social_hat->gettotalnumbers();
                    return $result;
                }
            }

            $result->Result = "data no available ! try again later !!";
            return $result;
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return null;
    }
    public function get_allowed_services($api_key, $appCode, $country, $carrier)
    { //get user id
        $allowed_list = [];
        try {
            $stmt = $this->pdo->prepare(
                "select  api_id  as 'id' from tokens , user_allowed_api ,foreignapi  where
                `tokens`.`access_Token` =? and userID =user_id and api_id =Id_Api order by priority  "
            );
            $stmt->execute([$api_key]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();

            foreach ($res as $api) {
                array_push($allowed_list, $api);
            }
        } catch (Exception $e) {
            $this->logger->Add("cannot get allowed list !! with token :" . $api_key, basename(__FILE__));
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        //get apis which provide selected services
        $available_apis = [];
        try {
            $query = "";
            $paramslist = [$country, $appCode];
            if ($carrier != "") {
                $query = "SELECT Id_Foreign_Api as 'id'  from foreignapiservice WHERE    `country`=?  and `code`=? and `carrier`=?";
                array_push($paramslist, $carrier);
            } else {
                $query = "SELECT Id_Foreign_Api as 'id'  from foreignapiservice WHERE  `country`=?  and `code`=? ";
            }

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($paramslist);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            foreach ($res as $api) {
                array_push($available_apis, $api);
            }
        } catch (Exception $e) {
            $this->logger->Add("cannot get allowed list !! with token :" . $api_key, basename(__FILE__));
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        $final_api_list = [];
        foreach ($allowed_list as $allowed) {
            if (in_array($allowed, $available_apis)) {
                array_push($final_api_list, $allowed);
            }
        }

        return $final_api_list;
    }
    public function balance($api_key,$amount){

        $sql = "select `users`.`Id` as userId from `users` left JOIN `tokens` on `users`.`Id` = `tokens`.`userID` WHERE `tokens`.`access_Token` = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$api_key]);
        $res = $stmt->fetchAll();
        if (sizeof($res) > 0) {
        $userId = $res[0]['userId'];
        include '/var/www/smsmarket/html/backend/redisconfig.php';
        $lockKey = "lock:$userId";
        $lockExists = $redis->exists($lockKey);
        while($lockExists) {
            $lockExists = $redis->exists($lockKey);
            $log = "[-] ".(string) $userId . "Lock $amount  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
            file_put_contents("/var/www/smsmarket/logging/balance_lock.log", $log, FILE_APPEND);
           usleep(5000);
        }
        $lockAcquired = $redis->set($lockKey, 1, ['nx' => true, 'px' => 10]); // 10 second lock
        if (!$lockAcquired) {
            $log = "[-] ".(string) $userId . "Lock22 $amount  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
        
            file_put_contents("/var/www/smsmarket/logging/balance_lock.log", $log, FILE_APPEND);
            return false;
        }
        $balanceKey = "balance:$userId";
        $currentBalance = $redis->get($balanceKey);
        if ($currentBalance === false || $currentBalance < $amount) {
            $redis->del($lockKey); // Release the lock
            return false;
        }
        // Deduct from balance
        $newBalance = $redis->incrbyfloat($balanceKey, -$amount);
        $redis->del($lockKey);
        return $newBalance; 
    }      
    }

    public function getnumber($api_key, $appCode, $country, $carrier = "", $available = 0, $count = 1)
    {
      
        try {
            if($country != "any"){
            $country = strtoupper($country);
            }

            #### select if $api_key in db and return user id and user balance and  ...
            // if ($this->check_api_key($api_key) == false) {
               
            //     return null;
            // }
            if (strlen($appCode) > 5) {
               
                return null;
            }

            if (strlen($country) > 40) {
              
                return null;
            }

            if (strlen($carrier) > 16) {
              
                return null;
            }
            $result = (object) [
                
            ];
           
            //$this->logger->Add($api_key. $appCode. $country. $carrier , basename(__FILE__));
            //check service existance and balance enough
            $appCode = strtolower($appCode); 
            if ($country == "any") {
                $sql = "SELECT countryList.`country_char` from countryList,user_countries,tokens where countryList.id = `user_countries`.country_id and tokens.userID = `user_countries`.user_id and tokens.access_Token = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$api_key]);
                $row = $stmt->fetchall(PDO::FETCH_ASSOC);
                if ($row) {
                    $countries = array_column($row, 'country_char');
                    if (sizeof($countries) > 1) {
                        shuffle($countries);
                        $country = reset($countries);
                    } else {
                        $country = $countries[0];
                    }
                }
            }
           
           // $allowed_services = [];
            ## TOBE remove
            //if ($country == "") {$country = "JO";}
            // echo $country." " .$appCode;
            $allowed_services = $this->get_allowed_services($api_key, $appCode, $country, $carrier= " ");

            //need to move to redis
            // $allowed_services = [
            //     [
            //         "id" => 17
            //     ]
            // ];

            #TODO important fix bug  SQLSTATE[42000]: Syntax error or access violation: 1172 Result consisted of more than one row ==> script: middelwareapi_outAPI.php  datetime: 07/27/2020 09:16:20 pm
            #echo $api_key, $appCode, $country, $carrier;
            //NOTE kellon nafs l se3er fa hay btemshe ok

            $stmt = $this->pdo->prepare("call `check_serviceAndBalance`(?, ?, ?, ?) ");
            $stmt->execute([$api_key, $appCode, $country, $carrier]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            $res = $res[0];

            if(isset($res['balance'])){
                $balance =  floatval($res['balance']);
                $count = floatval($count);
                $cost = floatval($res['cost']);
                
                if (($balance - $count*$cost) <0){
                    $result = (object) [
                        'ResponseCode' => 2,
                        'Msg' => "You don't have enough balance to get $count numbers",
                        'Result' => "balance: $balance",
                    ];
                    
                    echo json_encode($result);
                    die();
                    
                }
            }
            
            
           
            $flag = $res['flag'];

            if ($flag == "ok") {
                $fapi_id = $res['fapi_id'];
                $serv_id = $res['serv_id'];
                $cost = $res['cost'];
                $balance = $res['balance'];
                # loop  on apis to get number
                # new select query to get service_of_api for each fapi with other params
               // var_dump($allowed_services);die();
                foreach ($allowed_services as $f_apiO) {
                    $f_api = $f_apiO["id"];
                    #get fapi name to compare and service of api
                    
                    $paramslist = [$f_api, $country, $appCode];
                    if ($carrier != "") {
                        $query = "SELECT   `foreignapi`.`Name`, `foreignapiservice`.`service_of_api`, `foreignapiservice`.`Id_Service_Api` ,`foreignapiservice`.`country_of_api`    FROM `foreignapi`,`foreignapiservice`  WHERE `foreignapi`.`is_deleted`=0 and `foreignapiservice`.`is_deleted`=0 and  `foreignapi`.`Id_Api`=? and `country`=?  and `code`=? and `carrier`=? and  Id_Foreign_Api=Id_Api";
                        array_push($paramslist, $carrier);
                    } else {
                        $query = "SELECT   `foreignapi`.`Name`, `foreignapiservice`.`service_of_api`, `foreignapiservice`.`Id_Service_Api` ,`foreignapiservice`.`country_of_api`  FROM `foreignapi`,`foreignapiservice`  WHERE `foreignapi`.`is_deleted`=0 and `foreignapiservice`.`is_deleted`=0 and  `foreignapi`.`Id_Api`=? and `country`=?  and `code`=? and  Id_Foreign_Api=Id_Api";
                    }
                    $stmt5 = $this->pdo->prepare($query);
                    $stmt5->execute($paramslist);
                    $returns = $stmt5->fetchAll();
                    $stmt5->closeCursor();
                   
                    $returns = $returns[0];
                    $f_name = $returns['Name'];
                    $service_of_api = $returns['service_of_api'];
                    $country_of_api = $returns['country_of_api'];
                    $serv_id = $returns['Id_Service_Api'];
                    $result = null;

                    if ($f_name != "banana-api") {
                        if ($available != 0) {
                            $result = (object) [
                                'ResponseCode' => 5,
                                'Msg' => "You don't have access to this request,Please contact your administrator",
                                'Result' => null,
                            ];

                            echo json_encode($result);
                            die();
                        }
                    }
                    switch ($f_name) {

                        case "banana-api":
                            $result = $this->banana_api->getnumber($service_of_api, $country, $available = 0, $count = $count, $api_key);
                            break;    
                        case "jikatel_partner":
                            $result = $this->jikatel_partner->getnumber($service_of_api, $country,$api_key);
                            break;
                        case "digitalsim":
                            require_once 'inAPI/digitalsim_api_class.php';
                            $this->digitalsim = new digitalsim_api();
                            $result = $this->digitalsim->getnumber($service_of_api, $country,$api_key);
                            break;
                        default:
                            break;
                            //continue; // fapi not compatible

                    }
                    if ($result) {
                        
                        if ($result->ResponseCode > 0) { //purchase not OKK
                          
                            continue; //continue to next fapi
                        } else if ($result->Msg == "OK" && $result->Result !== null) //purchase okkkkkkk
                        {
                            $flag = "";
                            $numbers_list = json_encode($result->Result);
                            if (strpos($numbers_list, '[') === false || strpos($numbers_list, ']') === false) {
                                // Add square brackets if not present
                                $numbers_list = '[' . $numbers_list . ']';
                            }
                            ## TODO
                            /// deadLock on updating balance need to Fix
                            $stmt2 = $this->pdo->prepare("call  `purchasePro2`(?,?,?)");
                            $stmt2->execute([$api_key, $serv_id, $numbers_list]);
                            $res = $stmt2->fetchAll();
                            $stmt2->closeCursor();
                            $res = $res[0];                   
                            $res = json_decode($res['result']);
                            //var_dump($res);die();
                            $CountryCode = "";
                            $sql = "select `country_code` from `countryList` WHERE `country_char` =?";
                            $stmt = $this->pdo->prepare($sql);
                            $stmt->execute([$country]);
                            $row = $stmt->fetchall(PDO::FETCH_ASSOC);
                            if (sizeof($row) >= 1) {
                                $CountryCode = $row[0]['country_code'];
                                
                            }
                            if(sizeof($res) == 1){
                                
                                $result->Result =  $res[0];
                                $result->Result->App = $appCode;
                                $result->Result->Cost = $cost;
                                $result->Result->Balance = $balance - $cost*$count;
                                $result->Result->CountryCode = $CountryCode;
                            } else{
                            $result->Result = $res;
                            $result->App = $appCode;
                            $result->Cost = $cost;
                            $result->Balance = $balance - $cost*$count;
                            $result->CountryCode = $CountryCode;

                            }
                             ##########balance_in_redis###########
                             $status = $this->balance($api_key,$cost*$count);
                            ######################
                            #if user came from web update balance in session
                            if (isset($_SESSION['balance'])) {
                                $stmtx = $this->pdo->prepare("SELECT balance from users where Id=?");
                                $stmtx->execute([$_SESSION['id']]);
                                $logs = $stmtx->fetchall();
                                $_SESSION['balance'] = $logs[0]['balance'];
                                
                            }
                            if (isset($result->source)) {
                                // Remove the 'source' key from the array
                                unset($result->source);
                            }
                            ######################
                            //var_dump($result);
                            return $result;
                            #all ok so die()
                            die();
                            
                        } else {
                            //return $result;
                            
                            continue;
                        }
                    }
                } //end of foreach
                #TODO all apis not ok return error to user
                //$this->iprint("all apis error");
            } else {

               
                return (object) ['ResponseCode' => 3, 'Msg' => 'No enough Balance', 'Result' => null];
            }
        } catch (Exception $e) {
            ## error need to back number to list
             ## todo reback all numbers in getbulk need to check if Result is list or object
            
            require '/var/www/smsmarket/html/backend/redisconfig.php';
            $result = json_encode($result);
            $responseArray = json_decode($result, true);

            if (isset($responseArray['Result'])) {
                $result = $responseArray['Result'];
                $redisKey = 'live_'.$responseArray['source'];
              
                if($result){
                    if (is_array($result)) {
                        // 'Result' is an array
                        if (count($result) > 0 && is_array($result[0])) {
                            // 'Result' is a list of objects (array of arrays)
                            foreach ($result as $item) {
                                //echo 'need to delete from db or edit the total because the viwer in box '. $item['Number'];
                                $redis->sadd($redisKey, intval($item['Number']));
                                }
                            }
                        } else {

                            $number = intval($result['Number']);
                           // echo "errrrrrrrr $number   ==>";
                           // die();
                            $redis->sadd($redisKey, $number);  
                        }
                    }
                }
            






           
           
            

            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return (object) [
            'ResponseCode' => 2,
            'Msg' => 'The current business is busy, no mobile number is available, please try again later',
            'Result' => null,
        ];
    }

    


    
    function mergearrays($firstArray,$secondArray){
        if ($firstArray == null){
            return $secondArray;
        }
        $mergedArray = array_merge($firstArray, $secondArray);
    
        $resultArray = array();
    
        foreach ($mergedArray as $obj) {
           
            $key = $obj['application'] . '_' . $obj['country_code'];
            if (!isset($resultArray[$key])) {
                $resultArray[$key] = $obj;
            } else {
                if($obj['status'] == 1){
                    $resultArray[$key] = $obj;  
                }
                
                $resultArray[$key]['count'] += $obj['count'];
            }
        }
    
        $resultArray = array_values($resultArray);
        return $resultArray;
    }
    function stats_numbers($action,$keyadmin){
        $result = [];
        $result = $this->jikatel_partner->stats_numbers($action,$keyadmin);
        
        return $result;

    }

    function get_availableadmin($admin_key, $available = 1){
        $result = [];
            $jikatel_partner = ((array)$this->jikatel_partner->get_avialableadmin($available,$admin_key))["Result"];
            $result = $this->mergearrays( $result,$jikatel_partner);
            if($result == []){
                return (object) [
                    'ResponseCode' => 2,
                    'Msg' => 'The current business is busy, no mobile number is available, please try again later',
                    'Result' => null,
                ];
            }

             // Prepare a SELECT query to fetch the country_code and country_name from your table
             $stmt = $this->pdo->prepare('SELECT country_name,price_out  FROM foreignapiservice WHERE Name = ? AND country = ?');
            
             // Loop through each row in the JSON array
             foreach ($result as &$row) {
            
               // Execute the query
               $stmt->execute([$row['application'],$row['country_code']]);
               // Fetch the result row
             
               $search = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($search ){
                 // Add the country_name to the current row of the JSON array
                 $row['country'] = $search['country_name'];
                 
                 $row['price_out'] = $search['price_out'];
              }else{
                 $row['country'] = null;
                 $row['price_out'] = null;
              }
             }
             return (object) [
                 'ResponseCode' => 0,
                 'Msg' =>'OK',
                 'Result' => $result,
             ];
 
             echo json_encode($result);
             die();
             
        
    }
    function get_available($api_key, $available = 1)
    {
    
        try {
            // $result = (object) [
            //     'ResponseCode' => 1,
            //     'Msg' => 'OK',
            //     'Result' => null,
            // ];
            #### select if $api_key in db and return user id and user balance and  ...
            // if ($this->check_api_key($api_key) == false) {
    
            //     return null;
            // }
    
            $allowed_services = [];
    
            $sql = "SELECT `foreignapi`.`Id_Api` ,`foreignapi`.`Name` FROM `foreignapi`,`users`,`user_allowed_api`,`tokens` where `tokens`.`access_Token` = ? and `tokens`.`userID` = `users`.`Id` and `users`.`Id` = `user_allowed_api`.`user_id` and `foreignapi`.`Id_Api` = `user_allowed_api`.`api_id`";
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$api_key]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            $result = [];
           // var_dump($res);die();
            if (sizeof($res) > 0) {
                // var_dump($res);
                foreach ($res as $api) {
                    $result = $this->banana_api->get_avialable($available);
                    include 'redisconfig.php';
                    if($redis){
                        $key = "get_available:$api_key";
                        $redis->setex($key, 180, json_encode($result));
                       
                    }
                   
                    echo json_encode($result);
                    die();
                    break;
                    $api_name = $api['Name'];
                    if ("banana-api" == $api_name) {
                        
                        $banana = ((array)$this->banana_api->get_avialable($available))["Result"];  
                       
                        $result = $this->mergearrays( $result,$banana);                   
                       
                       
                    }
                    else if ("jikatel_partner" == $api_name) {

                        $jikatel_partner = ((array)$this->jikatel_partner->get_avialable($available,$api_key))["Result"];
                        if($result)
                        {$result = $this->mergearrays( $result,$banana);}                    
                       
                        $result = $this->mergearrays( $result,$jikatel_partner);
                    
                    }
                   else if ("digitalsim" == $api_name) {

                    require_once 'inAPI/digitalsim_api_class.php';
                    $this->digitalsim = new digitalsim_api();
                        $jikatel_partner = ((array)$this->digitalsim->get_avialable($available,$api_key))["Result"];  
                       $result = $this->mergearrays( $result,$jikatel_partner);
                    
                    }

                    
                }
              
                if($result == []){
                    return (object) [
                        'ResponseCode' => 2,
                        'Msg' => 'The current business is busy, no mobile number is available, please try again later',
                        'Result' => null,
                    ];
                }

                // Prepare a SELECT query to fetch the country_code and country_name from your table
                $stmt = $this->pdo->prepare('SELECT country_name,price_out  FROM foreignapiservice WHERE Name = ? AND country = ?');
            
                // Loop through each row in the JSON array
                foreach ($result as &$row) {
               
                  // Execute the query
                  $stmt->execute([$row['application'],$row['country_code']]);
                  // Fetch the result row
                
                  $search = $stmt->fetch(PDO::FETCH_ASSOC);
                 
                 if($search ){
                    // Add the country_name to the current row of the JSON array
                    $row['country'] = $search['country_name'];
                    
                    $row['price_out'] = $search['price_out'];
                 }else{
                    $row['country'] = null;
                    $row['price_out'] = null;
                 }
                }
                return (object) [
                    'ResponseCode' => 0,
                    'Msg' =>'OK',
                    'Result' => $result,
                ];
    
                echo json_encode($result);
                die();
               
    
            }
        } catch (Exception $e) {
            //echo $e->getMessage();
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return (object) [
            'ResponseCode' => 2,
            'Msg' => 'The current business is busy, no mobile number is available, please try again later',
            'Result' => null,
        ];
    }


    public function get_verificationcode($api_key, $id)
    {

        try {
            if (strlen($id) > 50) {
                return (object) ['ResponseCode' => 99, 'Msg' => 'Too long Argument', 'Result' => null];
            }

            // if ($this->check_api_key($api_key, 0) == false) {

            //     return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Wrong Api key 00', 'Result' => null];
            // }
            #ask api for code by id
            #if code ok insert to req_log
            $id = (int) $id;
            #TODO return phone number with get_code then add number to Result
            $stmt = $this->pdo->prepare('call `get_code`(?,?)');
            $stmt->execute([$id, $api_key]);
            $res = $stmt->fetchAll();
            //var_dump($res[0]);
            $stmt->closeCursor();
            $code = $res[0];
            if ($code['allow'] == 0) //this user own this request or not
            {
                return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Wrong request id', 'Result' => null];
            } else if ($code['code'] !== null) {

                $_result = (object) [
                    'ResponseCode' => 0,
                    'Msg' => 'OK',
                    'Result' => (object) ['Code' => $code['code'], 'SMS' => $code['sms_content']],
                ];

                return $_result;
                // return json_decode('{"code":' . $code['code'] . ' ,"sms_content":"' . $sms_content . '"}');
            } else {
                //Code is null and owner ask for it we need to ask th api and response to user
                # parse response of api

                $srv_req_id = $code['srv_req_id'];
                $foreign_id = $code['foreign_id'];
                # 1 ==>simcode
                // $this->logger->Add($code, basename(__FILE__));


                switch ($foreign_id) {
                    // case 1:
                    //     $result = $this->simcode_api->simcode_getverificationcode($srv_req_id);
                    //     break;
                    // case 10:
                    //     $result = $this->phantom_api->getverificationcode($srv_req_id);
                    //     break;
                    // case 11:
                    //     $result = $this->sesames_api->getverificationcode($srv_req_id);
                    //     break;
                    // case 12:
                    //     $result = $this->opapi_api->getverificationcode($srv_req_id);
                    //     break;
                    // case 13:
                    //     $stmtx = $this->pdo->prepare("SELECT Phone_Nb , country  FROM `requests_log` ,foreignapiservice  WHERE requests_log.Id_request=? and requests_log.service=foreignapiservice.Id_Service_Api");
                    //     $stmtx->execute([$id]);
                    //     $country = $stmtx->fetchall();
                    //     $country = $country[0]['country'];
                    //     $result = $this->social_hat->getverificationcode($srv_req_id, $country);
                    //     break;
                    // case 14:
                    //     $stmtx = $this->pdo->prepare("SELECT Phone_Nb , country  FROM `requests_log` ,foreignapiservice  WHERE requests_log.Id_request=? and requests_log.service=foreignapiservice.Id_Service_Api");
                    //     $stmtx->execute([$id]);
                    //     $country = $stmtx->fetchall();
                    //     $country = $country[0]['country'];
                    //     $result = $this->milktea->getverificationcode($srv_req_id, $country);
                    //     break;
                    case 17:
                        $result = $this->banana_api->getverificationcode($id);
                        break;
                    // case 18:
                    //     $result = $this->hxbama_api->getverificationcode($srv_req_id);
                    //     break;
                    // case 19:
                    //     $result = $this->gsimonline_api->getverificationcode($srv_req_id);
                    //     break;
                    // case 20:
                    //     $result = $this->smsonme_api->getverificationcode($srv_req_id);
                    //     break;
                    // case 21:
                    //     $result = $this->manualuser_api->getverificationcode($id);
                    //     break;
                    // case 22:
                    //     $result = $this->vak_sms_api->getverificationcode($srv_req_id);
                        // break;
                    case 23:
                        $result = $this->jikatel_partner->getverificationcode($srv_req_id,$api_key);
                        break;
                    case 24:
                        require_once 'inAPI/digitalsim_api_class.php';
                        $this->digitalsim = new digitalsim_api();
                        $result = $this->digitalsim->getverificationcode($srv_req_id,$api_key);
                        break;
                    default:
                        $result = "";
                        break;
                }


                if ($result) {

                    if ($result->ResponseCode == 0) //getcode okkkkkkk
                    {

                        if ($result->Result->Code !== null) {
                            //update to db
                            // $this->logger->Add("updateee...", basename(__FILE__));
                            // echo "<br> Finished ". $result->Result->Code." ". $result->Result->SMS." ". $id."<br>";
                            $stmt = $this->pdo->prepare('UPDATE requests_log set `Status`=? ,`SMSCode`=?  ,`sms_content`=? where Id_request=?  ');
                            $stmt->execute(["Finished", $result->Result->Code, $result->Result->SMS, $id]);
                            // echo "done1 <br>";
                            // echo "Finished ". $result->Result->Code." ". $result->Result->SMS." ". $id."<br>";
                            return $result;
                        }
                    }
                    // else if (){}
                    // echo "done2";
                    return $result;
                }
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Unkown error ,', 'Result' => null];
    }


    public function worng_code($api_key, $id)
    {
        // echo 1;
        try {
            if (strlen($id) > 50) {
                return (object) ['ResponseCode' => 99, 'Msg' => 'Too long Argument', 'Result' => null];
            }

            if ($this->check_api_key($api_key, 0) == false) {

                return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Wrong Api key 00', 'Result' => null];
            }
            #ask api for code by id
            #if code ok insert to req_log
            $id = (int) $id;
            #TODO return phone number with get_code then add number to Result
            $stmt = $this->pdo->prepare('call `get_code`(?,?)');
            $stmt->execute([$id, $api_key]);
            $res = $stmt->fetchAll();
            //var_dump($res[0]);
            $stmt->closeCursor();
            $code = $res[0];
            // var_dump($code);
            if ($code['allow'] == 0) //this user own this request or not
            {
                return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Wrong request id', 'Result' => null];
            } else {
                //Code is null and owner ask for it we need to ask th api and response to user
                # parse response of api
                $_result = (object) [
                    'ResponseCode' => 1,
                    'Msg' => 'error',
                    'Result' => "",
                ];


                $srv_req_id = $code['srv_req_id'];
                $foreign_id = $code['foreign_id'];
                # 1 ==>simcode
                // $this->logger->Add($code, basename(__FILE__));

                $result = "";
                switch ($foreign_id) {
                        // case 1:
                        //     $result = $this->simcode_api->simcode_getverificationcode($srv_req_id);
                        //     break;
                        // case 10:
                        //     $result = $this->phantom_api->getverificationcode($srv_req_id);
                        //     break;
                        // case 11:
                        //     $result = $this->sesames_api->getverificationcode($srv_req_id);
                        //     break;
                        // case 12:
                        //     $result = $this->opapi_api->getverificationcode($srv_req_id);
                        //     break;
                        // case 13:
                        //     $stmtx = $this->pdo->prepare("SELECT Phone_Nb , country  FROM `requests_log` ,foreignapiservice  WHERE requests_log.Id_request=? and requests_log.service=foreignapiservice.Id_Service_Api");
                        //     $stmtx->execute([$id]);
                        //     $country = $stmtx->fetchall();
                        //     $country = $country[0]['country'];
                        //     $result = $this->social_hat->getverificationcode($srv_req_id, $country);
                        //     break;
                        // case 14:
                        //     $stmtx = $this->pdo->prepare("SELECT Phone_Nb , country  FROM `requests_log` ,foreignapiservice  WHERE requests_log.Id_request=? and requests_log.service=foreignapiservice.Id_Service_Api");
                        //     $stmtx->execute([$id]);
                        //     $country = $stmtx->fetchall();
                        //     $country = $country[0]['country'];
                        //     $result = $this->milktea->getverificationcode($srv_req_id, $country);
                        //     break;
                        // case 17:
                        //     $result = $this->banana_api->getverificationcode($id);
                        //     break;
                        // case 18:
                        //     $result = $this->hxbama_api->getverificationcode($srv_req_id);
                        //     break;
                        // case 19:
                        //     $result = $this->gsimonline_api->getverificationcode($srv_req_id);
                        //     break;
                        // case 20:
                        //     $result = $this->smsonme_api->getverificationcode($srv_req_id);
                        //     break;
                    // case 21:
                    //     $result = $this->manualuser_api->worng_code($id);
                    //     break;

                    default:
                     
                        break;
                }




                return $result;
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Unkown error ,', 'Result' => null];
    }
 
    public function order_cancelation($api_key, $id)
    {

        // try {
        # order_cancelation
        //Check  $id if return to $api_key
        // check foreign  api type
        // if type is simcode then call simcode ordercancelation function
        if (strlen($id) > 10) {
            return (object) ['ResponseCode' => 1, 'Msg' => 'Too long Argument', 'Result' => null];
        }
        $balance = $this->check_api_key($api_key);
        if ($balance == false) {
            return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Wrong Api key', 'Result' => null];
        }
        // var_dump($balance);
        // die();
        $stmt = $this->pdo->prepare(" CALL `check_req_owner_ret_fapi`(?, ?);   ");
        $stmt->execute([$id, $api_key]);
        $res = $stmt->fetchAll();
        $stmt->closeCursor();
        $res = $res[0];
        $flag = $res['allow'];
        if ($flag == 1) {
            if ($res["Status"] == "Expired") {
                return (object) ['flag' => "OK", 'refund' => 0.00, 'Msg' => 'Already expired before'];
            }
            $refund_amount = 0.0;
            $srv_req_id = $res['srv_req_id'];
            if ($res['fapi'] == 1) { # simcode id
                //$ref = $this->simcode_api->simcode_order_cancelation($srv_req_id);
                //$refund_amount = $ref[0];
                //TODO ask khodor for what to do with simcode
                $refund_amount = 1;
            } else if ($res['fapi'] == 10) # phantom id
            {
                //$ref = $this->simcode_api->simcode_order_cancelation($srv_req_id);
                $refund_amount = 1;
            } else if ($res['fapi'] == 11) # seasames id
            {
                //$ref = $this->simcode_api->simcode_order_cancelation($srv_req_id);
                $refund_amount = 1;
            } else if ($res['fapi'] == 12) # opapi id
            {
                //$ref = $this->simcode_api->simcode_order_cancelation($srv_req_id);
                $refund_amount = 1;
            } else if ($res['fapi'] == 13) # social-hat id
            {
                $refund_amount = 1;
            } else if ($res['fapi'] == 14) # milktea id
            {
                $refund_amount = 1;
            } else if ($res['fapi'] == 23) # JKATEL id
            {
                $refund_amount = 1;
            } else {
                return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Cannot Cancel request', 'Result' => null];
            }
            if ((float) $refund_amount > 0.00) //getcode okkkkkkk
            {
                $stmt = $this->pdo->prepare('call `get_refund`(?,?) ');
                $stmt->execute([$id, $api_key]);
                $res = $stmt->fetchAll();
                $stmt->closeCursor();
                $res = $res[0];
                $balance = $this->check_api_key($api_key);
                return (object) ['flag' => $res["flag"], 'refund' => $res["refund"], 'balance' => $balance->balance, 'Msg' => $res["Msg"]];
            } else if ((float) $refund_amount == 0.00) {
                return (object) ['flag' => "OK", 'refund' => 0.00, 'Msg' => ''];
            } else {
                return (object) ['flag' => "error", 'refund' => 0.00, 'Msg' => 'wrong input data'];
            }
        } else {
            return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Wrong request id', 'Result' => null];
        }
        // } catch (Exception $e) {
        //     $this->logger->Add($e->getMessage(), basename(__FILE__));
        // }
        return (object) ['ResponseCode' => 1, 'Msg' => 'ERROR Unkown Error', 'Result' => null];
    }

    public function get_channels($api_key, $country, $quantity, $first = 1)
    {
        try {

            #### select if $api_key in db and return user id and user balance and  ...
            if ($this->check_api_key($api_key) == false) {
              
                return null;
            }

            if (strlen($country) > 40) {
              
                return null;
            }
            $quantity = intval($quantity);
            if ($quantity == null) {
                return null;
            }
            if ($quantity < 1) {
              
                return (object) ['ResponseCode' => 5, 'Msg' => 'Minimun Count is 1', 'Result' => null];
            }
            if ($quantity > 2000) {
              
                return (object) ['ResponseCode' => 5, 'Msg' => 'maximum Count is 2000', 'Result' => null];
            }

            //$this->logger->Add($api_key. $appCode. $country. $carrier , basename(__FILE__));
            //check service existance and balance enough
            //NOTE kellon nafs l se3er fa hay btemshe ok
            //TODO need to hide channel service from app list for end user
            $stmt = $this->pdo->prepare("call `check_Balance_with_count`(?, ?)  ");
            $stmt->execute([$api_key, $quantity]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            //var_dump($res);
            $res = $res[0];
            $flag = $res['flag'];
            //user can buy this service -->balance ok
           
            if ($flag == "ok") {
               
                $cost = floatval($res['cost']);
                $balance = floatval($res['balance']);

                # loop  on apis to get number
                # new select query to get service_of_api for each fapi with other params
                if ($this->is_channels_allowed($api_key) == 0) {
                    
                    return (object) ['ResponseCode' => 4, 'Msg' => 'User Not allowed to use this function', 'Result' => null];
                } else {

                    //id +timestamp used to identify callbacks
                    $id = substr($api_key, 0, 8);
                    $result = $this->oldchannels->getchannels($country, $id, $quantity, $first);
                    //  var_dump($result);
                    //return;
                    // return (json_decode($result,true));

                    if ($result) {
                        if ($result->ResponseCode > 0) { //purchase not OKK
                          
                        } else if ($result->Msg == "OK" && $result->Result !== null) //purchase okkkkkkk
                        {
                            //var_dump($result);
                            //return;
                            if (isset($result->Result->Id)) {
                                if ($result->Result->Id !== null) {
                                    $flag = "";
                                    //api class should return id
                                    $srv_req_id = $result->Result->Id;

                                    $stmt2 = $this->pdo->prepare("call  `purchase_channels`(?,?,?,?)");
                                    $stmt2->execute([$api_key, $srv_req_id, $quantity, $country]);
                                    $res = $stmt2->fetchAll();
                                    $stmt2->closeCursor();
                                    $res = $res[0];
                                    $flag = $res['flag'];
                                    $reqId = $res['reqId'];
                                    $mini_result = (object) [
                                        'id' => $reqId,
                                        'Count' => $quantity,
                                        'country' => $country,
                                        'Cost' => $cost,
                                        'Balance' => floatval($balance) - floatval($cost),
                                    ];
                                    $result->Result = $mini_result;
                                    ######################
                                    #if user came from web update balance in session
                                    if (isset($_SESSION['balance'])) {
                                        // $stmtx = $this->pdo->prepare("SELECT balance from users where Id=?");
                                        // $stmtx->execute([$_SESSION['id']]);
                                        // $logs = $stmtx->fetchall();
                                        //$_SESSION['balance'] = $logs[0]['balance'];
                                        $_SESSION['balance'] = floatval($balance) - floatval($cost);
                                    }
                                    
                                    ######################

                                    return $result;
                                    #all ok so die()
                                    die();
                                } else {
                                    $this->iprint("result->Result->Id is null");
                                }
                            }
                        } else {
                            //return $result;
                            $this->iprint("responce code 0 but there is an error");
                        }
                    }
                }
            } else {

                
                return (object) ['ResponseCode' => 3, 'Msg' => $flag, 'Result' => null];
            }
        } catch (Exception $e) {

            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        return (object) [
            'ResponseCode' => 2,
            'Msg' => 'The current business is busy, no channels available, please try again later',
            'Result' => null,
        ];
    }

    public function is_channels_allowed($api_key)
    {

        try {
            $stmt = $this->pdo->prepare(
                "select  foreignapi.Name  as 'api_name' from tokens , user_allowed_api ,foreignapi  where
                `tokens`.`access_Token` =? and userID =user_id and api_id =Id_Api "
            );
            $stmt->execute([$api_key]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();

            foreach ($res as $api) {
                if (in_array('china_old_channels', $api)) {
                    return 1;
                }
            }
        } catch (Exception $e) {
            $this->logger->Add("cannot check if customer is allowed to take channels !! with token :" . $api_key, basename(__FILE__));
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }

        return 0;
    }

    public function down_result($api_key, $id)
    {
        if ($this->check_api_key($api_key) == false) {
            return null;
        }

        //$stmt0 = $this->pdo->prepare("SELECT userID as 'id' from tokens where access_token=?  ");
        $stmt0 = $this->pdo->prepare("SELECT userID as 'id', srv_req_id as 'filename' from tokens,channels_log where access_token=? and Id_request =?  ");
        $stmt0->execute([$api_key, $id]);
        $res = $stmt0->fetchall();
        $uid = $res[0]['id'];
        // var_dump($res);
        $filename = $res[0]['filename'];
        // $uid = $stmt0->fetch()['id'];
        // echo "<br>".$uid .  $filename."<br/>";
        if ($uid && $filename) {
            $allowedPages = [];

            //$filename =
            $items = $this->getallowedPages($uid);
            foreach ($items as $item) {
                array_push($allowedPages, $item['srv_req_id']);
            }

            $file_path = '/var/www/html/oldChannels/';
            //$file_path = '../oldChannels/';

            if (in_array($filename, $allowedPages) && file_exists($file_path . $filename)) {
                $filename = $file_path . $filename;
                return file_get_contents($filename);
            } else {
                echo '{"ResponseCode":2,"Msg":"forbidden 403","Result":""}';
                die();
                //output error
            }
        } else {
            echo '{"ResponseCode":2,"Msg":"forbidden 403","Result":""}';
            die();
        }
    }

    public function down_hashs($api_key, $id)
    {
        if ($this->check_api_key($api_key) == false) {
            return null;
        }

        //$stmt0 = $this->pdo->prepare("SELECT userID as 'id' from tokens where access_token=?  ");
        $stmt0 = $this->pdo->prepare("SELECT userID as 'id', srv_req_id as 'filename' from tokens,channels_log where access_token=? and Id_request =?  ");
        $stmt0->execute([$api_key, $id]);
        $res = $stmt0->fetchall();
        $uid = $res[0]['id'];
        // var_dump($res);
        $filename = $res[0]['filename'];
        //echo $filename;

        if ($uid && $filename) {
            $allowedPages = [];

            //$filename =
            $items = $this->getallowedPages($uid);
            foreach ($items as $item) {
                array_push($allowedPages, $item['srv_req_id']);
            }

            $file_path = '/var/www/html/oldChannels/';
            $file_path = '../oldChannels/';
            $orgfile = $filename;
            if (in_array($filename, $allowedPages) && file_exists($file_path . $filename)) {
                $filename = $file_path . $filename;
                //return file_get_contents($filename);

            } else {
                echo '{"ResponseCode":2,"Msg":"forbidden 403","Result":""}';
                die();
                //output error
            }

            $cnv_path = $file_path . "Hashs/";


            if (file_exists($cnv_path . $orgfile)) {
                $filename = $cnv_path . $orgfile;
                echo file_get_contents($filename);
                die();
            }
            //convert
            else {
                $data = file_get_contents($filename);
                $data = json_decode($data);
                if (isset($data->Data)) {
                    $res_hashs = "";
                    foreach ($data->Data as $item) {
                        $b = str_replace('-', '+', $item->b);
                        $b = base64_decode($b);
                        if (strpos($b, 'com.whatsapp') !== false) {
                            preg_match_all('!\d+!', $b, $phone);
                            if (sizeof($phone) == 1) {
                                $phone = $phone[0];
                                if (sizeof($phone) == 3)
                                    $phone = $phone[1];
                                else
                                    $phone = '00';
                            } else
                                $phone = '00';
                        } else {
                            $phone = '00';
                        }



                        if ($phone != '00') {
                            // extract hash
                            $hash = validate_data($item->c);
                            $res_hashs .= $phone . "," . $hash . PHP_EOL;
                        }
                    }
                    // echo $res_hashs;
                    if ($res_hashs != "") {
                        file_put_contents($cnv_path . $orgfile, $res_hashs);
                        $filename = $cnv_path . $orgfile;

                        return file_get_contents($filename);
                    }
                }
            }
        } else {
            echo '{"ResponseCode":2,"Msg":"forbidden 403","Result":""}';
            die();
        }
    }

    public function getallowedPages($id)
    {

        require_once './../backend/config.php';
        $query = "SELECT * FROM `channels_log` WHERE Id_user = ? order by TimeStmp desc";
        $arrayParams = [];
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        $items = $stmt->fetchall();

        return $items;
        //array_push

    }

    private function validate_data($data)
    {
        $data = str_replace('-', '+', $data);
        $data = base64_decode($data);
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $data) as $line) {
            if (strpos($line, 'client_static_keypair_pwd_enc') !== false) {
                //<string name="client_static_keypair_pwd_enc">[2,&quot;Y4TRtdiWz9QKncuK7MEK0GVCQYGKA4iKlWqKgZ7vBtRR5JblhZ9nIEmlhgIGsn4dPf985kE+pbLocjBVGPzuag&quot;,&quot;OYDmu4psa6Ks0u541F9UoA&quot;,&quot;BkK87Q&quot;,&quot;HPMo1wcHG+W07N16zq7ZLg&quot;]</string>
                $line = preg_replace("/&#?[a-z0-9]+;/i", '', $line);
                $line = str_replace('<string name="client_static_keypair_pwd_enc">[2,', '', $line);
                $line = str_replace(']</string>', '', $line);
                $line = str_replace(' ', '', $line);
                $line = explode(",", $line);
                if (sizeof($line) > 1) {
                    $line = $line[0];
                    return $this->extracthash($line);
                }

                break;
            }
        }
    }

    private function extracthash($data)
    {

        $base64_bytes = $data;
        $base64_bytes = $base64_bytes . "===";
        $message_bytes = base64_decode($base64_bytes);
        $public = substr($message_bytes, 0, 32);
        $private = substr($message_bytes, -32);
        $public = base64_encode($public);
        $private = base64_encode($private);
        $result = $public . "," . $private;
        return $result;
    }
   
}
