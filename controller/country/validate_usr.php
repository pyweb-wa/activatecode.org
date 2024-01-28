<?php
include '/var/www/smsmarket/html/backend/config.php';

$data = json_decode(file_get_contents("php://input"));

if ($data && isset($data->user) && isset($data->password)) {
    $user = $data->user;
    $password = $data->password; 
    $sql = "SELECT * FROM user_stat WHERE username = :user";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->bindParam(':user', $user, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
    if ($user && password_verify($password, $user['passw'])) {
        $token = $user['token']; 
        echo json_encode(['token' => $token]);
    } else {
        http_response_code(401); 
        echo json_encode(['error' => 'Invalid credentials']);
    }
} else {
    http_response_code(400); 
    echo json_encode(['error' => 'Invalid or missing data']);
}


?>
