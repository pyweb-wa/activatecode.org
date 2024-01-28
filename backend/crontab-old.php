<?php
require_once 'inAPI/old-channels_class.php';
require_once 'config.php';
$oldchannels = new old_channels();

function start_callingAPI($value){
    $while = true;
    $count = 50;
    $cn=0;
    
	write_log("Start loop cn= ".$cn.PHP_EOL);
    $file = "/var/www/html/oldChannels/".$value['srv_req_id'];
    while($while){

		 try {
		
		//new select 
		$stmt0 = $GLOBALS["pdo"]->prepare('SELECT * FROM `channels_log` WHERE  srv_req_id= ?');
				$stmt0->execute([$value['srv_req_id']]);
				$channel_log_row = $stmt0->fetch();
				$status=$channel_log_row['Status'];

		if(strcmp($status,'pending')!==0)break;

		if (intval($channel_log_row['quantitydone']) >= intval($channel_log_row['quantity'])){
			$while = false;
			break;
		}  
		$datalen = intval($channel_log_row['quantitydone']);
		$count = intval($channel_log_row['quantity']) - intval($channel_log_row['quantitydone']);
		
		write_log( "count = ".$count);
				if($count >50) $count =50;
    
		$GLOBALS['oldchannels']->askforchannels2($channel_log_row['srv_req_id'],$count,$channel_log_row['info']);

		if(file_exists($file)){
			$data = file_get_contents($file);
			$data = json_decode($data,true);
			if(isset($data['Data']))
			{
				$datalen = sizeof($data['Data']); 
				
				if ($datalen >= intval($channel_log_row['quantity'])){
					$while = false;
					//break;
				}            
				
			}
		}
	
		write_log("request data from api ".PHP_EOL);
		// $GLOBALS['oldchannels']->askforchannels2($value['srv_req_id'],$count,$value['info']);
		$query = "Update `channels_log` Set quantitydone =  ? where `srv_req_id`= ?";
			$stmt = $GLOBALS["pdo"]->prepare($query);
			$stmt->execute([$datalen,$channel_log_row['srv_req_id']]);

		//ERROR last one will not accumulate quantity
		//ERROR if set finished using web page
		if($while) sleep(40);
		$cn +=1;
		if($cn >=50) $while = false;
	}



catch(Exception $e) {
	write_log($e->getMessage());
}
}
}

function write_log($message)
{
        $log = "[-] ".(string) $message . "  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
        $filepath = dirname(__FILE__) . "/logging/oldChannels.log"; 
		echo $filepath;
        file_put_contents($filepath, $log, FILE_APPEND);
}

function checkdata(){  
	try{	
    $query = "SELECT * FROM `channels_log` WHERE Status = 'pending' ";
        $stmt = $GLOBALS["pdo"]->prepare($query);
        $stmt->execute([]);
        $items = $stmt->fetchall();      
		
        foreach ($items as  $value) {
			write_log( "Start in ".$value['info']."==> ".$value['quantity'].PHP_EOL);
            
            start_callingAPI($value);
            //print_r($value);
			finish_request($value);
		}	

	}
		catch(Exception $e) {
			write_log($e->getMessage());
	}
}	
function finish_request($value){
	try{
			$query = "Update `channels_log` Set Status = 'Finished' where `srv_req_id`= ?";
			$stmt = $GLOBALS["pdo"]->prepare($query);
			$stmt->execute([$value['srv_req_id']]);
			#get done with value id
			#get customer id 
			##get price of each
			#update balance in users
			#if session exists update
			#insert reverse transaction for apis 
			#insert reverse transaction for customer
			#
			$user_id=$value['Id_user'];
			$quantitydone=$value['quantitydone'];
			$requested_quantity=$value['quantity'];
			$quantity_difference=$requested_quantity-$quantitydone;
			if($quantity_difference>0) //need to refund 
			{
				$stmt0 = $GLOBALS["pdo"]->prepare('  SELECT `price_in`, `price_out`   from `channels_api`  limit 1;  ');
				$stmt0->execute();
				$channels_api = $stmt0->fetch();
				$out_price= $channels_api['price_out'];
				$in_price= $channels_api['price_in'];
				$refund_amount=floatval($channels_api['price_out'])*$quantity_difference;
				//set session [balance]
				if (isset($_SESSION['balance'])) { $_SESSION['balance'] = floatval($_SESSION['balance'] ) + floatval($refund_amount);  }
				//set baance in db
				$stmt2 = $GLOBALS["pdo"]->prepare("update  `users` set `Balance`=`Balance`+? WHERE `Id` =? ");
                $stmt2->execute([$refund_amount, $user_id]);
				//refund from fapi account ==>old_channels id is 16
				$stmt3 = $GLOBALS["pdo"]->prepare("INSERT INTO `transaction`( `customerID`, `debit`, `credit`, `description`, `notes`,`fapi_id`,TransDate) VALUES (0,0,?,?,'',16,CURRENT_TIMESTAMP )");   
                $stmt3->execute([$refund_amount,'old channels refund '.$quantity_difference.' channels']); 
				//refund to customer 
				$stmt4 = $GLOBALS["pdo"]->prepare("INSERT INTO `transaction`( `customerID`, `debit`, `credit`, `description`, `notes`,`fapi_id`,TransDate) VALUES (?,?,0,?,'',0 ,CURRENT_TIMESTAMP)");   
                $stmt4->execute([$user_id,$refund_amount,'old channels refund '.$quantity_difference.' channels']); 				
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
	checkdata();
   
    sleep(5);

}

//checkdata();
?>
