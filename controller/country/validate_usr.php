<?php
// include '/var/www/smsmarket/html/backend/config.php';
// $data = json_decode(file_get_contents("php://input"));
// if ($data && isset($data->user) && isset($data->password)) {
//     $user = $data->user;
//     $password = $data->password; 
//     $sql = "SELECT * FROM user_stat WHERE username = :user";
//     $stmt = $GLOBALS['pdo']->prepare($sql);
//     $stmt->bindParam(':user', $user, PDO::PARAM_STR);
//     $stmt->execute();
//     $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
//     if ($user && password_verify($password, $user['passw'])) {
//         $token = $user['token']; 
//         echo json_encode(['token' => $token]);
//     } else {
//         http_response_code(401); 
//         echo json_encode(['error' => 'Invalid credentials']);
//     }
// } else {
//     http_response_code(400); 
//     echo json_encode(['error' => 'Invalid or missing data']);
// }
?>

<?php

include '/var/www/smsmarket/html/backend/config.php';

$data = json_decode(file_get_contents("php://input"));

$ip_address = '';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip_address = $_SERVER['REMOTE_ADDR'];
}

$country = getCountry();
$user_id = 0;
$user_token = '';
$email = '';
$cpanel = 'ControllerPanel';
$state = ''; 

if ($data && isset($data->user) && isset($data->password)) {
    $user = $data->user;
    $email = $user ;
    $password = $data->password; 

    $sql = "SELECT * FROM user_stat WHERE username = :user";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->bindParam(':user', $user, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['passw'])) {
        $user_id = $user['id']; 
        $user_token = $user['token']; 
        $state = 'Login Successful';
        $insertStmt = $pdo->prepare("INSERT INTO `login_log` (`ip`, `country`, `user_id`, `user_token`, `email`, `cpanel`, `state`)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");

        $insertStmt->execute([$ip_address, $country, $user_id, $user_token, $email, $cpanel, $state]);

        echo json_encode(['token' => $user_token]);
        die();
    } else {
        $state = 'Invalid Credentials';

        http_response_code(401); 
        echo json_encode(['error' => 'Invalid credentials']);
    }
} else {
    $state = 'Invalid or missing data';

    http_response_code(400); 
    echo json_encode(['error' => 'Invalid or missing data']);
}
$insertStmt = $pdo->prepare("INSERT INTO `login_log` (`ip`, `country`, `user_id`, `user_token`, `email`, `cpanel`, `state`)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
$insertStmt->execute([$ip_address, $country, $user_id, $user_token, $email, $cpanel, $state]);
function getCountry()
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
    $country = "";
    try {
        $url = "https://ipinfo.io/" . $ip_address;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $output1 = json_decode($output, true);
        
        if (isset($output1["country"])) {
            $country =  $output1["country"];
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    return $country;
}
?>
