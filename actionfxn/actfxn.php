<?php


function get_token($pdo, $user_id)
{
    $stmt3 = $pdo->prepare("SELECT   `access_Token`  FROM `tokens` WHERE userID=? ");
    $stmt3->execute([$user_id]);
    $json = $stmt3->fetchall();
    if ($json[0]) {
        if ($json[0]["access_Token"]) {
             return $json[0]["access_Token"];
        }
    }
    return null;
}
function generateRandomString($length = 6,$withdash=true) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        if($i==3 && $withdash)$randomString .= '-';
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
}
function base64UrlSafeEncode(string $input)
{
   return str_replace(['+', '/'], ['-', '_'], $input);
}
function generate_url($email,$keys){
    $method = 'AES-256-CBC';
    $key = getenv('NAMESPACED_CRYPTO_KEY');
    $length = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($length);
    $pltxt=$email."[***]".$keys;
    $encrypted = openssl_encrypt($pltxt, $method, $key, OPENSSL_RAW_DATA, $iv);
    $ctxt = base64_encode($encrypted) . '|' . base64_encode($iv);
    return "http://" . $_SERVER['SERVER_NAME'] . "/Unlock.php?ENC=" . base64UrlSafeEncode($ctxt); 
}