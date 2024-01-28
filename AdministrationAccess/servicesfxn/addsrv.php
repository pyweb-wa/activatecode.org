<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (
      isset($_POST['Name']) && isset($_POST['code']) && isset($_POST['api_id']) && 
    isset($_POST['country']) &&  isset($_POST['description']) && isset($_POST['price_in'])
    && isset($_POST['price_out']) && isset($_POST['carrier']) && isset($_POST['service_of_api'])
) {

    if (!empty($_POST['Name'])    ) { 
        $name = $_POST['Name'];
        $code= $_POST['code'];
        $country= $_POST['country'];
        $price_in= $_POST['price_in'];
        $price_out= $_POST['price_out'];
        $carrier= $_POST['carrier'];
        $service_of_api= $_POST['service_of_api'];
        $description = $_POST['description'];  
        $api_id=  $_POST['api_id'];   
        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("INSERT INTO `foreignapiservice`  (`Name`,`code`,`price_in`,`price_out`, 
                            `description`,`country`,`carrier`, `service_of_api` ,`Id_Foreign_Api`) values(?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$name, $code, $price_in, $price_out, $description,$country,
                        $carrier,$service_of_api,$api_id]);
 

        $stmt4 = $pdo->prepare("SELECT  `Id_Service_Api`,`Name`,`code`,`price_in`,`price_out`,  `description`,`country`,`carrier`, `service_of_api` FROM 
                               foreignapiservice WHERE `Name`=? and `code`= ? and `price_in`=? and `price_out`=? and 
                            `description`=? and `country`=? and `carrier`=? and  `service_of_api`=?  and `Id_Foreign_Api`=?");
        $stmt4->execute([$name, $code, $price_in, $price_out, $description,$country,$carrier,$service_of_api,$api_id]);
        $newapi = $stmt4->fetch(); 
        echo (json_encode($newapi));
        die();
    }
}

echo "something went wrong";
