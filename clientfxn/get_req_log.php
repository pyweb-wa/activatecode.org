
<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

require_once './../backend/config.php';
$user = (int) $_SESSION['id'];
if ($user == 0) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}


//$stmt1 = $pdo->prepare("UPDATE requests_log set `Status`='Expired' WHERE `TimeStmp` <= DATE_SUB(NOW(), INTERVAL 10 MINUTE) and `Id_user`=?   and  `Status`='pending'  ");

// $stmt1 = $pdo->prepare("SELECT `Id_request` FROM  requests_log  WHERE `TimeStmp` <= DATE_SUB(NOW(), INTERVAL 5 MINUTE) and `Id_user`=?   and  `Status`='pending'  ");
// $stmt1->execute([$user]);
// $req_to_refund = $stmt1->fetchAll();
// ///////////////////////////
// foreach($req_to_refund as $reqID){
//     $stmt = $pdo->prepare('call `get_refund`(?,?) ');
//     $stmt->execute([$reqID['Id_request'], $_SESSION['api_key']]);
//     $res = $stmt->fetchAll();
//     $stmt->closeCursor();
// }
//////////////////////////
$stmt2 = $pdo->prepare("select NOW()  ");
$stmt2->execute();
$current_time = $stmt2->fetch();
$timestamp = strtotime($current_time['NOW()']);

$stmt = $pdo->prepare("SELECT `Id_request`,`Status`, `Phone_Nb`, `SMSCode`, `sms_content`, `TimeStmp`,   `code`,`Name`  FROM `requests_log` ,`foreignapiservice`
WHERE  requests_log.service = foreignapiservice.Id_Service_Api and requests_log.Id_user=? order by Id_request desc limit 5000 ");
$stmt->execute([$user]);
$logs = $stmt->fetchall();
$jarray = [];

foreach ($logs as $row) { //Success
    if ($row['Status'] == 'pending') {
        $row['Status'] = '<span class="badge badge-pill badge-warning">' . $row['Status'] . '</span> 
        <span class="badge badge-pill badge-danger" onclick="cancel_code_request(' . $row['Id_request'] . ')" ><i class="fa fa-times" ></i></span>
        ';
        $row_time = strtotime($row['TimeStmp']);
        $diff = 600 - ($timestamp - $row_time);
        //<button type="button" class="btn-danger " id="'.$row['SMSCode'].'" onclick="" ><i class="fas fa-times" ></i></button>
        //
        $row['TimeStmp'] = '
        <div class="progress">
        <div class="progress-bar progress-bar-striped  bg-warning progress-bar-animated" role="progressbar" aria-valuenow="' . $diff . '" aria-valuemin="0" aria-valuemax="600" style="width:' . ((int)((int)$diff / 6)) . '%">
       <span class="pending_progress"> ' . $diff . ' Seconds Left </span>
       <span class="_request_id" value="' . $row['Id_request'] . '"></span> 
       </div>
        </div>';
    } else if ($row['Status'] == 'Expired') {
        $row['Status'] = '<span class="badge badge-pill badge-danger">' . $row['Status'] . '</span>';
        $row['SMSCode'] = '<button type="button" class="btn-danger number_btn">Expired</button>';
    } else if ($row['Status'] == 'Finished') {
        $row['Status'] = '<span class="badge badge-pill badge-primary ">' . $row['Status'] . '</span>';
    }
    //  else if ($row['Status'] == 'DONE') {
    //     $row['Status'] = '<span class="badge badge-pill badge-success ">' . $row['Status'] . '</span>';
    // }

    if ($row['SMSCode'] == null) {
        $row['SMSCode'] = '<img src="assets/img/waiting.gif"  width="80" height="25" />';
    } else if ($row['SMSCode'] != '<button type="button" class="btn-danger number_btn">Expired</button>') {
        $row['SMSCode'] = '<button type="button" class="btn-primary number_btn" id="' . $row['SMSCode'] . '" onclick="copy_to_clip()" >' . $row['SMSCode'] . '</button>';
    }
    if(file_exists('../assets/img/apps_icons/'. $row['Name'].'.png'))
    {
        $row['code'] = '<img src="assets/img/apps_icons/' . $row['Name'] . '.png"  width="40"/>'; 
    }else{
        $row['code'] = '<img src="assets/img/placeholder.png"   width="40"/>';     
    }

   $row['Phone_Nb'] = '<button type="button" class="btn-success number_btn" id="' . $row['Phone_Nb'] . '" onclick="copy_to_clip()" >' . $row['Phone_Nb'] . '</button>';

    array_push($jarray, $row);
}

echo (json_encode($jarray));
