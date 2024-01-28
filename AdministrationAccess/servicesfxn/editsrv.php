<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (
    isset($_POST['Id_Service_Api']) && isset($_POST['Name']) && isset($_POST['code']) && 
    isset($_POST['country']) &&  isset($_POST['description']) && isset($_POST['price_in'])
    && isset($_POST['price_out']) && isset($_POST['carrier']) && isset($_POST['service_of_api'])
) {

    if (!empty($_POST['Name']) && !empty($_POST['Id_Service_Api'])) {
        $srv_id = (int) $_POST['Id_Service_Api'];
        $name = $_POST['Name'];
        $code= $_POST['code'];
        $country= $_POST['country'];
        $price_in= $_POST['price_in'];
        $price_out= $_POST['price_out'];
        $carrier= $_POST['carrier'];
        $service_of_api= $_POST['service_of_api'];
        $description = $_POST['description'];   

        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("UPDATE `foreignapiservice` SET `Name`=?,`code`= ?,`price_in`=?,`price_out`=?, 
                            `description`=?,`country`=?,`carrier`=?, `service_of_api`=? WHERE `Id_Service_Api`=?");
        $stmt->execute([$name, $code, $price_in, $price_out, $description,$country,
                        $carrier,$service_of_api,$srv_id]);


        



        $stmt4 = $pdo->prepare("SELECT  `Id_Service_Api`,`Name`,`code`,`price_in`,`price_out`,  `description`,`country`,`carrier`, `service_of_api` FROM 
                               foreignapiservice WHERE `Id_Service_Api`=?  ");
        $stmt4->execute([$srv_id]);
        $newapi = $stmt4->fetch(); 
        echo (json_encode($newapi));
        die();
    }
}

echo "something went wrong";
