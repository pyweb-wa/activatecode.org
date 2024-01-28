<?php
// require_once "validate_token.php";
// if (!checkTokenInDatabase()) {
//     header('Location: index.php');
//     exit(); 
// }
date_default_timezone_set('Asia/Beirut');

while (true) {
    $start = microtime(true);

    stats();
    $end = microtime(true);
    $executionTime = $end - $start;

    echo "Script execution time: " . $executionTime . " seconds" . PHP_EOL;

    sleep(20);
}
function stats()
{

    try {
        include '/usr/local/lsws/customers/html/backend/config.php';

        // $sql = "SELECT `bananaapi-number`.`createdTime` t FROM `bananaapi-number` group by t ORDER BY t DESC limit 1";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute([]);
        // $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $currentday = $results[0]['t'];

        $sql = "SELECT
		`t`.`country_id`,
        `t`.`country_char`,
        `t`.`source`,
        `t`.`createdTime`,
        `t`.`country`,
        `t`.`country_code`,
        `t`.`enabled`,
        `t`.`start`,
        `t`.`stop`,
        `t`.`mstart`,
        `t`.`mstop`
    FROM
        (
            SELECT
                `bananaapi-number`.`country_code` AS country_char,
                `bananaapi-number`.`source`,
                MAX(`bananaapi-number`.`createdTime`) AS createdTime,
                `countryList`.`country`,
            	`countryList`.`id` as country_id,
                `countryList`.`country_code`,
                `countries_control`.`enabled`,
                `countries_control`.`start`,
                `countries_control`.`stop`,
                `countries_control`.`mstart`,
                `countries_control`.`mstop`
            FROM
                `bananaapi-number`
            JOIN
                `countries_control` ON `bananaapi-number`.`source` = `countries_control`.`source`
            JOIN
                `countryList` ON `bananaapi-number`.`country_code` = `countryList`.`country_char`
            WHERE
                `bananaapi-number`.`is_finished` = 0
            GROUP BY
                `bananaapi-number`.`country_code`,
                `countryList`.`country`,
            	`countryList`.`id`,
                `countryList`.`country_code`,
                `countries_control`.`enabled`,
                `countries_control`.`start`,
                `countries_control`.`stop`,
                `countries_control`.`mstart`,
                `countries_control`.`mstop`,
                `bananaapi-number`.`source`
        ) AS t;";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = [];
        if (sizeof($results) > 0) {
            foreach ($results as $res) {
                // if ($res['country_char'] != "CL") {

                //     continue;
                // }
                $sql = "select s.* FROM (select @mysource:=:source,@country_code:=:country_code,@currentday:=:currentday,@country_id:=:country_id) param, stats s;";

                $stmt = $pdo->prepare($sql);
                $stmt->execute(
                    array("country_code" => $res['country_char'],
                        "source" => $res['source'],
                        "currentday" => $res['createdTime'],
                        "country_id" => $res['country_id']));
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (sizeof($result) > 0) {
                    $result = $result[0];
                    $result['country'] = $res['country'];
                    $result['country_code'] = $res['country_code'];
                    $result['country_char'] = $res['country_char'];
                    $result['status'] = $res['enabled'];
                    $start = strtotime($res['start']);
                    $start = date('h:i a', $start);
                    // $start = DateTime::createFromFormat('h:i A', $res['start']);
                    // $start = $start->format('H:i:s');
                    $stop = strtotime($res['stop']);
                    $stop = date('h:i a', $stop);
                    $result['start'] = $start;
                    $result['stop'] = $stop;
                    $result['Mstart'] = intval($res['mstart']);
                    $result['Mstop'] = intval($res['mstop']);
                    $result['source'] = $res['source'];
                    $result['pushtime'] = $res['createdTime'];
                    $result['servertime'] = date('Y-m-d H:i:s', time());

                    array_push($response, $result);
                    // break;
                }

            }
        }
        // echo json_encode($response);
        // Retrieve existing status data
        $existingStatus = [];
        $selectExisting = "SELECT `country`,`source`, `status` FROM `country_stats1`";
        $existingStmt = $pdo->query($selectExisting);
        while ($row = $existingStmt->fetch(PDO::FETCH_ASSOC)) {
            $existingStatus[$row["country"] . "_" . $row["source"]] = $row['status'];
        }

        $sql = "INSERT INTO `country_stats1` (`country`, `country_char`, `country_code`, `has_sms`, `no_sms`, `requested`, `servertime`, `source`, `start`, `status`, `stop`, `total`, `Mstop`, `Mstart`, `available`,`pushtime`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ";

        if (count($response) > 0) {
            $todel = "truncate table country_stats1";
            $pdo->exec($todel);
            foreach ($response as $key => $country) {
                $status = isset($existingStatus[$country["country"] . "_" . $country["source"]]) ? $existingStatus[$country["country"] . "_" . $country["source"]] : $country["status"];
                // echo $country["country"] . "_" . $country["source"] . " new_status= " . $status . "\n";
                // echo $country["country"] . "_" . $country["source"] . " old_status= " . $country["status"] . "\n------------\n";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $country["country"],
                    $country["country_char"],
                    $country["country_code"],
                    $country["has_sms"],
                    $country["no_sms"],
                    $country["requested"],
                    $country["servertime"],
                    $country["source"],
                    $country["start"],
                    $status,
                    // $country["status"],
                    $country["stop"],
                    $country["total"],
                    $country["Mstop"],
                    $country["Mstart"],
                    $country["available"],
                    $country["pushtime"],
                ]);
            }

        }

    } catch (Exception $e) {
        echo $sql . "<br>";
        echo 'Caught exception: ', $e->getMessage(), "\n";

    }

}
