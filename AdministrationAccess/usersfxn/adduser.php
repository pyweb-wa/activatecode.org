<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

require_once './../../backend/mylogger.php';
$logger = new MyLogger();
$logger->Add("test", basename(__FILE__));

if (
    isset($_POST['email'])
    && isset($_POST['name'])
    && isset($_POST['Valid'])
    && isset($_POST['Is_Activated'])
    && isset($_POST['access_Token'])
) {
    if (
        !empty($_POST['email'])
        && !empty($_POST['Valid'])
        && !empty($_POST['Is_Activated'])

    ) {
        try {



            $name = $_POST['name'];
            $email = $_POST['email'];

            ($_POST['Valid'] == "true" ? $Valid = 1 : $Valid = 0);
            ($_POST['Is_Activated'] == "true" ? $Is_Activated = 1 : $Is_Activated = 0);
            $access_token = $_POST['access_Token'];

            if ($access_token == "empty")
                $access_token = "";
            else if ($access_token == "generate new") {
                $access_token = hash('sha256', generateRandomString() . time());
            } else {
                $access_token = "";
                // echo "1- something went wrong";
                // die();
            }
            $registration_date = date("Y-m-d H:i:s");
            require_once './../../backend/config.php';
            $stmt = $pdo->prepare("INSERT INTO `users`(  `email`,`name`, `registration_date`, `activation_key`, `Is_Activated`, `Balance`,`admin_id`) VALUES (  ?, ?, ?, ?, ?,?,? ) ");
            $stmt->execute([$email, $name, $registration_date, "0000", $Is_Activated, 0, $_SESSION["id"]]);
            $userId = $pdo->lastInsertId();


            if ($userId) {

                $futureDate = date('Y-m-d', strtotime('+1 year'));
                $stmt3 = $pdo->prepare("INSERT INTO `tokens`( `userID`, `access_Token`, `expiry_date`, `Valid`, `Tag`) VALUES (?,?,?,?,?)");
                $stmt3->execute([$userId, $access_token, $futureDate, 1, " "]);

                // add user permissions on all countries and jkatel api
                $stmt = $pdo->prepare("SELECT DISTINCT(country_id) FROM countries_control;");
                $stmt->execute([]);
                $countries = $stmt->fetchall();
                if (count($countries) > 0) {
                    $stmt = $pdo->prepare("INSERT INTO user_countries (user_id, country_id) VALUES (?, ?)");
                    foreach ($countries as $country_id) {
                        $stmt->bindValue(1, $userId);
                        $stmt->bindValue(2, $country_id['country_id']);
                        $stmt->execute();
                    }
                }
                $stmt2 = $pdo->prepare("INSERT INTO user_allowed_api (user_id, api_id) VALUES (?, ?)");

                $stmt2->bindValue(1, $userId);
                $stmt2->bindValue(2, 17);
                $stmt2->execute();
            }


            $stmt4 = $pdo->prepare("SELECT `Id`,`email`,`name`, `registration_date`, `activation_key`, `Is_Activated`, `Balance`
        , `access_Token`, `expiry_date`, `Valid`, `Tag`
        from users left join tokens   on users.Id = tokens.userID where users.Id=? ");
            $stmt4->execute([$userId]);
            $newuser = $stmt4->fetch();
            if ($newuser["Valid"] == "1") {
                $newuser["Valid"] = true;
            } else {
                $newuser["Valid"] = false;
            }
            if ($newuser["Is_Activated"] == "1") {
                $newuser["Is_Activated"] = true;
            } else {
                $newuser["Is_Activated"] = false;
            }
            echo (json_encode($newuser));
            die();
        } catch (Exception $e) {
            $logger->Add($e->getMessage(), basename(__FILE__));
        }
    }


}

echo "2- something went wrong";

function generateRandomString($length = 10)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}
