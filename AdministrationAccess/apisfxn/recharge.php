<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (isset($_POST['fapi_id'])) {
    if (!empty($_POST['fapi_id'])) {
        $Id = $_POST['fapi_id'];
        if (isset($_POST['amount'])) {
            if (!empty($_POST['amount'])) {
                $amount =(float) $_POST['amount'];
                if($amount!=0){
                    require_once './../../backend/config.php';
                    $stmt = $pdo->prepare("UPDATE `finance_accounts` SET `account_balance`=`account_balance`+?
                       where `reference_api_id`=?");
                    $stmt->execute([$amount,$Id]); 
                    $desc='API recharge';
                    if($amount<0)
                    {
                    $desc="Administration reduction";
                    }
                    $stmt5 = $pdo->prepare("INSERT INTO `transaction`( `customerID`, `debit`, `credit`, `description`, `notes`,`fapi_id`,`transDate`) VALUES (0,0,?,?,'',? ,?)");   
                    $stmt5->execute([$amount,$desc,$Id,date("Y-m-d")]); 
                    echo ("{Id_Api:'" . $Id . "'}");
                    die();
                }
            }
        } 
    }
}


if (isset($_POST['admin_id'])) {
    if (!empty($_POST['admin_id'])) {
        $Id = $_POST['admin_id'];
        if (isset($_POST['amount'])) {
            if (!empty($_POST['amount'])) {
                $amount =(float) $_POST['amount'];
                if($amount!=0){
                    require_once './../../backend/config.php';
                    $stmt = $pdo->prepare("UPDATE `cms_users` SET `balance`=`balance`+?
                       where `Id_User`=?");
                    $stmt->execute([$amount,$Id]); 
                    $desc='Admin user recharge';
                    if($amount<0)
                    {
                    $desc="Administration reduction";
                    }
                    $stmt5 = $pdo->prepare("INSERT INTO `transaction`( `customerID`, `debit`, `credit`, `description`, `notes`,`fapi_id`,`transDate`) VALUES (?,?,0,?,'',0,?)");   
                    $stmt5->execute([$Id,$amount,$desc,date("Y-m-d")]); 
                    echo ("{Id_User:'" . $Id . "'}");
                    die();
                }
            }
        } 
    }
}

echo "something went wrong";
