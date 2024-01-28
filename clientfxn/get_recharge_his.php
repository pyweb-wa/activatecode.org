<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

require_once './../backend/config.php';
$user_id = (int) $_SESSION['id'];
if ($user_id == 0) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

$stmt = $pdo->prepare("SELECT `recharge_id` as 'id',  `amount`, `recharge_date`, `payment_gatewaycol` as 'gateway' ,`type` FROM `recharge`  ,`payment_gateway` where `recharge`.`gate_id`=`payment_gateway`.`gate_id` and `recharge`.`customer_id`=? order by `recharge_id` desc ");
$stmt->execute([$user_id]); 
$rows=$stmt->fetchAll(); //fa fa-gift
$jarray = [];
foreach($rows as $row)
{
    if($row['type'] =='Gift')
    {
    $row['type'] = '<button type="button" class="btn btn-info  "><i class="fa fa-gift" aria-hidden="true"></i> Gift</button>';
    }else{
        $row['type'] = '<button type="button" class="btn btn-success  "><i class="fas fa-dollar-sign" aria-hidden="true"></i> Real</button>';
    }
    array_push($jarray, $row);
}
echo (json_encode($jarray));
die();