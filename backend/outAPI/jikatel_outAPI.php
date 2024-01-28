<?php

class jikatel_backend
{
    public function iprint($msg)
    {
        //echo "$msg <br>";
        $this->logger->Add($msg, basename(__FILE__));
    }

    public function __construct()
    {
        include '../config.php';
        include_once "../mylogger.php";
        $this->logger = new MyLogger();
        $this->pdo = $pdo;
    }

    public function GetStatistics()
    {
        try {
            // $sql = "select `bananaapi-number`.`country_code` as country_char,`countryList`.`country`,`countryList`.`country_code` from `bananaapi-number`,`countryList` where `bananaapi-number`.`taked` = 0 and `countryList`.`country_char`= `bananaapi-number`.`country_code` GROUP by `bananaapi-number`.`country_code`,`countryList`.`country` ,`countryList`.`country_code`";
            $sql = "SELECT `bananaapi-number`.`country_code` AS country_char, `countryList`.`country`, `countryList`.`country_code`, `countries_enabled`.`enabled`
            FROM `bananaapi-number`
            INNER JOIN `countryList` ON `countryList`.`country_char` = `bananaapi-number`.`country_code`
            INNER JOIN `countries_enabled` ON `countryList`.`id` = `countries_enabled`.`country_id`
            WHERE `bananaapi-number`.`taked` = 0
            GROUP BY `bananaapi-number`.`country_code`, `countryList`.`country`, `countryList`.`country_code`, `countries_enabled`.`enabled`";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = [];
            if(sizeof($results)>0){
                foreach($results as $res){
                    // $sql =   $sql = 'SELECT 
                    // (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = :country_code) AS total,
                    // (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = :country_code) AS avilable, 
                    // (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = :country_code) AS Requested, 
                    // (SELECT COUNT(*) FROM `requests_log` JOIN `foreignapiservice` ON `requests_log`.`service` = `foreignapiservice`.`Id_Service_Api` JOIN `bananaapi-number` ON `bananaapi-number`.phone_number = `requests_log`.`Phone_Nb` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`sms_content` IS NOT NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`taked` = 1) AS Has_sms,
                    // (SELECT COUNT(*) FROM `requests_log` JOIN `foreignapiservice` ON `requests_log`.`service` = `foreignapiservice`.`Id_Service_Api` JOIN `bananaapi-number` ON `bananaapi-number`.phone_number = `requests_log`.`Phone_Nb` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`sms_content` IS  NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`taked` = 1) AS Has_no_sms';
                    $sql = "SELECT 
                    (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = :country_code) AS total,
                     (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = :country_code) AS avilable, 
                    (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = :country_code) AS Requested, 
                    (SELECT COUNT(*) FROM `requests_log`,`foreignapiservice`,`bananaapi-number` where `foreignapiservice`.`country` = :country_code and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS NOT NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `bananaapi-number`.`taked` = 1 and `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb`) AS Has_sms,
                     (SELECT COUNT(*) FROM `requests_log`,`foreignapiservice`,`bananaapi-number` where `foreignapiservice`.`country` = :country_code and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS  NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `bananaapi-number`.`taked` = 1 and `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb`) AS Has_no_sms'";

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(array("country_code" => $res['country_char']));
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if(sizeof($result)>0){
                        $result = $result[0];
                        $result['country'] = $res['country'];
                        $result['country_code'] = $res['country_code'];
                        $result['enabled'] = $res['enabled'];
                        array_push($response,$result);
                       // break;
                    }
                    

                }
            }
            echo json_encode($response);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
           
        }


    }
}

$test = new jikatel_backend();
$test->GetStatistics();