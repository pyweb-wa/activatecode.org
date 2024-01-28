<?php


require_once '../backend/config.php';

$valreq = $_POST['Name'];

if(isset($valreq)){
	$query = "Update `channels_log` Set Status = 'Finished' where `srv_req_id`= ?";
	$stmt = $GLOBALS["pdo"]->prepare($query);
	$stmt->execute([$valreq]);
}
?>