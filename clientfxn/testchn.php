<?php







function getallowedPages($id,$archive=false){
    include './../backend/config.php';
    //global $pdo;
    $query = "SELECT * FROM `channels_log` WHERE Id_user = ? and down_state = 0 order by TimeStmp desc";
    if($archive)
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
   /* $data = str_replace('-', '+', $data);

    $data =base64_decode($data);
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $data) as $line){
      if(strpos($line, 'client_static_keypair_pwd_enc') !== false){
        //<string name="client_static_keypair_pwd_enc">[2,&quot;Y4TRtdiWz9QKncuK7MEK0GVCQYGKA4iKlWqKgZ7vBtRR5JblhZ9nIEmlhgIGsn4dPf985kE+pbLocjBVGPzuag&quot;,&quot;OYDmu4psa6Ks0u541F9UoA&quot;,&quot;BkK87Q&quot;,&quot;HPMo1wcHG+W07N16zq7ZLg&quot;]</string>*/
        $line='<string name="client_static_keypair_pwd_enc">[2,&quot;hyWx8m1rSnncILCwBtgQcNpWxn6K4FA0+CoEEZj0GO4jkBTEhCh/q3jUdmFa83BXk4UI1nZqORSvOE69rRY+Mg&quot;,&quot;hcTQD8s7v4y7EwF+1MfTdg&quot;,&quot;RGP9WA&quot;,&quot;cZ3\/qZhlXvev6Vg1PlWKFA&quot;]</string>';

        $line = preg_replace("/&#?[a-z0-9]+;/i",'',$line);
        $line = str_replace('<string name="client_static_keypair_pwd_enc">[2,','',$line);
        $line = str_replace(']</string>','',$line);
        $line = str_replace(' ','',$line);
        $line = explode (",",$line);
        if(sizeof($line)>1){
          $line = $line[0];
          echo $line ."<br/>";
          return extracthash($line);
      }

      return "";
  



}

function extracthash($data){

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

$hash = validate_data("");
echo $hash;
