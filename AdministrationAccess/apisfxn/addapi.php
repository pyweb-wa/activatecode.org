<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (
    isset($_POST['Name']) && isset($_POST['Description']) && isset($_POST['Access_Token']) &&
    isset($_POST['account_balance']) && isset($_POST['Valid'])
) {

    if (!empty($_POST['Name']) && !empty($_POST['Access_Token'])) {
        $name = $_POST['Name'];
        $Description = $_POST['Description'];
        ($_POST['Valid'] == "true" ? $Valid = 1 : $Valid = 0);
        $Access_Token = $_POST['Access_Token'];
        $Refresh_Token = $_POST['Refresh_Token'];
        $balance = (float) $_POST['account_balance']; 
        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("INSERT INTO `foreignapi`( `Name`, `Description`, `Access_Token`, `Refresh_Token`, `Valid`) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $Description, $Access_Token, $Refresh_Token, $Valid, ]);

        $stmt2 = $pdo->prepare(" select Id_Api from  `foreignapi` where `Name`=? and `Access_Token` =? and  `Refresh_Token` =? order by  Id_Api desc limit 1 ");
        $stmt2->execute([$name, $Access_Token, $Refresh_Token]);
        $stmt2->execute();
        $ids = $stmt2->fetchall();
        $apiId = $ids[0]['Id_Api'];
        if ($apiId) {

            $futureDate = date('Y-m-d', strtotime('+1 year'));
            $stmt3 = $pdo->prepare("INSERT INTO `finance_accounts`(  `account_name`, `account_balance`, `reference_api_id`) VALUES (?,?,?)");
            $stmt3->execute([$name, $balance, $apiId]);
        }

        $stmt4 = $pdo->prepare("SELECT `Id_Api` , `Name` , `Description`, `Access_Token`, `Refresh_Token`, `Valid`, 
                                `account_balance`  FROM `foreignapi`  left join finance_accounts on 
                                Id_Api=reference_api_id  WHERE   Id_Api=? ");
        $stmt4->execute([$apiId]);
        $newapi = $stmt4->fetch();
        if ($newapi["Valid"] == "1") {
            $newapi["Valid"] = true;
        } else {
            $newapi["Valid"] = false;
        }
        echo (json_encode($newapi));
        die();
    }
}

echo "something went wrong";
