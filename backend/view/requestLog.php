<?php 
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
  header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
  die();
}

require_once './../config.php';
$query = "SELECT `Id_request`, `users`.`name` as 'Customer',  `foreignapiservice`.`Name` as 'Service' , `Phone_Nb`, `SMSCode`, `TimeStmp` as 'date',`foreignapi`.`Name` ,`Status`, `sms_content` FROM `requests_log` ,`users` ,`foreignapiservice`,`foreignapi`
where `requests_log`.`Id_user` =`users`.`Id` and  `requests_log`.`service`=`foreignapiservice`.`Id_Service_Api` and `foreignapiservice`.`Id_Foreign_Api` =`foreignapi`.`Id_Api` ";
if($_SESSION["is_super"] == 0)
$query = $query . " and `users`.`admin_id` = " . $_SESSION["id"];
$query= $query ."  ORDER BY date DESC limit 5000";
 $stmt = $pdo->prepare($query);


//$stmt = $pdo->prepare("SELECT `Id_request`, `users`.`name` as 'Customer',  `foreignapiservice`.`Name` as 'Service' , `Phone_Nb`, `SMSCode`, `sms_content`,`Status`, `TimeStmp` as 'date' FROM `requests_log` ,`users` ,`foreignapiservice` 
  //  ");

$stmt->execute();
$logs = $stmt->fetchall();
$json = json_encode($logs);
echo ($json);
