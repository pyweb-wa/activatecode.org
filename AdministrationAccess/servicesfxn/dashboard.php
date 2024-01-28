<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (isset($_POST['requestCountperDate'])) {
    require_once './../../backend/config.php';

    $GLOBALS['pdo'] = $pdo;
    requestCountperDate();
    die();
}
if (isset($_POST['getdailyProfits'])) {
    require_once './../../backend/config.php';

    $GLOBALS['pdo'] = $pdo;
    dailyProfit();
    die();
}



require_once './../backend/config.php';

$GLOBALS['pdo'] = $pdo;



function total_apibalance()
{
    $stmt = $GLOBALS['pdo']->prepare("SELECT SUM(account_balance) AS value_sum FROM finance_accounts,foreignapi where foreignapi.is_deleted = 0 and `foreignapi`.`Id_Api` = `finance_accounts`.`reference_api_id` ");
    $stmt->execute();
    $row = $stmt->fetch();
    $sum = $row['value_sum'];
    return $sum;
}


function total_userbalance()
{
    $query = "SELECT SUM(Balance) AS balance FROM users where `users`.`is_deleted` =0";
    if($_SESSION["is_super"] == 0)
    $query = $query . " and admin_id=" . $_SESSION["id"];
    
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    $sum = $row['balance'];
    return $sum;
}

function total_Profits()
{
    if ($_SESSION["is_super"] == 0) {
        $query = "SELECT  (
    SELECT SUM(debit)  FROM `transaction` WHERE `description` LIKE '%Refund%'  and customerID IN (select id from users where admin_id = " . $_SESSION["id"] . ")
        ) AS debit,
        (
            SELECT SUM(credit)  FROM `transaction`  where customerID IN (select id from users where admin_id = " . $_SESSION["id"] . ")
        ) AS credit,
        (
            SELECT SUM(debit)  FROM `transaction` WHERE `description`  LIKE '%recharge Real%'  and customerID IN (select id from users where admin_id = " . $_SESSION["id"] . ")
        ) AS recharge
        
        ";
    } else {
        $query = "SELECT  (
            SELECT SUM(debit)  FROM `transaction` WHERE `description` LIKE '%Refund%'
                ) AS debit,
                (
                    SELECT SUM(credit)  FROM `transaction` 
                ) AS credit,
                (
                    SELECT SUM(debit)  FROM `transaction` WHERE `description`  LIKE '%recharge Real%' 
                ) AS recharge
                
                ";
    }
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    $profits = $row["credit"] - $row["debit"];
    $f = floatval($row["recharge"])  - $profits;
    $total = $profits . '/(' . $f . ')';
    return $total;
}

function requestCountperDate()
{
    $query = "SELECT DATE_FORMAT(TimeStmp, '%Y') as 'year', DATE_FORMAT(TimeStmp, '%m') as 'month', DATE_FORMAT(TimeStmp, '%d') as 'day', COUNT(Id_request) as 'total' FROM requests_log ";

    if ($_SESSION["is_super"] == 0)
        $query = $query . " where id_user IN (select id from users where admin_id = " . $_SESSION["id"] . ")";

    $query = $query . " GROUP BY  DATE_FORMAT(TimeStmp, '%Y'), DATE_FORMAT(TimeStmp, '%m'), DATE_FORMAT(TimeStmp, '%d') ";

    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $row = $stmt->fetchall();
    // var_dump($row );
    $date = array();
    $count = array();

    foreach ($row as $key) {
        //echo "{$key} => {$value} ";
        //echo $key['year'];
        array_push($date, $key['year'] . "-" . $key['month'] . "-" . $key['day']);
        array_push($count, $key['total']);
    }
    $myObj = new stdClass();
    $myObj->date = false;
    $myObj->count = false;
    $myObj->date = $date;
    $myObj->count = $count;

    $myJSON = json_encode($myObj);
    echo $myJSON;
}
function dailyProfit()
{
    $query = "SELECT DATE_FORMAT(transDate, '%Y') as 'year', DATE_FORMAT(transDate, '%m') as 'month', DATE_FORMAT(transDate, '%d') as 'day', sum(credit-debit) as 'total' FROM transaction ";
    if ($_SESSION["is_super"] == 0)
        $query = $query . " where customerID  IN (select id from users where admin_id = " . $_SESSION["id"] . ")";

    $query = $query . " GROUP BY DATE_FORMAT(transDate, '%Y'),DATE_FORMAT(transDate, '%m'),DATE_FORMAT(transDate, '%d')";

    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $row = $stmt->fetchall();
    // var_dump($row );
    $date = array();
    $count = array();

    foreach ($row as $key) {
        //echo "{$key} => {$value} ";
        //echo $key['year'];
        array_push($date, $key['year'] . "-" . $key['month'] . "-" . $key['day']);
        array_push($count, $key['total']);
    }
    $myObj = new stdClass();
    $myObj->date = false;
    $myObj->count = false;
    $myObj->date = $date;
    $myObj->count = $count;

    $myJSON = json_encode($myObj);
    echo $myJSON;
}
function Total_Customers()
{
    $query = "SELECT COUNT(*) as customer_count FROM `users` WHERE is_deleted=0 ";
    if ($_SESSION["is_super"] == 0)
        $query =  $query . " and admin_id = " . $_SESSION["id"];

    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    $customer_count = $row["customer_count"];

    return $customer_count;
}

function top_used_apps()
{
    $query = "SELECT foreignapiservice.Name as 'app' ,country_name as 'country'  , foreignapi.Name as 'API' 
    ,count(Id_request) as 'count' FROM `foreignapiservice` ,`foreignapi`,`requests_log` WHERE 
    `requests_log`.`service` =`foreignapiservice`.`Id_Service_Api` and 
    `foreignapiservice`.`Id_Foreign_Api`=`foreignapi`.`Id_Api` 
    GROUP by app,country_name ,API order by count desc limit 10";
    // app API count country
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchall();
    return $rows;
}
