<?php
require_once '../backend/config.php';

function get_avialable()
{
	try {

		$output = array();
		$current_country = "";
		$current_operator = "";
		$operator_map = array();
		$stmt = $GLOBALS['pdo']->prepare('SELECT country, operator, application, COUNT(*) as count FROM `rutest` WHERE taked =0  GROUP BY country, operator, application');
		$stmt->execute();
		// Build the JSON object
		$countryList = array();
		if ($stmt->num_rows > 0) {
			return array("status" => "NO_NUMBERS");
		}
		while ($row = $stmt->fetch()) {
			$country = $row['country'];
			$operator = $row['operator'];
			$application = $row['application'];
			$appCount = $row['count'];


			if (!array_key_exists($country, $countryList)) {
				$countryList[$country] = array();
			}

			if (!array_key_exists($operator, $countryList[$country])) {
				$countryList[$country][$operator] = array();
			}

			$countryList[$country][$operator][$application] = $appCount;
		}

		$jsonObj = array('country_List' => array());
		foreach ($countryList as $country => $operators) {
			$operatorMap = array();
			foreach ($operators as $operator => $apps) {
				$operatorMap[$operator] = $apps;
			}
			$countryObj = array('country' => $country, 'operatorMap' => $operatorMap);
			array_push($jsonObj['country_List'], $countryObj);
			$jsonObj['status'] = "SUCCESS";
		}

		// Return the JSON object


	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	return $jsonObj;
}

function updateTaked($id, $table)
{
	try {
		#TODO change id dynamic from request
		$stmt = $GLOBALS['pdo']->prepare('UPDATE ' . $table . ' SET `taked`=1 WHERE `id` = ?');
		$stmt->execute([$id]);
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		//$this->logger->Add($e->getMessage(), basename(__FILE__));
	}
}

function get_numbers($obj)
{
	try {
		if (
			!isset($obj["country"]) &&
			!isset($obj["operator"]) &&
			!isset($obj["service"]) &&
			!isset($obj["sum"])
		) {
			echo json_encode(array("status" => "error", "error" => "Missing Keys"));
			die();
		}
		if (
			strlen($obj["country"]) > 10 ||
			strlen($obj["operator"]) > 10 ||
			strlen($obj["service"]) > 10 ||
			strlen($obj["sum"]) > 10
		) {
			echo json_encode(array("status" => "error", "error" => "Long Values "));
			die();
		}
		$country = $obj["country"];
		$service = $obj['service'];
		$operator = $obj['operator'];
		$sum = $obj['sum'];
		$result = array();
		$sql = "SELECT *,(select count(*) from `rutest` where taked = 0 and `country` = :country  and `application` = :application) as total FROM `rutest` where `country` = :country  and `application` = :application and taked = 0 ";

		if ($operator != "any") {
			$sql = "SELECT *,(select count(*) from `rutest` where taked = 0 and `country` = :country and `operator` = :operator and `application` = :application ) as total FROM `rutest` where `country` = :country and `operator` = :operator and `application` = :application and taked = 0 ";
			//$stmt = $GLOBALS['pdo']->prepare( $sql);
			//$stmt->bindParam(':operator', $operator);   
		}

		if (isset($obj['exceptionPhoneSet'])) {
			if (is_array($obj['exceptionPhoneSet'])) {
				if (sizeof($obj['exceptionPhoneSet']) >= 1) {
					foreach ($obj['exceptionPhoneSet'] as $value) {
						$sql .= ' and `phone_number` not like "' . $value . '%" ';
					}
				}
			}
		}
		$sql .= " limit :limit";
		// echo $sql;
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindParam(':operator', $operator);
		$stmt->bindParam(':limit', $sum, PDO::PARAM_INT);
		$stmt->bindParam(':country', $country);
		$stmt->bindParam(':application', $service);
		$stmt->execute();
		$res = $stmt->fetchAll();
		$stmt->closeCursor();
		if (sizeof($res) > 0) {
			if ($sum > $res[0]['total']) {
				echo json_encode(array("status" => "error", "error" => "No enough Numbers"));
				die();
			}
			foreach ($res as  $value) {
				//updateTaked($value['id'], "`rutest`");
				array_push($result, array("status" => "SUCCESS", "number" => $value["phone_number"], "activationId" => $value["id"], "operator" => $value['operator']));
			}
			//return $result;
		}
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		// $this->logger->Add($e->getMessage(), basename(__FILE__));
	}
	return $result;
}

function finish_activation($obj)
{
	$result = new object;
	try {
		if (!isset($obj["activationId"]) && !isset($obj["status"]) && !isset($obj["action"]) && !isset($obj["key"])) {
			echo json_encode(array("status" => "error", "error" => "Missing Keys"));
			die();
		}
		$activationId=$obj["activationId"];
		$status=$obj["status"];
		$action=$obj["action"];
		$key=$obj["key"];


		/*************** D O   S O M T H I N G *********************************/



		$jsonObj['status'] = "SUCCESS";

	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		// $this->logger->Add($e->getMessage(), basename(__FILE__));
	}
	return $result;
}

function push_sms($obj)
{
	$result = new object;
	//bananaapi-results_test
	$smsId = $obj['smsId'];
	try {
		$sql = "select id,phone_number,sms,Sender_n,rutest.application from bananaapi-results_test
		left join rutest
		on rutest.phone_number = bananaapi-results_test.phone_number 
		where id = :id ";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindParam(':id', $smsId);
		$stmt->execute();
		$res = $stmt->fetchAll();
		if (sizeof($res) > 0) {
			$result->smsId = $smsId;
			$result->phoneFrom = $res[0]['application'];
			$result->phone = $res[0]['phone_number'];
			$result->text = $res[0]['sms'];
			$result->action = "PUSH_SMS";
			$result->key = "12345";

			$url = 'http://smshub.org:2052/json';
			$data = array(
				'smsId' => $smsId, 'phoneFrom' => $res[0]['application'],
				'phone' => $res[0]['phone_number'], 'text' => $res[0]['sms'],
				'action' => 'PUSH_SMS', 'key' => '12345',
			);
			$count_req = 0;
			while (true) {
				$count_req++;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$response = curl_exec($ch);
				curl_close($ch);
				$ress = "";
				if (!$response) {
				} else {
					$response_data = json_decode($response, true);

					if (isset($response_data['status'])) {
						$ress = $response_data['status'];
					}
				}
				if ($ress == "SUCCESS") {
					break;
				}
				if ($count_req == 4) {
					break;
				}
				sleep(3);
			}
			updateTaked($smsId, 'bananaapi-result_test');
		} else {
		}
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		// $this->logger->Add($e->getMessage(), basename(__FILE__));
	}
	return $result;
}
function returner($obj){
header('Content-Type: text/html; charset=utf-8');
header('Content-Encoding: gzip');

$compressed = gzencode($obj);
//echo utf8_encode($compressed);
echo ($compressed);
die();

}

$compressed_data = file_get_contents('php://input');
// Decompress the data using gzdecode
$json_data = gzdecode($compressed_data);

// Convert the JSON data to an object using json_decode
$obj = json_decode($json_data, true);

if (isset($obj["key"])) {
	if ($obj["key"] == "ruPass") {
		if (isset($obj["action"])) {
			switch ($obj["action"]) {

				case "GET_SERVICES":
					$result = get_avialable();
					echo 11;
					returner($result);
					header('Content-Type: application/json');
					echo json_encode($result);
					die();
				case "GET_NUMBERS":
					$result = get_numbers($obj);
					header('Content-Type: application/json');
					echo json_encode($result);
					die();
				case "FINISH_ACTIVATION":
					$result = finish_activation($obj);
					header('Content-Type: application/json');
					echo json_encode($result);
					die();
				case "PUSH_SMS":
					$result = push_sms($obj);
					header('Content-Type: application/json');
					echo json_encode($result);
					die();
				default:
					break;
			}
		}
	}
}
