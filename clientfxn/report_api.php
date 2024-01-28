<?php 
session_start();
if (!isset($_SESSION['name'])) {
  header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
  die();
}

if(isset($_POST["report"])){
  require_once './../backend/config.php';

    $stmt = $pdo->prepare("SELECT `Id_request`, `users`.`name` as 'Customer',  `foreignapiservice`.`Name` as 'Service' , `Phone_Nb`, `SMSCode`, `sms_content`,`Status`, `TimeStmp` as 'date' FROM `requests_log` ,`users` ,`foreignapiservice` 
    where `requests_log`.`Id_user` =`users`.`Id` and  `requests_log`.`service`=`foreignapiservice`.`Id_Service_Api` AND `users`.`Id`=?; limit 5000");
   
   
   //$stmt = $pdo->prepare("SELECT `Id_request`, `users`.`name` as 'Customer',  `foreignapiservice`.`Name` as 'Service' , `Phone_Nb`, `SMSCode`, `sms_content`,`Status`, `TimeStmp` as 'date' FROM `requests_log` ,`users` ,`foreignapiservice` 
     //  ");
   
   $stmt->execute([$_SESSION['id']]);
   $logs = $stmt->fetchall();
   $json = json_encode($logs);
   echo ($json);
}

