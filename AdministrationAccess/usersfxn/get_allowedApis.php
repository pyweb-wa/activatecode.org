<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

if (isset($_POST['user_id'])) {
    if (!empty($_POST['user_id'])) {
        $user_id = $_POST['user_id']; 
        require_once './../../backend/config.php'; 
        // $stmt = $pdo->prepare("SELECT  Id_Api as 'id' ,Name, '1' as 'checked' from foreignapi left join user_allowed_api on foreignapi.Id_Api = user_allowed_api.api_id  where user_allowed_api.user_id=? And `foreignapi`.`is_deleted` = 0
        //                 UNION
        //                 select  Id_Api as 'id' ,Name, '0' as 'checked' from foreignapi where Id_Api not in (
        //                 select api_id from user_allowed_api WHERE `foreignapi`.`is_deleted` = 0 and user_allowed_api.user_id=?)
        //                  order by id
        //                 ");

        $stmt = $pdo->prepare(" SELECT Id_Api as 'id' ,Name, '1' as 'checked' from foreignapi left join user_allowed_api on foreignapi.Id_Api = user_allowed_api.api_id where user_allowed_api.user_id=? And `foreignapi`.`is_deleted` = 0 UNION select Id_Api as 'id' ,Name, '0' as 'checked' from foreignapi where `foreignapi`.`is_deleted` = 0 And Id_Api not in ( select api_id from user_allowed_api WHERE `foreignapi`.`is_deleted` = 0 and user_allowed_api.user_id=?) order by id
        ");


        $stmt->execute([$user_id, $user_id]);
        $logs = $stmt->fetchall();
        $jarray = [];
        foreach ($logs as $json) {
            if ($json["checked"] == "1") {
                $json["checked"] = '<button  class="btn btn-success" id= "_' . rand() .'" onclick="uncheck(' . $json['id'] . ')" >...</button>';
            } else {
                $json["checked"] = '<button class="btn btn-danger"  id= "_' . rand() .'" onclick="check(' . $json['id'] . ')" >...</button>';
            }

            array_push($jarray, $json);
        }

        echo (json_encode($jarray));
    }
    else{echo"error 1";}
}
else{echo"error 2";}