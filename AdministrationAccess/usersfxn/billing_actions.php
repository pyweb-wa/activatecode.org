<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (!isset($_POST['user_id'])) {
    echo "something went wrong";
    die();
}
if (empty($_POST['user_id'])) {
    echo "something went wrong";
    die();
}
$user_id = $_POST['user_id'];
if (isset($_POST['amount'])) {
    if (!empty($_POST['amount'])) {
        try {
            require_once './../../backend/config.php';
            $amount = (float)$_POST['amount'];
            $gift = (float)$_POST['gift'];
            $is_super = $_SESSION['is_super'];
            $admin_id = $_SESSION['id'];
            // echo 444;die();
            // $_SESSION['balance'] = $_SESSION['balance'] - $amount;
            $stmt = $pdo->prepare("CALL PerformRecharge(?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $amount, PDO::PARAM_STR);
            $stmt->bindParam(3, $gift, PDO::PARAM_STR);
            $stmt->bindParam(4, $is_super, PDO::PARAM_INT);
            $stmt->bindParam(5, $admin_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            

            if ($result['status'] === 'Success') {
                include '/var/www/smsmarket/html/backend/redisconfig.php';
                $balanceKey = "balance:$user_id";
                $redis->incrbyfloat($balanceKey, $amount);
                echo "Success";
            } else {
                echo "Error: " . $result['status'];
            }
        } catch (PDOException $e) { 
            if ($e->getCode() == '45000' && strpos($e->getMessage(), 'NoBalance') !== false) {
                echo "NoBalance";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
        die();
    }
} else if (isset($_POST['get'])) {
    if ($_POST['get'] == 'balance') {
        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("select Balance from users where Id =? ");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
        echo (json_encode($row));
        die();
    } else if ($_POST['get'] == 'history') {
        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("SELECT `recharge_id` as 'id',  `amount`, `recharge_date`, `payment_gatewaycol` as 'gateway' ,`type` FROM `recharge`  ,`payment_gateway` where `recharge`.`gate_id`=`payment_gateway`.`gate_id` and `recharge`.`customer_id`=? order by `recharge_id` desc ");
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll(); //fa fa-gift
        $jarray = [];
        foreach ($rows as $row) {
            if ($row['type'] == 'Gift') {
                $row['type'] = '<button type="button" class="btn btn-info  "><i class="fa fa-gift" aria-hidden="true"></i> Gift</button>';
            } else {
                $row['type'] = '<button type="button" class="btn btn-success  "><i class="fas fa-dollar-sign" aria-hidden="true"></i> Real</button>';
            }
            array_push($jarray, $row);
        }
        echo (json_encode($jarray));
        die();
    }
}



echo "something went wrong";
