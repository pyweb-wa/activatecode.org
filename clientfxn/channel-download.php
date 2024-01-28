<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

$user = (int) $_SESSION['id'];
//getallowedPages(1);
$filename = "";
if (isset($_GET['fname'])) {
    $filename = $_GET['fname'];
} else if (isset($_POST['fname'])) {
    $filename = $_POST['fname'];
}
$allowedPages = [];


if ($filename) {
    //require_once './../backend/config.php';
    //$conn = $pdo;
    
    $orgfile = $filename;
    //get user filename;
    
    //$allowedPages = array("10a8948994d55c94526e96785bca1647", "e22236d9c8e3b52106ada6a53cd3c199");
    if(isset($_GET['archive'])){
        $items = getallowedPages($user,'archive');
    }
    else{
    $items = getallowedPages($user);
    }
    //var_dump($items);

    foreach($items as $item)
    {
       array_push($allowedPages,$item['srv_req_id']); 
    }
  
    //$filename = $_GET['fname'];
    $file_path = '/var/www/html/oldChannels/';
    //$file_path = '../oldChannels/';
    if (in_array($filename, $allowedPages) && file_exists($file_path . $filename )) {
        $filename = $file_path . $filename;
    } else {
        echo "forbidden 403";
        die();
        //output error
    }
    if (isset($_POST['show']) || isset($_GET['show'])) {
        echo file_get_contents($filename);
        die();
    }

    if(isset($_POST['convert']) || isset($_GET['convert'])){
    $cnv_path = $file_path . "Hashs/";

    if(file_exists($cnv_path.$orgfile))
    {
        $filename = $cnv_path.$orgfile;
    }
    //convert
    else{
        extracthash($file_path.$orgfile,$cnv_path);
        $filename = $cnv_path.$orgfile;

        // $data = file_get_contents($filename);
        // $data = json_decode($data);
        // if(isset($data->Data)) {
        //     $res_hashs = "";
        //     foreach($data->Data as $item){

        //         //extract phone number
        //         $b = str_replace('-', '+', $item->b);
        //         $b = base64_decode($b);
        //         if(strpos($b, 'com.whatsapp') !== false){
        //         preg_match_all('!\d+!', $b, $phone);
        //         if(sizeof($phone) == 1) {
        //             $phone = $phone[0];
        //             if(sizeof($phone) ==3) $phone = $phone[1]; 
        //             else $phone = '00';
        //         }
        //         else $phone = '00';
                
        //         } else{
        //             $phone = '00';
        //         }



        //         if($phone != '00'){
        //         // extract hash
        //         $hash = validate_data($item->c);
        //         $res_hashs .=$phone.",". $hash.PHP_EOL;
        //         }

        //        // echo $hash."\n";
        //     }
        //    // echo $res_hashs;
        //     if($res_hashs != ""){
        //         file_put_contents($cnv_path.$orgfile,$res_hashs);
        //         $filename = $cnv_path.$orgfile;

        //     }
        // }
       


    }
    }

    updateDownState($orgfile);
    //die();
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=' . basename($filename).'.txt');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    header("Content-Type: text/plain");
    readfile($filename);
}



function getallowedPages($id,$archive=false){
    include './../backend/config.php';
    //global $pdo;
    $query = "SELECT * FROM `channels_log` WHERE Id_user = ? and down_state = 0 order by TimeStmp desc";
    if($archive=='archive')
    $query = "SELECT * FROM `channels_log` WHERE Id_user = ?  and down_state = 1 order by TimeStmp desc";
   

    $arrayParams = [];
    $stmt =$pdo->prepare($query);
    $stmt->execute([$id]);
    $items = $stmt->fetchall();
    
    return $items;
    //array_push

}

function updateDownState($id){
   
    include './../backend/config.php';
    $sql = "UPDATE `channels_log` SET down_state = 1 WHERE `srv_req_id`=?";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$id]);

}



function validate_data($data){
    $data = str_replace('-', '+', $data);

    $data =base64_decode($data);
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $data) as $line){
      if(strpos($line, 'client_static_keypair_pwd_enc') !== false){
        //<string name="client_static_keypair_pwd_enc">[2,&quot;Y4TRtdiWz9QKncuK7MEK0GVCQYGKA4iKlWqKgZ7vBtRR5JblhZ9nIEmlhgIGsn4dPf985kE+pbLocjBVGPzuag&quot;,&quot;OYDmu4psa6Ks0u541F9UoA&quot;,&quot;BkK87Q&quot;,&quot;HPMo1wcHG+W07N16zq7ZLg&quot;]</string>
        $line = preg_replace("/&#?[a-z0-9]+;/i",'',$line);
        $line = str_replace('<string name="client_static_keypair_pwd_enc">[2,','',$line);
        $line = str_replace(']</string>','',$line);
        $line = str_replace(' ','',$line);
        $line = explode (",",$line);
        if(sizeof($line)>1){
          $line = $line[0];
         return extracthash($line);
        }
    
       break;
    } 
    
     
    } 
    }
    
    function extracthash2($data){
    
    $base64_bytes = $data;
    $base64_bytes = $base64_bytes."===";
    $message_bytes = base64_decode($base64_bytes); 
    $public = substr($message_bytes,0,32);
    $private = substr($message_bytes,-32);
    $public = base64_encode($public);
    $private = base64_encode($private);
    $result = $public.",".$private;
    return  $result;
    
    }

    function extracthash($infile,$outdir)
    {
    #echo exec('java --version');
    #echo 'java -jar /home/pyweb/wapi.jar "' . $data . '" "' . $iv . '" "' . $salt . '" "' . $pwd . '"<br/>';
        $cmd = 'java -jar /var/www/html/sms-platform/wapi.jar  -InFile ' . $infile. ' -OutDir ' . $outdir;
	//echo $cmd;
exec($cmd,$last_line);  
    return  $last_line;
    
    }
// var_dump(extracthash('/var/www/html/oldChannels/fe0012cf46728085ddb42c0cfd5e2e49','/var/www/html/oldChannels/Hashs/'));

