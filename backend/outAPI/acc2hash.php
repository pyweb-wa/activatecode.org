<?php



//data = apiresult['c'];




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
     echo extracthash($line);
    }

   break;
} 

 
} 
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
