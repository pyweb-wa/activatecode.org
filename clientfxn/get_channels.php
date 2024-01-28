<?php
session_start(); 
if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}
//TODO change from files list to db status check
//$files = glob('../oldChannels/*.txt');
$file_path = '/var/www/html/oldChannels/';
//$file_path = './../oldChannels/';
if(!file_exists($file_path)) $file_path = "../oldChannels/";
$arr = array();
$cnt=0;
$pend = 0;
$archive = false;
if(isset($_GET['archive']))
{
    $archive = true;
}
$user = (int) $_SESSION['id'];
require_once 'channel-download.php';
$items = getallowedPages($user,$archive);
//var_dump($items);

//$allowedPages = array("10a8948994d65c94526e96785bca1647","e22236d9c8e3b52106ada6a53cd3c199");
foreach ($items as $file){
    //if(! in_array(basename($file,".txt"),$items['srv_req_id']) ) continue; //Finished
    if($file['Status']=="Finished"){
        if(file_exists($file_path .$file['srv_req_id'])){
            $arr2 = array('id'=>$cnt, 'FileTime' =>$file['TimeStmp'],'quantity'=>intval($file['quantitydone'])."/".$file['quantity'],'name'=>$file['info'].'/'.substr($file['srv_req_id'],0,6),'status' => $file['Status'],'show' => '<button type="button" class="btn btn-success" onclick="ShowOnline(\''.$file['srv_req_id'].'\')"  >Show</button>','download' => '<button type="button" class="btn btn-primary" onclick="DownloadChannel(\''.$file['srv_req_id'].'\')"  >Download</button>','convert' => '<button type="button" class="btn btn-warning" onclick="Convert(\''.$file['srv_req_id'].'\')" title="convert and download channels as hashs"  >Hashs</button>','emulator' => '<button type="button" class="btn btn-danger" onclick="Emulator(\''.$file['srv_req_id'].'\')" title="Convert to Emulator format"  >Emulator</button>','stop' => '<button type="button" class="btn btn-danger" disabled >Stop</button>');
            array_push($arr,$arr2);
            //$cnt +=1;
        }
        else{
            $arr2 = array('id'=>$cnt, 'FileTime' =>$file['TimeStmp'],'quantity'=>$file['quantity'],'name'=>$file['info'].'/'.substr($file['srv_req_id'],0,6),'status' => $file['Status'],'show' => '<button type="button" class="btn btn-danger"    >FILE ERROR</button>','download' => '<button type="button" class="btn btn-danger"    >FILE ERROR</button>','convert' => '<button type="button" class="btn btn-danger"    >FILE ERROR</button>','emulator' => '<button type="button" class="btn btn-danger"    >FILE ERROR</button>','stop' => '
        <button type="button" class="btn btn-danger" disabled >Stop</button>');
            array_push($arr,$arr2);
            //$cnt +=1;
        }
       // $cnt +=1;
        
    } 
    else
    {
        $arr2 = array('id'=>$cnt, 'FileTime' =>$file['TimeStmp'],'quantity'=>intval($file['quantitydone'])."/".$file['quantity'],'name'=>$file['info'].'/'.substr($file['srv_req_id'],0,6),'status' => $file['Status'],'show' => '
        <button type="button" class="btn btn-warning" disabled >Pending</button>','download' => '
        <button type="button" class="btn btn-warning" disabled >Pending</button>','convert' => '
        <button type="button" class="btn btn-warning" disabled >Pending</button>','emulator' => '
        <button type="button" class="btn btn-warning" disabled >Pending</button>','stop' => '
        <button type="button" class="btn btn-danger" onclick="FireCancel(\''.$file['srv_req_id'].'\')" >Stop</button>');
        array_push($arr,$arr2);
        $pend +=1;
    }
    $cnt +=1;
//echo $file;
}

if(isset($_GET["count"]))
{
    echo $cnt.",".$pend;
}else{
    echo (json_encode($arr));
}