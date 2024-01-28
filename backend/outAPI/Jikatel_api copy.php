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
        $this->path = "/var/www/sms-paltform/backend/logging/jikatel";
    }
    public function reactivate($obj)
    {
        // include '/usr/local/lsws/customers/html/backend/config.php';
        try {

            $stmt1 = $this->pdo->prepare("SELECT * FROM `bananaapi-number` where  `taked` = 1 and is_finished = 0 and country_code =? and source=? ");
            $stmt1->execute([$obj['data']['country_char'], $obj['data']['source']]);

            $results = $stmt1->fetchAll();

            if (count($results) == 0) {
                echo json_encode(['status' => 'ok']);
                die();
            }

            if (sizeof($results) >= 1) {

                foreach ($results as $res) {
                    $stmt = $this->pdo->prepare('UPDATE `bananaapi-number` SET `taked`=0, taked_time=null,is_finished=0,is_finished_time=null, release_time = NOW() WHERE `id` = ?');
                    $stmt->execute([$res['id']]);

                }
                echo json_encode(['status' => 'ok']);
            }
        } catch (Exception $e) {
            // file_put_contents('/usr/local/lsws/customers/logging/reactivate_err.log', $e->getMessage(), FILE_APPEND);
            echo ($e->getMessage());
        }

    }
    public function autoreactivate($obj)
    {
        try {

            $stmt1 = $this->pdo->prepare("SELECT * FROM `countries_control` where  `reactivate` = 1");
            $stmt1->execute([]);

            $results = $stmt1->fetchAll();
            if (count($results) == 0 || $results == null) {
                $stmt = $this->pdo->prepare('UPDATE `countries_control` SET `reactivate`=1');
                $stmt->execute([]);
                echo json_encode(['status' => 'ok','msg'=>'auto reactivate start']);
                die();

            }else
            if (sizeof($results) >= 1) {
                    $stmt = $this->pdo->prepare('UPDATE `countries_control` SET `reactivate`=0');
                    $stmt->execute([]);
                echo json_encode(['status' => 'ok','msg'=>'auto reactivate stop']);
                die();
            }
        } catch (Exception $e) {
            echo ($e->getMessage());
        }

    }
    public function autoreactivatestatus($obj)
    {
        try {

            $stmt1 = $this->pdo->prepare("SELECT * FROM `countries_control` where  `reactivate` = 1");
            $stmt1->execute([]);

            $results = $stmt1->fetchAll();
            if (count($results) == 0 || $results == null) {
                echo json_encode(['status' => 'ok','msg'=>'stoped']);
                die();

            }else
            if (sizeof($results) >= 1) {
                echo json_encode(['status' => 'ok','msg'=>'started']);
                die();
            }
        } catch (Exception $e) {
            echo ($e->getMessage());
        }

    }
    public function deletenumbersstats($obj){
        $sql2 = " DELETE FROM country_stats where country_char =? and source =?  ";
        $stmt = $this->pdo->prepare($sql2);
        $stmt->execute([$obj['data']['country_char'], $obj['data']['source']]);
    }
    public function deletenumbers($obj)
    {
        try {
            $sql = "DELETE from `bananaapi-number` where country_code =? and source =? and is_finished = 0 ";
            if ($obj["action"] == "deleteNB") {
                $sql = " DELETE FROM `bananaapi-number` where country_code =? and source =? ";
                //echo $obj['data']['country_char']." ". $obj['data']['source'];die();
                $sql2 = " DELETE FROM countries_control where  country_id = (SELECT id FROM countryList where `country_char` = ? limit 1) and source =?  ";

                $stmt = $this->pdo->prepare($sql2);
                $stmt->execute([$obj['data']['country_char'], $obj['data']['source']]);
                $this->deletenumbersstats($obj);
            }
            $stmt1 = $this->pdo->prepare($sql);
            $stmt1->execute([$obj['data']['country_char'], $obj['data']['source']]);
            echo json_encode(['status' => 'ok']);
            die();
        } catch (Exception $e) {
            echo json_encode(['status' => $e->getMessage()]);
            die();
        }
    }
    public function GetDataStats($source = null)
    {
        date_default_timezone_set('Asia/Beirut');
        $sql = "select * from country_stats";
        if($source != null){
            $sql = $sql." where source = '". $source."'";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
    public function GetStatistics($enable = null)
    {
        date_default_timezone_set('Asia/Beirut');
        try {
            //     $sql = "SELECT
            //     `bananaapi-number`.`country_code` AS country_char,
            //     `bananaapi-number`.`source`,
            //     `countryList`.`country`,
            //     `countryList`.`country_code`,
            //     `countries_control`.`enabled`,
            //     `countries_control`.`start`,
            //     `countries_control`.`stop`,
            //     `countries_control`.`mstart`,
            //     `countries_control`.`mstop`
            // FROM
            //     `bananaapi-number`
            //     INNER JOIN `countryList` ON `countryList`.`country_char` = `bananaapi-number`.`country_code`
            //     INNER JOIN `countries_control` ON `countryList`.`id` = `countries_control`.`country_id`
            // WHERE
            //     `bananaapi-number`.`is_finished` = 0
            //     AND `bananaapi-number`.`source` = `countries_control`.`source`
            // GROUP BY
            //     `bananaapi-number`.`country_code`,
            //     `countryList`.`country`,
            //     `countryList`.`country_code`,
            //     `countries_control`.`enabled`,
            //     `countries_control`.`start`,
            //     `countries_control`.`stop`,
            //     `countries_control`.`mstart`,
            //     `countries_control`.`mstop`,
            //     `bananaapi-number`.`source`;";

            $sql = " SELECT
            `bananaapi-number`.`country_code` AS country_char,
            `bananaapi-number`.`source`,
            `countryList`.`country`,
            `countryList`.`country_code`,
            `countries_control`.`enabled`,
            `countries_control`.`start`,
            `countries_control`.`stop`,
            `countries_control`.`mstart`,
            `countries_control`.`mstop`
        FROM
            `bananaapi-number`, `countries_control` ,countryList
            where is_finished = 0 and `countryList`.`id` = `countries_control`.`country_id` and `bananaapi-number`.`source` = `countries_control`.`source` and `bananaapi-number`.`country_code` = `countryList`.`country_char` ";

            if ($enable != null) {

                $sql = $sql . " and `countries_control`.`enabled` = 1 ";

            }

            $sql = $sql . " GROUP BY
            `bananaapi-number`.`country_code`,
            `countryList`.`country`,
            `countryList`.`country_code`,
            `countries_control`.`enabled`,
            `countries_control`.`start`,
            `countries_control`.`stop`,
            `countries_control`.`mstart`,
            `countries_control`.`mstop`,
            `bananaapi-number`.`source`; ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = [];
            if (sizeof($results) > 0) {
                foreach ($results as $res) {
                    // if ($res['country_char'] != "CL") {

                    //     continue;
                    // }
                    $sql = "SELECT
                    (SELECT COUNT(*) FROM `bananaapi-number` WHERE `country_code` = :country_code and `source` = :source) AS total,
                     (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = :country_code and `source` = :source) AS available,
                    (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 1 and `country_code` = :country_code and `source` = :source) AS requested,
                    (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`,`foreignapiservice`,`bananaapi-number` where `foreignapiservice`.`country` = :country_code  and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content` IS NOT NULL and `foreignapiservice`.`Id_Foreign_Api` =17 and `bananaapi-number`.`is_finished` = 1 and `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb` and `bananaapi-number`.`source` = :source) AS has_sms,
                     (SELECT COUNT(DISTINCT `requests_log`.`Phone_Nb`) FROM `requests_log`, `foreignapiservice`, `bananaapi-number` WHERE `foreignapiservice`.`country` = :country_code AND `requests_log`.`service` = `foreignapiservice`.Id_Service_Api AND `requests_log`.`sms_content` IS NULL AND `foreignapiservice`.`Id_Foreign_Api` = 17 AND `bananaapi-number`.`is_finished` = 0 AND `bananaapi-number`.`phone_number` = `requests_log`.`Phone_Nb` AND `bananaapi-number`.`source` = :source) AS no_sms";

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(array("country_code" => $res['country_char'], "source" => $res['source']));
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (sizeof($result) > 0) {
                        $result = $result[0];
                        $result['country'] = $res['country'];
                        $result['country_code'] = $res['country_code'];
                        $result['country_char'] = $res['country_char'];
                        $result['status'] = $res['enabled'];
                        $start = strtotime($res['start']);
                        $start = date('h:i a', $start);
                        $stop = strtotime($res['stop']);
                        $stop = date('h:i a', $stop);
                        $result['start'] = $start;
                        $result['stop'] = $stop;
                        $result['Mstart'] = intval($res['mstart']);
                        $result['Mstop'] = intval($res['mstop']);
                        $result['source'] = $res['source'];
                        $result['servertime'] = date('Y-m-d H:i:s', time());

                        array_push($response, $result);
                        // break;
                    }

                }
            }
            return ($response);
        } catch (Exception $e) {
            echo $sql . "<br>";
            echo 'Caught exception: ', $e->getMessage(), "\n";

        }

    }

    public function download($data)
    {
        try {

            // $stmt = $this->pdo->prepare("SELECT phone_number from  `bananaapi-number`,`countryList` where `taked` = 0 and `bananaapi-number`.`country_code` = `countryList`.`country_char` and `countryList`.`country` = ?");
            //  echo $country_code;

            $stmt = $this->pdo->prepare("SELECT phone_number from `bananaapi-number` where `taked` = 0 and `bananaapi-number`.`country_code` = ?");
            $stmt->execute([$data]);
            $result = $stmt->fetchAll();
            $numbers = "";
            foreach ($result as $row) {
                $numbers .= $row['phone_number'] . "\r\n";
            }

            echo $numbers;
        } catch (PDOException $e) {
            // file_put_contents("/usr/local/lsws/hashJikatel/logging/errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            echo "Error: " . $e->getMessage();
        }
    }
    public function powertogglestats($data){
        if (isset($data['data']['status'], $data['data']['country_char'], $data['data']['source'])) {
            $country_char = $data['data']['country_char'];
            $status = intval($data['data']['status']);
            if ($status !== 0 && $status !== 1) {
                echo "error value of status " . gettype($status);
                die();
            }
            $status = ($status == 0) ? 1 : 0;
            $source = $data['data']['source'];
            $sql = "UPDATE country_stats
            SET status = :status WHERE country_char = :country_char  AND source = :source;
        ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":source", $source);
            $stmt->bindParam(":country_char", $country_char);
            $stmt->execute();

    }
}
    public function powertoggle($data)
    {
        try {

            if (isset($data['data']['status'], $data['data']['country_char'], $data['data']['source'])) {
                $country_char = $data['data']['country_char'];
                $status = intval($data['data']['status']);
                if ($status !== 0 && $status !== 1) {
                    echo "error value of status " . gettype($status);
                    die();
                }
                $status = ($status == 0) ? 1 : 0;
                $source = $data['data']['source'];
                $sql = "UPDATE countries_control
            JOIN countryList ON countries_control.country_id = countryList.id
            SET enabled = :status WHERE countryList.country_char = :country_char  AND countries_control.source = :source;
            ";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(":status", $status);
                $stmt->bindParam(":source", $source);
                $stmt->bindParam(":country_char", $country_char);

                // Execute the update statement
                if ($stmt->execute()) {
                   $this->powertogglestats($data);
                    echo json_encode(['status' => 'ok']);
                    die();
                    // echo "Update successful for ".$country_char."with source: ".$source ;
                } else {
                    echo json_encode(['status' => 'error']);
                    die();
                    //   echo "Update failed.";
                }
            } else {
                echo "missing keys";
            }

        } catch (PDOException $e) {
            //file_put_contents("/usr/local/lsws/hashJikatel/logging/errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            echo "Error: " . $e->getMessage();
        }
    }

    public function SaveConfig($obj)
    {
        try {

            if (isset($obj['data']['status'], $obj['data']['start'], $obj['data']['stop'], $obj['data']['Mstart'], $obj['data']['Mstop'], $obj['data']['country_char'])) {

                $start = DateTime::createFromFormat('h:i A', $obj['data']['start']);
                $start = $start->format('H:i:s');
                $stop = DateTime::createFromFormat('h:i A', $obj['data']['stop']);
                $stop = $stop->format('H:i:s');

                $sql = "UPDATE countries_control
                JOIN countryList ON countries_control.country_id = countryList.id
                SET enabled = :status, start = :start, stop =:stop, mstart = :mstart, mstop = :mstop
                WHERE countryList.country_char = :country_char  AND countries_control.source = :source;
                ";
                $stmt = $this->pdo->prepare($sql);
                // Bind the parameters
                $stmt->bindParam(":status", $obj['data']['status']);
                $stmt->bindParam(":start", $start);
                $stmt->bindParam(":stop", $stop);
                $stmt->bindParam(":mstart", $obj['data']['Mstart']);
                $stmt->bindParam(":mstop", $obj['data']['Mstop']);
                $stmt->bindParam(":country_char", $obj['data']['country_char']);
                $stmt->bindParam(":source", $obj['data']['source']);

                // Execute the update statement
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'ok']);
                    die();
                } else {
                    echo json_encode(['status' => 'error']);
                    die();
                }

            }
        } catch (PDOException $e) {
            //file_put_contents("/usr/local/lsws/hashJikatel/logging/errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            echo "Error: " . $e->getMessage();
        }
    }
}

function returner($sms)
{
    $sms = json_encode($sms);
    echo $sms;
    die();
}

function check_key()
{
    //$headers = apache_request_headers();
    $headers = getallheaders();
    if (isset($headers['Authorization']) && isset($headers['Code'])) {

        $key = md5($headers['Code'] . "banana-api-passwordCode");
        if ($key == $headers['Authorization']) {
            return true;
        }

    }
    return false;
}

// $api = new jikatel_backend();
// $result = $api->GetStatistics();
// returner($result);
// die();

if (!check_key()) {
    die();
}

$request = file_get_contents('php://input');
$obj = json_decode($request, true);

if (isset($obj["action"])) {
    $api = new jikatel_backend();
    switch ($obj["action"]) {
        case "getdata":
            if(isset($obj["source"]) && !empty($obj["source"]))
            $result = $api->GetDataStats($obj["source"]);
            else
            $result = $api->GetDataStats();
            returner($result);
            break;
        case "getdata1":
            $result = $api->GetStatistics();

            returner($result);
            break;
        case "download":
            $country_char = $obj['data']['country_char'];
            $result = $api->download($country_char);
            echo $result;
            break;
        case "powertoggle":
            $result = $api->powertoggle($obj);
            break;
        case "SaveConfig":
            $result = $api->SaveConfig($obj);
            break;
        case "getstats":
            $result = $api->GetStatistics(1);
            returner($result);
            break;
        case "reactivate":
            $result = $api->reactivate($obj);
            returner($result);
            break;
        case "autoreactivatestatus":
            $result = $api->autoreactivatestatus($obj);
            returner($result);
            break;
        case "autoreactivate":
            $result = $api->autoreactivate($obj);
            returner($result);
            break;
        case "deleteNB":
           
            $result = $api->deletenumbers($obj);
            returner($result);
            break;
        case "deleteN":
            $result = $api->deletenumbers($obj);
            returner($result);
            break;
        default:
            break;
    }
}
