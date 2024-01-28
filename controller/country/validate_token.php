<?php
include '/var/www/smsmarket/html/backend/config.php';

function verifyToken($token) {
    $sql = "SELECT * FROM user_stat WHERE token = :token";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isValid = ($user !== false);
    return $isValid;
}

function checkTokenInDatabase() {
    $token = $_COOKIE['token'] ?? null; 
    if ($token !== null && verifyToken($token)) {
        return true;  
    } 
    return false;  
}


?>
