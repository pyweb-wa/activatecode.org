<?php

$logPath = "/usr/local/lsws/customers/logging/";

function Receive_SMS($obj){
    require_once '../backend/config.php';
    //$data = array('action' => 'PUSH_SMS', 'phoneFrom' => $value['Sender_n'], 'smsId' => $value['id'], 'text' => utf8_encode($value['sms']), 'key' => $key, "phone" => intval($phone_number));
    if(isset($obj['phoneFrom'],$obj['text'],$obj['smsId'],$obj['phone'],$obj['application'])){

        if (strpos(strtolower($obj["text"]), "whatsapp") !== false 
        || strpos(strtolower($obj["text"]), "can also tap on this link") !== false 
        || strpos(strtolower($obj["sender"]), "whatsapp") !== false) {
                $pattern = '/\d{3}-\d{3}/';
                preg_match($pattern,$obj["text"], $matches);
                if(sizeof($matches) >=1){
                $number = $matches[0];
                $code = str_replace("-", "", $number);
                }
            } else if(strpos(strtolower($obj["text"]), "google") !== false) {
                preg_match_all('/\d+/',$obj["text"], $matches);
                if(sizeof($matches) >=1){
                $code = implode('', $matches[0]);   
                $code = str_replace("-", "",  $code);
                }
            }
            else{
                $new = str_replace(":>4:", "", $obj["text"]);
                $all_digits = preg_replace('/\D/', '', $new);
                $size = strlen($all_digits);
                 if ($size >= 6) {
                    $code = substr($all_digits, 0, 6);
                } else {
                   $code= "";
                }
            }
        try{
        $stmt = $pdo->prepare("INSERT INTO `jikatel_partnerResults`(`phone_number`,  `sms`, `Sender_n`,`code`, `smsId`,`application`) VALUES (:phone_number, :sms, :Sender_n, :code, :smsId, :application)");
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':sms', $sms);
        $stmt->bindParam(':Sender_n', $Sender_n);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':smsId', $smsId);
        $stmt->bindParam(':application', $application);
     
        $phone_number = $obj['phone'];
        $sms = $obj['text'];
        $Sender_n = $obj['phoneFrom'];
        
        $smsId = $obj['smsId'];
        $application = $obj['application'];
        $stmt->execute();
        echo '{"status":"SUCCESS"}';
        }    catch (Exception $e) {
             //   file_put_contents($GLOBALS['logPath']."push_errors.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
               echo 'Caught exception: ',  $e->getMessage(), "\n";
            
        }

    }
    

}
$compressed_data = file_get_contents('php://input');
file_put_contents($logPath."jikatel_partnert.log", "time:" . date('Y-m-d H:i:s') . "=> " . $compressed_data . "\n", FILE_APPEND);
$obj = json_decode($compressed_data, true);

if (isset($obj["action"])) {
	// if (isset($obj["key"])) {
	// 	if ($obj["key"] == "8c203af7fe0f1496d5dfe5567a63301ab49fe3f9"){
			switch ($obj["action"]) {
               
				case "PUSH_SMS":
					$result = Receive_SMS($obj);
                    
					break;
				default:
					break;
			}
	   // }
//    }
}
