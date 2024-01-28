<?php
session_start();
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {

//     // Build POST request:
//     $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
//     $recaptcha_secret = '6LdjHc0ZAAAAAJel7Zwoula9x8dGtH_jGl2qA2mk';
//     $recaptcha_response = $_POST['recaptcha_response'];

//     // Make and decode POST request:
//     $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
//     $recaptcha = json_decode($recaptcha);

//     // Take action based on the score returned:
//     if (isset($recaptcha->score)|| $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == 'localhost'){


//     if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $recaptcha->score >= 0.5 || $_SERVER['REMOTE_ADDR'] == 'localhost') {

if (isset($_POST['email']) && isset($_POST['password'])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];

        if (strlen($email) > 63  || (strpos($email, '@') == false)) {

            header('Location:login.php');
            die();
        }
        $passwd =  password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {                
            require_once './../backend/config.php';
            $stmt = $pdo->prepare("SELECT `Id_User`,`email`,`Passwd`,`Name`,`is_super`,`token`,`balance` FROM `cms_users` WHERE `email` =?");
            $stmt->execute([$email]);
            $json = $stmt->fetch();
            if ($json["Passwd"]) {
                if ($email == $json["email"] && password_verify($_POST['password'], $json["Passwd"])) {
                    $_SESSION['id'] = $json["Id_User"];
                    $_SESSION['valid'] = true;
                    $_SESSION['timeout'] = time();
                    $_SESSION['email'] = $email;
                    $_SESSION['level'] = "rootlevel";
                    $_SESSION['name'] = $json["Name"];
                    $_SESSION['is_super'] = $json["is_super"];
                    $_SESSION['token'] = $json["token"];
                    $_SESSION['balance'] = $json["balance"];
                    header('Location:users.php');
                    die();
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();  
            header('Location:login.php');
            die();
        }
    }
}
 
header('Location:login.php');
die();


function checkip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }
    //whether ip is from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    //whether ip is from remote address
    else {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }

    try {
        $url = "https://ipinfo.io/" . $ip_address;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $output1 = json_decode($output, true);
        $country = "";
        if (isset($output1["country"])) {
            $country =  $output1["country"];
        }
        if ($country == "LB" || $country == "MY") {
            return True;
        } elseif (isset($output1["bogon"])) {
            if ($output1["bogon"] == 1) {
                return True;
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage();

        //$this->logger->Add($e->getMessage(), basename(__FILE__));
    }
    return False;
}
