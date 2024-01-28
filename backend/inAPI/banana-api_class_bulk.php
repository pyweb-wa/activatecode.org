<?php

require_once 'general_in_api.php';

class banana_api
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

    ## Admin

    public function updateTaked($id, $table)
    {

        try {
            #TODO change id dynamic from request
            $stmt = $this->pdo->prepare('UPDATE ' . $table . ' SET `taked`=1, `taked_time` = ? WHERE `id` = ?');
            $timestamp = date("Y-m-d H:i:s");
            $stmt->execute([$timestamp,$id]);

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
    }


    
    private function getValueAtPosition($position, $mstart, $mstop) {
        $totalLength = $mstart + $mstop;
        
        // Calculate the position within the repeating pattern
        if($totalLength == 0){
            return 0;
        }
        $patternPosition = $position % $totalLength;
        
        if ($patternPosition < $mstart) {
            return 1;
        } else {
            return 0;
        }
    }
    
    private function startstop($country_char,$source){
       
        try {
            $sourceString = implode(",", $source);

            $placeholders = implode(',', array_map(function($item) {
                return ':' . str_replace("-","__",$item);
            }, $source));
            
            $array = explode(',', $placeholders);

            // Remove empty elements and any trailing hyphens
            $array = array_map('trim', array_filter($array, 'strlen'));
            
            

            $query = "SELECT 
                    start, 
                    stop,
                    mstart,
                    mstop,
                    enabled,
                    source
                FROM 
                    countries_control
                JOIN
                    countryList ON countryList.id = countries_control.country_id
                WHERE 
                    countryList.country_char = :countryChar and source in ($placeholders)";
                  

            $stmt = $this->pdo->prepare($query);

            // Bind the named parameters
            $stmt->bindParam(":countryChar", $country_char, PDO::PARAM_STR);
            for ($i = 0; $i < count($source); $i++) {
            $stmt->bindParam( $array[$i], $source[$i], PDO::PARAM_STR);
          
            }


        $stmt->execute();

      
    
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $enabledSources = array();
        foreach ($result as $row) {
            $start = strtotime($row["start"]);
            $stop = strtotime($row["stop"]);
            $mstart = intval($row["mstart"]);
            $mstop = intval($row["mstop"]);
            $enabled = intval($row["enabled"]);
            $source = $row["source"];
            $now = time();  
            if (($now >= $start && $now <= $stop) || $start == $stop) {
                $time_diff = $now - $start;    
                // Calculate the number of minutes elapsed
              $minutes_elapsed = floor($time_diff / 60);
    
              $calculatedEnabledStatus = $this->getValueAtPosition($minutes_elapsed, $mstart, $mstop);
              if ($calculatedEnabledStatus == 1){
                 // echo $calculatedEnabledStatus." ".$source." <br>";
                  array_push($enabledSources,$source);
              }
            }
            $conn = null;
          
        }
        //var_dump($enabledSources);die();
        return $enabledSources;
        } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        
        }
    
        $conn = null;
        return 1;
    }
    
    public function check_enabled($country_char,$source=null){
        try{
           
            $sql = "SELECT `enabled`,`source`  FROM `countries_control`,`countryList` WHERE `countryList`.`id` = `countries_control`.`country_id` and `countryList`.`country_char` = ? AND (TIME(NOW()) >= start AND TIME(NOW()) <= stop OR start = stop)  and enabled = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$country_char]);
            $row = $stmt->fetchall(PDO::FETCH_ASSOC);
          
            
           
            $enabledSources = array();
            if ($row) {
                $enabled = 1;
                if (sizeof($row) == 1){
                   
                    $enabledSources[] = $row[0]["source"];
                  
                }
                else if (sizeof($row) > 1){
                 
                    foreach ($row as $item) {
                        if ($item["enabled"] === 1) {
                            
                            $enabledSources[] = $item["source"];
                        }
                    }
                }
                
                $running_source = $this->startstop($country_char,$source=$enabledSources);
                if(!$running_source){
                    return false;
                }

                if ($enabled == 1 ){
                    $commonValues = array_intersect($enabledSources, $running_source);
                    return $commonValues;
                }
            }
           

        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
      
        return false;
    }
    
    public function getnumber($businessCode, $country,$available=0,$count=1,$api_key=0)
    {   
       
      
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
          
            
            $enabled = $this->check_enabled($country);
           // echo $enabled;die();
            if(!$enabled){
                $result->ResponseCode = 2;
                $result->Msg = "The current business is busy, please try again later";
                return $result;
            }
           

            if($country == "any"){
                $sql = "SELECT countryList.`country_char` from countryList,user_countries,tokens where countryList.id = `user_countries`.country_id and tokens.userID = `user_countries`.user_id and tokens.access_Token = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$api_key]);
                $row = $stmt->fetchall(PDO::FETCH_ASSOC);
                if($row){
                    $countries = array_column($row, 'country_char');
                    if (sizeof($countries) >1 ){
                        shuffle($countries);
                        $country = reset($countries);
                    }else {
                        $country = $countries[0];
                    }
                   
                }

            }
            // $sql = "SELECT id, phone_number FROM `bananaapi-number` WHERE `taked`=0 AND `country_code` = ? AND `application` = ? AND type = 0 AND `source` IN ('" . implode("', '", $enabled) . "') LIMIT 1";

            $randomKey = array_rand($enabled);
            $randomSource = $enabled[$randomKey];
            $count = (int)$count;
           // echo $randomSource;
            $sql = "SELECT id, phone_number FROM `bananaapi-number` WHERE `taked`=0 AND `country_code` = ? AND `application` = ? AND type = 0 AND `source` = ? LIMIT $count";
           
           $sql .= "  FOR UPDATE";
           
           $this->pdo->beginTransaction();

           $stmt = $this->pdo->prepare($sql);
           $stmt->execute([$country,$businessCode,$randomSource]);
           $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
           // $this->pdo->commit();
            //echo json_encode(($row));die();
            // Mark the row as taken
            if ($rows) {
                $ids = array_column($rows, 'id');
                $sql2 = 'UPDATE `bananaapi-number` SET `taked`=1,  `taked_time` = NOW() WHERE `id` IN (' . implode(',', $ids) . ')';
                // echo "<br>";
                // echo $sql2;
                // die();
                $stmt = $this->pdo->prepare($sql2);
                $stmt->execute();
                
                $this->pdo->commit();    
                // $stmt = $this->pdo->prepare('UPDATE `bananaapi-number` SET `taked`=1,  `taked_time` = NOW() WHERE `id` = ?');
            
                // $stmt->execute([$row['id']]);
                //$this->pdo->commit();    
                //$result->ResponseCode = 0;
                //$number = str_replace("+", "", $rows['phone_number']);
                //  echo json_encode($rows);die();   
                $results = []; 
            
                foreach ($rows as $row) {
                $number = str_replace("+", "", $row['phone_number']);
                
                $result2 = (object) [
                    'Id' => $row['id'],
                    'Number' => (int)$number,
                    'App' => "Whatsapp",
                ];
        
                $results[] = $result2;
                }
        
                // Return the results array
                $result->ResponseCode = 0;
                $result->Result = $results;
       
        
              // Optionally, echo or return $result
                //echo json_encode($result);die();
                $numberArray = array_map(function($item) {  
                    return "$item->Number";
                }, $result->Result);
            // Convert the new array to JSON
            $newJsonArray = json_encode($numberArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
            //echo $newJsonArray;
            //die();
            ///////
                if (strpos($randomSource, '_') !== false) {
                // If found, extract the number after the underscore
                $parts = explode('_', $randomSource);
                
                // Check if there are enough parts
                if (count($parts) >= 3) {
                    $server_name = $parts[1];
                    $list_id = $parts[2];
                    include 'simberry/api2.php';
                    $accounts = get_accounts();
                    
                    $found = false;

                    foreach ($accounts as $server) {
                        if (isset($server['name']) && $server['name'] == $server_name) {
                            $found = $server;
                            break;
                        }
                    }
                    if ($found) {
                      $res =  AddArrayToList($server,$newJsonArray,$list_id);
                     
                    }
                                    
                } 
                }
            

            //var_dump($result);
            return $result;
        }

            // Commit the transaction
        


           
                // return $result;
           // }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
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

            //$sql = 'SELECT `bananaapi-results`.`code` as code FROM `requests_log`,`bananaapi-results`,`foreignapiservice`  WHERE `requests_log`.Id_request = ?  and CONCAT('+',`requests_log`.`Phone_Nb`) = `bananaapi-results`.`phone_number` and `bananaapi-results`.`taked` = 1 and `requests_log`.`service`= `foreignapiservice`.`Id_Service_Api` and LOWER(`foreignapiservice`.Name) = LOWER(`bananaapi-results`.`Sender_n`)';

            // $sql = "SELECT `bananaapi-results`.`id`,`requests_log`.`Phone_Nb`,`bananaapi-results`.`sms`,`bananaapi-results`.`code` as code ,`requests_log`.`srv_req_Id` FROM `requests_log`,`bananaapi-results`,`bananaapi-number` WHERE `requests_log`.Id_request = 6185863 and `requests_log`.`Phone_Nb` = `bananaapi-results`.`phone_number` and `bananaapi-results`.taked = 0 and LOWER(`bananaapi-results`.`application`) = LOWER(`bananaapi-number`.`application`) AND `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb`;";


            $stmt = $this->pdo->prepare('SELECT b.`id`, r.`Phone_Nb`, b.`sms`, b.`code`, r.`srv_req_Id`, b.`application`
            FROM `requests_log` r
            JOIN `bananaapi-results` b ON r.`Phone_Nb` = b.`phone_number`
            JOIN `bananaapi-number` n ON LOWER(b.`application`) = LOWER(n.`application`) AND n.`phone_number` = r.`Phone_Nb`
            WHERE r.`Id_request` = ? AND b.`taked` = 0  and b.timestamp >= r.TimeStmp   limit 1');
        
            $stmt->execute([$id]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            if (sizeof($res) > 0) {
                $result->ResponseCode = 0;
                $result->Result = (object) [
                    'SMS' => $res[0]["sms"],
                    'Code' => $res[0]["code"],
                    'application' => $res[0]["application"],
                ];

                $stmt2 = $this->pdo->prepare('UPDATE `bananaapi-results` SET `taked`=1, `taked_time` = NOW() WHERE `id` = ? ');
                $stmt2->execute([$res[0]['id']]);

                $stmt3 = $this->pdo->prepare('UPDATE `bananaapi-number` SET `is_finished` = 1, `is_finished_time` =NOW() WHERE `id` = ? ');
                $stmt3->execute([$res[0]["srv_req_Id"]]);
              
                // $this->updateTaked($res[0]['id'], "`bananaapi-results`");
            } else {
                $result->Msg = "waiting for sms";
            }
            return $result;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
          
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }

    //SELECT `country_code`, COUNT(*) as count FROM `bananaapi-number` where `taked` = 0 GROUP BY `country_code`;

    public function get_avialable($type)
    {

        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

    //         $stmt = $this->pdo->prepare('SELECT  COUNT(`bananaapi-number`.phone_number) AS count, `bananaapi-number`.application, `bananaapi-number`.`country_code`, `foreignapiservice`.code as app_code
    //         FROM `bananaapi-number` 
    //         INNER JOIN `foreignapiservice`
    //         ON `bananaapi-number`.application = `foreignapiservice`.Name and `bananaapi-number`.country_code = `foreignapiservice`.country
    // WHERE `bananaapi-number`.`taked` = 0
    // AND `foreignapiservice`.`Id_Foreign_Api` = 17 
    // and `bananaapi-number`.`source` not like  "%hash%"
    // and `bananaapi-number`.`source` not like  "%used%"
    // and `bananaapi-number`.`source` not  like   "%new%" 
    // GROUP BY `country_code`,`application`,`app_code`');
        // $sql = "SELECT COUNT(n.phone_number) AS count, n.application, n.country_code, f.code AS app_code FROM `bananaapi-number` AS n INNER JOIN foreignapiservice AS f ON lower(n.application) = lower(f.Name) AND n.country_code = f.country WHERE n.taked = 0 AND f.Id_Foreign_Api = 17 AND n.type = 0 GROUP BY n.country_code, n.application, f.code;";
            $sql = "SELECT 
                    SUM(subquery.count) AS count,
                    subquery.application,
                    subquery.country_code,
                    subquery.app_code,
                    COALESCE(subquery.status, 0) AS status
                FROM (
                    SELECT 
                        COUNT(n.phone_number) AS count, 
                        n.application, 
                        n.country_code, 
                        f.code AS app_code, 
                        MAX(c.enabled) AS status 
                    FROM 
                        `bananaapi-number` AS n 
                    INNER JOIN 
                        foreignapiservice AS f ON lower(n.application) = lower(f.Name) AND n.country_code = f.country 
                    INNER JOIN 
                        countryList AS cl ON n.country_code = cl.country_char 
                    LEFT JOIN 
                        countries_control AS c ON cl.id = c.country_id and c.source = n.source
                    WHERE 
                        n.taked = 0 AND f.Id_Foreign_Api = 17 AND n.type = 0 
                    GROUP BY 
                        n.country_code, n.application, f.code
                ) AS subquery
                GROUP BY 
                    subquery.country_code, subquery.application, subquery.app_code, subquery.status;";
    
    $stmt = $this->pdo->prepare( $sql);

            

            if ($type == 1){
 $stmt = $this->pdo->prepare('SELECT COUNT(n.phone_number) AS count, n.application, n.country_code, f.code AS app_code FROM `bananaapi-number` AS n INNER JOIN foreignapiservice AS f ON lower(n.application) = lower(f.Name) AND n.country_code = f.country WHERE n.taked = 0  AND f.Id_Foreign_Api = 17 AND n.type = 1 GROUP BY n.country_code, n.application, f.code;');
            }
            $stmt->execute();
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            if (sizeof($res) > 0) {
                
                $array = (array) $res;
                $result->ResponseCode = 0;
                $result->Result = $array;
               
                return $result;
                 //echo json_encode($result);
    
    
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__),"jikatel_err.log");

           // echo 'Caught exception: ',  $e->getMessage(), "\n";
           
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }
}
