<?php
// $host = '127.0.0.1';
// $db   = 'smsdb';
// $user = 'root';
// $pass = '';
// $charset = 'utf8';
#Server
$host = '192.168.100.20';
$db   = 'smsdb';
$user = 'mixsimverify';
$pass = 'mix@123123';
$charset = 'utf8';

$port = "3306";
$port = "3306";

$hosts = ['192.168.100.2', '192.168.100.1','192.168.100.20'];

$host = $hosts[array_rand($hosts)];
$host = '192.168.100.21';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true, //default false
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY    => true,  //i added it
    PDO::ATTR_TIMEOUT => 90
];
try {
$pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    $log = "[-] ".(string) $e->getMessage(). " ==> script: config.php  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
        $filepath = "/var/www/smsmarket/logging/loggingpdo.log"; 
        file_put_contents($filepath, $log, FILE_APPEND);
    //throw new \PDOException($e->getMessage(), (int)$e->getCode());
    echo '{"ResponseCode":4,"Msg":"The current business is busy, no mobile number is available, please try again later","Result":null}';
    die();
}
