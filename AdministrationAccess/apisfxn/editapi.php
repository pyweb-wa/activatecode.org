<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


if (
    isset($_POST['Id_Api']) && isset($_POST['Name']) && isset($_POST['Description']) && isset($_POST['Access_Token']) &&
    isset($_POST['account_balance']) && isset($_POST['Valid'])
) {

    if (!empty($_POST['Name']) && !empty($_POST['Id_Api'])) {
        $apiId = (int) $_POST['Id_Api'];
        $name = $_POST['Name'];
        $Description = $_POST['Description'];
        ($_POST['Valid'] == "true" ? $Valid = 1 : $Valid = 0);
        $Access_Token = $_POST['Access_Token'];
        $Refresh_Token = $_POST['Refresh_Token'];
        $balance = (float) $_POST['account_balance'];  
        require_once './../../backend/config.php';
        $stmt = $pdo->prepare("UPDATE   `foreignapi`set `Name` =? , `Description`=?, `Access_Token`=?, `Refresh_Token`=?, `Valid`=?  where `Id_Api`=?");
        $stmt->execute([$name, $Description, $Access_Token, $Refresh_Token, $Valid, $apiId]);


        $stmt3 = $pdo->prepare("UPDATE   `finance_accounts`  set   `account_balance` =?  ,`account_name`=? where `reference_api_id`=?");
        $stmt3->execute([$balance, $name, $apiId]);



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
