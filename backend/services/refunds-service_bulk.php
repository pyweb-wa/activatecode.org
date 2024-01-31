<?php

//require_once 'config.php';

function write_log($message, $_file = "Log")
{
	$log = "[-] " . (string) $message . "  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
	$filepath = "/var/www/smsmarket/logging/refunds_" . $_file . ".log";
	//	echo $filepath;
	file_put_contents($filepath, $log, FILE_APPEND);
}

function balance($userId,$amount){

	include '/var/www/smsmarket/html/backend/redisconfig.php';
	$balanceKey = "balance:$userId";
	$newBalance = $redis->incrbyfloat($balanceKey, $amount);
	return $newBalance; 

}

function Update_request_logs($service, $Id_user)
{
	try {
		include '/var/www/smsmarket/html/backend/config.php';

		// Fetch pricing data
		$sqlPricing = "SELECT `price_in`, `price_out`, `country`, `carrier`, `Id_Foreign_Api`, `foreignapiservice`.`Id_Service_Api` as service
            FROM `foreignapiservice`
            WHERE `Id_Service_Api` = :service LIMIT 1;";

		$pricingStmt = $pdo->prepare($sqlPricing);
		$pricingStmt->bindParam(':service', $service);
		$pricingStmt->execute();
		$pricingdata = $pricingStmt->fetch(PDO::FETCH_ASSOC);

		// Check if pricing data exists
		if (!$pricingdata) {
			return;
		}
	
		// Fetch Ids from requests_log
		$sqlIds = "SELECT Id_request
            FROM `requests_log`
            WHERE Id_user = :Id_user
            AND service = :service
            AND Status = 'pending'
            AND `requests_log`.`TimeStmp` <= DATE_SUB(NOW(), INTERVAL 6 MINUTE)
            GROUP BY Id_request;";

		$selectStmt = $pdo->prepare($sqlIds);
		$selectStmt->bindParam(':Id_user', $Id_user, PDO::PARAM_INT);
		$selectStmt->bindParam(':service', $service);
		$selectStmt->execute();
		$allIds = $selectStmt->fetchAll(PDO::FETCH_COLUMN);
		sort($allIds);

		// Update records in batches
		$batchSize = 200;
		$offset = 0;
		$maxRetries = 10;
		$all = count($allIds);

		// echo $all;
		// die();

		while ($offset < count($allIds)) {
			$idsInBatch = array_slice($allIds, $offset, $batchSize);

			if (!empty($idsInBatch)) {
				$retryCount = 0;

				// Retry loop in case of deadlock
				while ($retryCount <= $maxRetries) {
					try {
						// var_dump($idsInBatch);
						// die();
						// Update records in requests_log
						$placeholders = implode(',', array_fill(0, count($idsInBatch), '?'));
						$updateSql = "UPDATE `requests_log` SET `Status` = 'Expired' WHERE Id_request IN ($placeholders)";
						$updateStmt = $pdo->prepare($updateSql);
						$updateStmt->execute($idsInBatch);
						$all = $all - $batchSize;
						echo "Updated requests_log " . count($idsInBatch) . " records " . $all . "\n";

						// Update User Balance
						$refunds = intval($pricingdata['price_out']) * count($idsInBatch);
						$updateBalanceSql = "UPDATE `users` SET `balance` = `balance` + :refunds WHERE `Id` = :Id_user";
						$updateBalanceStmt = $pdo->prepare($updateBalanceSql);
						$updateBalanceStmt->bindParam(':refunds', $refunds, PDO::PARAM_INT);
						$updateBalanceStmt->bindParam(':Id_user', $Id_user, PDO::PARAM_INT);
						$updateBalanceStmt->execute();
						balance($Id_user,$refunds);
						echo "Refunded $refunds to User $Id_user\n";

						// Insert into Transaction Table
						$desc = "Refund " . count($idsInBatch) . " numbers from service $service " . $pricingdata['country'] . "-";
						$transactionSql = "INSERT INTO `transaction`(`customerID`, `debit`, `credit`, `description`, `notes`,`transDate`)
                            VALUES(:Id_user, :price_out, 0, :desc, ' ',NOW())";

						// Prepare and execute the statement
						$transactionStmt = $pdo->prepare($transactionSql);
						$transactionStmt->bindParam(':Id_user', $Id_user, PDO::PARAM_INT);
						$transactionStmt->bindParam(':price_out', $refunds, PDO::PARAM_INT);
						$transactionStmt->bindParam(':desc', $desc);
						$transactionStmt->execute();
						// Get the last inserted ID
						$lastInsertedId = $pdo->lastInsertId();

						echo "Last Inserted ID: $lastInsertedId\n";
						break;
					} catch (PDOException $e) {
						if ($e->getCode() == 40001) {
							$retryCount++;
							echo "Deadlock detected, retrying (attempt $retryCount)...\n";
							usleep(500000);
						} else {
							throw $e;
						}
					}
				}

				$offset += $batchSize;
			}
		}

		echo "Operation completed successfully.\n";
	} catch (PDOException $e) {
		$filepath = "/var/www/smsmarket/logging/delete_data.log";
		$log = "[+] error datetime: " . date('m/d/Y h:i:s a', time()) . " " . $e->getMessage() . "\n";
		file_put_contents($filepath, $log, FILE_APPEND);

		echo "Error: " . $e->getMessage();
	}
}




function refunds()
{
	//include 'config.php'; 

	try {
		//include '/var/www/smsmarket/html/backend/config.php'; 
		$host = 'localhost';
		$db   = 'smsdb';
		$user = 'mixsimverify';
		$pass = 'mix@123123';
		$charset = 'utf8';
		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => true, //default false
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY    => true  //i added it
		];
		$pdo = new PDO($dsn, $user, $pass, $options);
		$stmt1 = $pdo->prepare("SELECT
							`requests_log`.service,
							`requests_log`.Id_user
								FROM
									`requests_log`
								WHERE
									Status = 'pending' AND
									`requests_log`.`TimeStmp` <= DATE_SUB(NOW(), INTERVAL 6 MINUTE)
									-- AND Id_user = 28
								GROUP BY
									`requests_log`.service, `requests_log`.Id_user; ");
		$stmt1->execute([]);
		$results = $stmt1->fetchAll();

		echo json_encode($results)."\n";;
		//	die();
		if (sizeof($results) >= 1) {

			foreach ($results as $res) {

				Update_request_logs($res['service'], $res['Id_user']);
			}
		}
	} catch (Exception $e) {
		echo $e->getMessage();
		write_log($e->getMessage());
	}
}
# check if multiple RUN First db flag and run this first of all
# edit old channel to insert order only with Pending flag Status

write_log("start script");
// refunds();
// die();
while (true) {
	echo "check for refunds " . time();
	try {
		refunds();
	} catch (Exception $e) {
		write_log($e->getMessage(), "err");
	}
	sleep(10);
}

//checkdata();
