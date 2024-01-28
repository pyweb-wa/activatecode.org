<?php 
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:login.php');
    die();
}

require_once './../../backend/config.php';

if (isset($_POST["updatecountry"])) {
    $html = ''; 
    $top_usage = top_used_apps_country();
    $array_country = array();
    if(sizeof($top_usage)>=1){
        $html = '';    
    foreach($top_usage as $row){
    //     $obj = (object) [
    //         'country' => $row['country'],
    //         'country_name' =>$row['country_name']
    //     ];
    //    array_push($array_country,$obj);

   $status = getcardbycountry( $row['country'],$row['country_name']); 
////////////////////////////////////
$html .= '
<div class="col-10 col-sm-6 col-md-3 d-flex align-items-stretch flex-column">
                            <div class="card bg-light d-flex flex-fill">
                                <div class="card-header text-muted border-bottom-0">
                                    Country: '.$row["country_name"].'
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row">
                                        <div class="col-6">
                                             <h2 class="lead"><b>Total used:</b></h2>
                                            
                                            <ul class="ml-4 mb-0 fa-ul text-muted">
                                                <li class="small"><span class="fa-li"><i class="fa fa-home"></i></span>avilable:'. $status['avilable'].' </li>
                                              
                                                <li class="small"><span class="fa-li"><i class="fas fa-phone"></i></span> Requested: '. $status['Requested'].'</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-sms"></i></span> Has Message: '. $status['Has_sms'].'</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-ban"></i></span> No sms recived: '. $status['Has_no_sms'].'</li>
                                                <li class="small"><span class="fa-li"><i class="fas fa-mobile-alt"></i></span> applications: '. $status['application'].'</li>
                                            </ul>
                                        </div>
                                        <!-- <div class="col-5 text-center">
                                            <img src="../../dist/img/user1-128x128.jpg" alt="user-avatar" class="img-circle img-fluid">
                                        </div> -->
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-right">
                                        <a href="#" class="btn btn-sm bg-teal">
                                            <i class="fas fa-comments"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-primary">
                                            <i class="fas fa-user"></i> View Profile
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>';


//////////////////////////////////////////






    }
}

echo  $html;
}
else if( isset($_POST["get_stat"]) &&
isset($_POST["country"]) && 
isset($_POST["country_name"])) {
    $res = getcardbycountry($_POST["country"],$_POST["country_name"]);



}


function getcardbycountry($country,$country_name){

    $sql ='SELECT 
    (SELECT COUNT(*) FROM `bananaapi-number` WHERE taked = 0 and `country_code` = "'.$country.'") AS avilable, 
    (SELECT COUNT(*) FROM `requests_log`,`foreignapiservice` where `foreignapiservice`.`country_name` = "'.$country_name.'" and requests_log.service = `foreignapiservice`.Id_Service_Api ) AS Requested, 
    (SELECT COUNT(*) FROM `requests_log`,`foreignapiservice` where `foreignapiservice`.`country_name` = "'.$country_name.'" and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content`IS NOT NULL) AS Has_sms,
    (SELECT COUNT(*) FROM `requests_log`,`foreignapiservice` where `foreignapiservice`.`country_name` = "'.$country_name.'" and requests_log.service = `foreignapiservice`.Id_Service_Api and `requests_log`.`sms_content`IS  NULL) AS Has_no_sms,
    (SELECT GROUP_CONCAT(DISTINCT  foreignapiservice.Name) as applications FROM `requests_log`,`foreignapiservice` where `foreignapiservice`.`country_name` = "'.$country_name.'" and requests_log.service = `foreignapiservice`.Id_Service_Api ) As application';

   $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute([]);
    $stats = $stmt->fetchall();
    return $stats[0];
}


function top_used_apps_country()
{
    $query="SELECT foreignapiservice.Name as 'app' ,country as 'country' ,country_name  , foreignapi.Name as 'API' ,count(Id_request) as 'count' FROM `foreignapiservice` ,`foreignapi`,`requests_log` WHERE `requests_log`.`service` =`foreignapiservice`.`Id_Service_Api` and `foreignapiservice`.`Id_Foreign_Api`=`foreignapi`.`Id_Api` GROUP by app,country,country_name ,API order by count desc limit 5";
    // app API count country
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute();
    $rows = $stmt->fetchall(); 
    return $rows;
}


