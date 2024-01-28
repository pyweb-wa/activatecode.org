<?php  
    if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
        header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
        die();
    }

function get_trans_by_user($userid)
{
    require_once './../backend/config.php';
    //SELECT `transactionID` as 'tid', `name`, `debit`, `credit`, `description`, `notes`, `transDate`  FROM `transaction` ,`users` WHERE `transaction`.`customerID` =`users`.`Id`
    $stmt = $pdo->prepare("SELECT `transactionID` as 'tid', `name`, `debit`, `credit`, `description`, `notes`, `transDate` as 'date'  FROM `transaction` ,`users` WHERE `transaction`.`customerID` =`users`.`Id`
    and `users`.`Id`=?");
    $stmt->execute([$userid]);
    $trans=$stmt->fetchAll();
    return $trans;
}

function get_trans_by_api($apiID)
{
    require_once './../backend/config.php';
    //SELECT `transactionID` as 'tid', `name`, `debit`, `credit`, `description`, `notes`, `transDate`  FROM `transaction` ,`users` WHERE `transaction`.`customerID` =`users`.`Id`
    $stmt = $pdo->prepare("SELECT `transactionID` as 'tid',Name as 'name' ,`debit`, `credit`, `transaction`.`description`, `notes`, `transDate`  as 'date' FROM `transaction` , `foreignapi` WHERE `transaction`.`fapi_id`=`foreignapi`.`Id_Api` and `fapi_id`=?");
    $stmt->execute([$apiID]);
    $trans=$stmt->fetchAll();
    return $trans;
}

function get_trans_by_date($month,$year)
{

    require_once './../backend/config.php';
    //SELECT `transactionID` as 'tid', `name`, `debit`, `credit`, `description`, `notes`, `transDate`  FROM `transaction` ,`users` WHERE `transaction`.`customerID` =`users`.`Id`
    $stmt = $pdo->prepare("SELECT `transactionID` as 'tid',Name as 'name' ,`debit`, `credit`, `transaction`.`description`, `notes`, `transDate` as 'date' ,'API' as 'type' FROM `transaction` , `foreignapi` WHERE `transaction`.`fapi_id`=`foreignapi`.`Id_Api` and  MONTH(transDate)=? and YEAR(transDate)=? ");
    $stmt->execute([$month,$year]); 
    $trans1=$stmt->fetchAll();

    $stmt2 = $pdo->prepare("SELECT `transactionID` as 'tid', `name`, `debit`, `credit`, `description`, `notes`, `transDate` as 'date'  ,'Customer' as 'type' FROM `transaction` ,`users` WHERE `transaction`.`customerID` =`users`.`Id` and  MONTH(transDate)=? and YEAR(transDate)=? ");
    $stmt2->execute([$month,$year]);
    $trans2=$stmt2->fetchAll();
    
    return $trans1+$trans2;
}
