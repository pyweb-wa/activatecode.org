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
            $stmt->execute([$timestamp, $id]);
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
    }



    private function getValueAtPosition($position, $mstart, $mstop)
    {
        $totalLength = $mstart + $mstop;

        // Calculate the position within the repeating pattern
        if ($totalLength == 0) {
            return 0;
        }
        $patternPosition = $position % $totalLength;

        if ($patternPosition < $mstart) {
            return 1;
        } else {
            return 0;
        }
    }

    private function startstop($country_char, $source)
    {

        try {
            $sourceString = implode(",", $source);

            $placeholders = implode(',', array_map(function ($item) {
                return ':' . str_replace("-", "__", $item);
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
                $stmt->bindParam($array[$i], $source[$i], PDO::PARAM_STR);
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
                    if ($calculatedEnabledStatus == 1) {
                        // echo $calculatedEnabledStatus." ".$source." <br>";
                        array_push($enabledSources, $source);
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


    
    public function check_enabled($country_char,$api_key,$businessCode, $source = null)
    {
        try {
            require '/var/www/smsmarket/html/backend/redisconfig.php';
            $sources = $redis->sMembers("CountryPerm:$api_key");       
            $source = array();
            foreach ($sources as $value) {
                if (strpos($value,$country_char  ) !== false) {
                    $source[] = $value;
                }
            }      
            // echo $businessCode;die();
            $placeholders = implode(',', array_fill(0, count($source), '?'));
            $query = "SELECT `enabled`,`source` FROM `countries_control`,`countryList` WHERE `countryList`.`id` = `countries_control`.`country_id` and `countryList`.`country_char` = ?  AND (TIME(NOW()) >= IF(start = '00:00:00', '24:00:00', start) AND TIME(NOW()) <=  IF(stop = '00:00:00', '24:00:00', stop) or start = stop)  AND enabled = 1 and `countries_control`.application = ? and source IN ($placeholders)";

            // $query = "SELECT `enabled`,`source` FROM `countries_control`,`countryList` WHERE `countryList`.`id` = `countries_control`.`country_id` and `countryList`.`country_char` = ? AND (TIME(NOW()) >= start AND TIME(NOW()) <= stop OR start = stop) and enabled = 1 and `countries_control`.application = ? and source IN ($placeholders)";
              
            $stmt = $this->pdo->prepare($query);
            $params = array_merge([$country_char],[$businessCode], $source);
            $stmt->execute($params);
            $row = $stmt->fetchall(PDO::FETCH_ASSOC);
        //     echo 444;
        //    echo json_encode($row);die();
            
            $enabledSources = array();
            if ($row) {
                $enabled = 1;
                if (sizeof($row) == 1) {

                    $enabledSources[] = $row[0]["source"];
                } else if (sizeof($row) > 1) {

                    foreach ($row as $item) {
                        if ($item["enabled"] === 1) {

                            $enabledSources[] = $item["source"];
                        }
                    }
                }
                // echo json_encode($enabledSources);
                $running_source = $this->startstop($country_char, $source = $enabledSources);
                // var_dump($running_source);
                // echo "<br>";
                // // var_dump($enabledSources);
                // // echo "<br>";
                if (!$running_source) {
                    return false;
                }
                // echo 333;
                if ($enabled == 1) {
                    $commonValues = array_intersect($enabledSources, $running_source);
                //     echo "<br>";
                //     var_dump($commonValues);
                // echo "<br>";
                    return $commonValues;
                }
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }

        return false;
    }

    public function GetNumber_From_Redis($country_code, $source, $businessCode, $count = 1)
    {
        require '/var/www/smsmarket/html/backend/redisconfig.php';
        require '/var/www/smsmarket/html/backend/redisconfig1.php';

                $redisKey = "live_$source";
            //   echo $businessCode;die();
                $numbersArray = [];
                for ($i = 0; $i < $count; $i++) {
                    $phoneNumber = $redis->spop($redisKey);
                    if ($phoneNumber) {
                        $baseTimeZone = 'UTC';
                            // Generate a random time zone offset in seconds (e.g., between -7200 and 7200 seconds)
                            $randomTimeZoneOffset = rand(-7200, 7200);
                            // Set the time zone to the base time zone plus the random offset
                            date_default_timezone_set($baseTimeZone);
                            $microtime = microtime(true);
                            $milliseconds = round(($microtime + $randomTimeZoneOffset) * 10000);
                        //$timestampWithMicroseconds = microtime(true) * 10000;
                        $timestampAsInt = intval($milliseconds);
                        $numbersArray[] = [
                            "id" => $timestampAsInt,
                            "phone_number" => $phoneNumber
                        ];
                        // Prepare the SQL statement
                        $sql = "INSERT INTO `redis_numbers` 
                        (`id`, `phone_number`, `country_code`, `source`, `taked`, `createdTime`, `taked_time`) 
                        VALUES 
                        (:id, :phone_number, :country_code, :source, 1, (select created_time from countries_control where source = :_source) , :taked_time)";

                        $query = "select created_time from countries_control where source = ?";            
                        $stmt2 = $this->pdo->prepare($query);
                        $stmt2->execute([$source]);   
                        $source_time = $stmt2->fetchall(PDO::FETCH_ASSOC);

                        // Prepare and execute the statement
                        try{
                            $taked_time = date('Y-m-d H:i:s');
                            $redis1->hmset(
                                'redis_numbers:'.$timestampAsInt,
                                [
                                    'id' => $timestampAsInt,
                                    'phone_number' => $phoneNumber,
                                    'country_code' => $country_code,
                                    'source' => $source,
                                    'application' => $businessCode,
                                    'taked' =>1,
                                    'createdTime' => $source_time[0]['created_time'],
                                    'taked_time' => $taked_time,
                                    'is_finished' => 0,
                                    'is_finished_time' => 0,
                                ]
                            );
                            $ttlInSeconds = 518400; // 5 days
                            $redis1->expire("redis_numbers:$timestampAsInt", $ttlInSeconds);
                                                
                            $stmt = $this->pdo->prepare($sql);
                            $taked_time = date('Y-m-d H:i:s');
                            //$country_code = 'LB';
                            // Bind parameters
                            $stmt->bindParam(':id', $timestampAsInt);
                            $stmt->bindParam(':phone_number', $phoneNumber);
                            $stmt->bindParam(':country_code', $country_code);
                            $stmt->bindParam(':source', $source);
                            $stmt->bindParam(':_source', $source);
                         
                            $stmt->bindParam(':taked_time', $taked_time);
                            $stmt->execute();
                            // Optionally, check for success
                            
    
                        }
                       catch(Exception $e)
                            {
                                echo $e->getMessage();
                            }



                    } else {
                        // If Redis is empty or no more numbers are available, break the loop
                        break;
                    }
                }
                // die();
                return $numbersArray;
            //}
        //}

       return false;
    }



    public function getnumber($businessCode, $country, $available = 0, $count = 1, $api_key = 0)
    {


        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

            
            $enabled = $this->check_enabled($country,$api_key,$businessCode);

        //    echo json_encode ($enabled);
        //     die();
 
            if (!$enabled) {
                $result->ResponseCode = 2;
                $result->Msg = "The current business is busy, please try again later";
                return $result;
            }


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
            

            #$randomKey = array_rand($enabled[0]);
            $randomKey = array_rand($enabled);
            $randomSource = $enabled[$randomKey];
            // echo json_encode ($randomSource);
            // die();
 
            // echo 11;
            // var_dump( $randomSource);
            //  echo "<br>";
           // $randomSource = "LB_35_1998";
            //die();
            $count = (int)$count;
            $rows = $this->GetNumber_From_Redis($country, $randomSource, $businessCode,$count);
           
            if ($rows) {

                        
                // Mark the row as taken
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
                $result->source = $randomSource;

                require '/var/www/smsmarket/html/backend/redisconfig.php';

                $redisKey = 'simberry:' . $randomSource;

                foreach ($results as $number) {
                    $redis->sadd($redisKey, $number->Number);
                }
                //echo json_encode($result);

                $randomSleepTime = mt_rand(500000, 2000000);

                // Sleep for the random amount of time
                usleep($randomSleepTime);
                return $result;

            }

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

            // $stmt = $this->pdo->prepare('SELECT b.`id`, r.`Phone_Nb`, b.`sms`, b.`code`, r.`srv_req_Id`, b.`application`
            // FROM `requests_log` r
            // JOIN `bananaapi-results` b ON r.`Phone_Nb` = b.`phone_number`
            // JOIN `redis_numbers` n ON LOWER(b.`application`) = LOWER(n.`application`) AND n.`phone_number` = r.`Phone_Nb`
            // WHERE r.`Id_request` = ? AND b.`taked` = 0  and b.timestamp >= r.TimeStmp   limit 1');
            include '/var/www/smsmarket/html/backend/redisconfig1.php';

            $sql2 = "Select * from `requests_log` WHERE Id_request = ?";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([$id]);
            $res1 = $stmt2->fetchAll();
            // get application from redis 
           // echo json_encode($res);die();
            $RedisKey = "redis_numbers:".$res1[0]['srv_req_id'];
            
            $application = $redis1->hget($RedisKey, 'application');
           
            $sql = "SELECT b.`id`, r.`Phone_Nb`, b.`sms`, b.`code`, r.`srv_req_Id`, b.`application`
            FROM `requests_log` r
            JOIN `bananaapi-results` b ON r.`Phone_Nb` = b.`phone_number` And b.application = ?
            WHERE r.`Id_request` = ? AND b.`taked` = 0  and b.timestamp >= r.TimeStmp limit 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$application,$id]);
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            if (sizeof($res) > 0) {
                // echo json_encode($res);die();
                $result->ResponseCode = 0;
                $result->Result = (object) [
                    'SMS' => $res[0]["sms"],
                    'Code' => $res[0]["code"],
                    'application' => $res[0]["application"],
                ];

                $stmt2 = $this->pdo->prepare('UPDATE `bananaapi-results` SET `taked`=1, `taked_time` = NOW() WHERE `id` = ? ');
                $stmt2->execute([$res[0]['id']]);

                $stmt3 = $this->pdo->prepare('UPDATE `redis_numbers` SET `is_finished` = 1, `is_finished_time` =NOW() WHERE `id` = ? ');
                $stmt3->execute([$res[0]["srv_req_Id"]]);
                
                //update redis_numbers in redis
                $finished_time = date('Y-m-d H:i:s');
                $redis1->hset( $RedisKey, 'is_finished', 1);
                $redis1->hset( $RedisKey, 'is_finished_time',$finished_time);
            

                // $this->updateTaked($res[0]['id'], "`bananaapi-results`");
            } else {
                $result->Msg = "waiting for sms";
            }
            return $result;
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), "\n";

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

            $sql = 'SELECT
            `country_stats`.`country_char` as country_code,
            countryList.country,
            `countries_control`.`enabled` as status,
            `countries_control`.`application` as application,
            `application_code`.id as app_code,
            1 as price_out,
            "10000" as count
            FROM
                country_stats
            LEFT JOIN
                countryList ON `country_stats`.`country_char` = `countryList`.`country_char`
            LEFT JOIN
                countries_control ON `country_stats`.`source` = `countries_control`.`source`
                                AND `countries_control`.`country_id` = `countryList`.`id`
			 LEFT JOIN
                application_code ON lower(`application_code`.`application`) = `countries_control`.`application`
                                AND `country_stats`.`source` = `countries_control`.`source`
            WHERE
                `countries_control`.`source` IS NOT NULL 
                AND `countries_control`.`enabled` = 1
            GROUP BY
                `country_stats`.`country_char`, countryList.country,application,app_code;';

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute();
            $res = $stmt->fetchAll();
            $stmt->closeCursor();
            if (sizeof($res) > 0) {

                $array = (array) $res;
                $result->ResponseCode = 0;
                $result->Result = $array;


                return $result;
            }
        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__), "jikatel_err.log");

            // echo 'Caught exception: ',  $e->getMessage(), "\n";

        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }
}
