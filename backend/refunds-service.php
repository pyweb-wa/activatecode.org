<?php

//require_once 'config.php';

function write_log($message,$_file = "Log")
{
        $log = "[-] ".(string) $message . "  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
        $filepath = "/var/www/smsmarket/logging/refunds_".$_file.".log"; 
	//	echo $filepath;
        file_put_contents($filepath, $log, FILE_APPEND);
}

function refunds(){
	//include 'config.php'; 
	
	try{
		$host = 'localhost';
		$db   = 'smsdb';
		$user = 'mixsimverify';
		$pass = 'mix@123123';
		$charset = 'utf8';
		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => true, //default false
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY    => true  //i added it
		];
		$pdo = new PDO($dsn, $user, $pass, $options);

	$stmt1 = $pdo->prepare("SELECT `Id_request`, `access_Token`,`Id_user` from `requests_log`,`tokens` where `requests_log`.`Status` = ? and `requests_log`.`Id_user` = `tokens`.`userID`AND `requests_log`.`TimeStmp` <= DATE_SUB(NOW(), INTERVAL 5 MINUTE) ");
	$stmt1->execute(["pending"]);
	$results= $stmt1->fetchAll();
	if(sizeof($results)>=1){
	foreach ($results as $res) {
		write_log("refun request_Id: ".$res['Id_request']." for userID: ".$res['Id_user']);
		$stmt = $pdo->prepare('call `get_refund`(?,?) ');
		$stmt->execute([$res['Id_request'], $res['access_Token']]);
		$res = $stmt->fetchAll();
		$stmt->closeCursor();
	//	write_log("refun request_Id: ".$res['Id_request']." for userID: ".$res['Id_user']);
	}
}
	}
	catch(Exception $e) {
		write_log($e->getMessage());
	}

}
# check if multiple RUN First db flag and run this first of all
# edit old channel to insert order only with Pending flag Status
 
write_log("start script");

while(true){
  echo "check for refunds ".time();
  try{
	refunds();
}
catch(Exception $e) {
	write_log($e->getMessage(),"err");
}
    sleep(10);

}

//checkdata();
?>
