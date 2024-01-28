<?php

require_once 'general_in_api.php';

class manualuser_api
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

    public function getnumber($businessCode, $country)
    {   
       
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
          
           $sql = "SELECT id, phone_number FROM `manual_numbers` where `taked`=0 and  `deleted` =0 and `country_char` = ? and `application` = 'wa'   limit 1 ";
           $sql .= "  FOR UPDATE";
        //    echo $sql;
            
           $this->pdo->beginTransaction();

           $stmt = $this->pdo->prepare($sql);
           $stmt->execute([$country]);
           $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Mark the row as taken
            if ($row) {
            $stmt = $this->pdo->prepare('UPDATE `manual_numbers` SET `taked`=1, `taked_time` = NOW() WHERE `id` = ?');
            $stmt->execute([$row['id']]);
            $this->pdo->commit();    
            $result->ResponseCode = 0;
            $number = str_replace("+", "", $row['phone_number']);

            $result->{"Result"} = (object) [
                'Id' => $row['id'],
                'Number' => (int)$number,
                'App' => "Whatsapp",
            ];
            return $result;
            }

            // Commit the transaction
            $this->pdo->commit();

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            $this->logger->Add($e->getMessage(), basename(__FILE__),"manual_err.log");
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

        $sql = 'SELECT `manual_numbers`.id, `manual_numbers`.`sms_code` as code FROM `requests_log`,`manual_numbers`,`foreignapiservice`  WHERE `requests_log`.Id_request = ?  and `requests_log`.`Phone_Nb` = `manual_numbers`.`phone_number` and `manual_numbers`.`taked` = 1 and `requests_log`.`service`= `foreignapiservice`.`Id_Service_Api` and LOWER(`foreignapiservice`.`code`) = LOWER(`manual_numbers`.`application`) limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $res = $stmt->fetch();
            $stmt->closeCursor();
            
            if(isset($res['code'])) {
            if ($res['code']) {
                $result->ResponseCode = 0;
                $result->Result = (object) [
                    'SMS' => '',
                    'Code' => $res["code"],
                ];
             
            $stmt2 = $this->pdo->prepare('UPDATE `manual_numbers` SET `taked_sms`= 1, `sms_taked_time` = NOW() WHERE `id` = ?');
            $stmt2->execute([$res['id']]);
          
            }
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

    public function worng_code($id)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];

        $sql = 'SELECT * FROM `requests_log` where `Id_request` = ? limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $res = $stmt->fetch();
            $stmt->closeCursor();

            if ($res['srv_req_id']) {
             
            // $stmt2 = $this->pdo->prepare('UPDATE `manual_numbers` SET `taked_sms`= 0, `wrong_code`  = 1  WHERE `id` = ?');
            // ane 3melta hek  Ta***
            $stmt2 = $this->pdo->prepare('UPDATE `manual_numbers` SET  `wrong_code`  = 1  WHERE `id` = ?');
            $stmt2->execute([$res['srv_req_id']]);
          
            $result = (object) [
                'ResponseCode' => 0,
                'Msg' => 'success',
                'Result' => "",
            ];
                
            } else {
                $result->Msg = "error";
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
    public function get_avialable($type=0)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
            ];
            $sql ='SELECT  COUNT(`bananaapi-number`.phone_number) AS count, `bananaapi-number`.application, `bananaapi-number`.`country_code`, `foreignapiservice`.code as app_code
            FROM `bananaapi-number` 
            INNER JOIN `foreignapiservice`
            ON `bananaapi-number`.application = `foreignapiservice`.Name and `bananaapi-number`.country_code = `foreignapiservice`.country
            WHERE `bananaapi-number`.`taked` = 0
            AND `foreignapiservice`.`Id_Foreign_Api` = 17 
            and `bananaapi-number`.`source` not like  "%hash%"
            and `bananaapi-number`.`source` not like  "%used%"
            and `bananaapi-number`.`source` not  like   "%new%" 
            GROUP BY `country_code`,`application`,`app_code`';
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
            echo 'Caught exception: ',  $e->getMessage(), "\n";
           
        }
        $result->ResponseCode = 1;

        $result->Msg = "error";

        return $result;
    }
}
