<?php



if (!function_exists('get_accounts')) {
  function get_accounts() {
    include 'accounts.php';
    return $accounts;
  }
}

// function get_accounts(){
//   include 'accounts.php';
//   return $accounts;
// }
if (!function_exists('UpdateTokens')) {
function UpdateTokens(){
  include 'accounts.php';

    foreach($accounts as $server){
        GetTokensFromServer($server);
        usleep(30000);
 

  }
}
}


if (!function_exists('Check_sessionFile')) {
function Check_sessionFile($server){
    include 'accounts.php';

    $filePath =   $scriptPath . "/".$server['name']."_token";
    if (file_exists($filePath) && is_readable($filePath)) {
        $session = file_get_contents($scriptPath."/".$server['name']."_token");
        return $session;
    } else {
       // echo "The file does not exist or is not readable.";
        UpdateTokens();
        if (file_exists($filePath) && is_readable($filePath)) {
            $session = file_get_contents($scriptPath."/".$server['name']."_token");
            return $session;
        }
            else return null;
    }
  
}
}

if (!function_exists('deletenumberfromAllServers')) {
function deletenumberfromAllServers($number,$flag=false){
  include 'accounts.php';

  foreach($accounts as $server){
  $session = Check_sessionFile($server);
  if(!$session){
      return null;
  }
  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_PORT => $server['port'],
    CURLOPT_URL => $server['url']."/number_list/delete_number",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_POSTFIELDS => '{"number": "'.$number.'"}',
    CURLOPT_HTTPHEADER => [
      "Authorization: Bearer ".$session,
      "Content-Type: application/json"
    ],
  ]);
  $response = curl_exec($curl);
  file_put_contents($logging, $response."/n", FILE_APPEND | LOCK_EX);

  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  if ($httpCode === 401) {
     // echo "Unauthorized: The request requires user authentication.";
      UpdateTokens();
      if(!$flag){
          deletenumberfromServer($server,$number,true);
      }
  }
  $err = curl_error($curl);
  
  curl_close($curl);
  
  if ($err) {
    file_put_contents($logging, "cURL Error #:" . $err."\n", FILE_APPEND | LOCK_EX);
    //echo "cURL Error #:" . $err;
  } else {
   
   // echo $response;
  }
    
 }
}
}
if (!function_exists('deletenumberfromServer')) {
function deletenumberfromServer($server,$number,$flag=false){
    include 'accounts.php';

    $session = Check_sessionFile($server);
    if(!$session){
        return null;
    }
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_PORT => $server['port'],
      CURLOPT_URL => $server['url']."/number_list/delete_number",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "DELETE",
      CURLOPT_POSTFIELDS => '{"number": "'.$number.'"}',
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer ".$session,
        "Content-Type: application/json"
      ],
    ]);
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($httpCode === 401) {
        echo "Unauthorized: The request requires user authentication.";
        UpdateTokens();
        if(!$flag){
            deletenumberfromServer($server,$number,true);
        }
    }
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
      file_put_contents($logging, "cURL Error #:" . $err."\n", FILE_APPEND | LOCK_EX);
    } else {
     // echo $response;
    }
         
}
}

if (!function_exists('deletenumberArrayfromServer')) {
function deletenumberArrayfromServer($server,$array,$flag=false){
  include 'accounts.php';

  $session = Check_sessionFile($server);
  if(!$session){
      return null;
  }


  $headers = [
    "Authorization: Bearer " . $session,
    "Content-Type: application/json",
];

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_PORT => $server['port'],
    CURLOPT_URL => $server['url']."/number_list/delete_array",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_POSTFIELDS => $array,
    CURLOPT_HTTPHEADER =>  $headers,
  ]);
  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  if ($httpCode === 401) {
      echo "Unauthorized: The request requires user authentication.";
      UpdateTokens();
      if(!$flag){
          deletenumberfromServer($server,$number,true);
      }
  }
  $err = curl_error($curl);
  
  curl_close($curl);

  if ($err) {
    file_put_contents($logging, "cURL Error #:" . $err."\n", FILE_APPEND | LOCK_EX);
  } else {
    echo $response;
  }
       
}

}



if (!function_exists('AddNumberToList')) {   
function AddNumberToList($server,$number,$list_id,$flag=false){
  include 'accounts.php';

    $session = Check_sessionFile($server);
    if(!$session){
        return null;
    }
    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_PORT => $server['port'],
    CURLOPT_URL => $server['url']."/number_list/add_number",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_POSTFIELDS => '{
      "number": "'.$number.'",  
      "list_id": '.$list_id.',             
      "comment": "auto_push"    
    }',
    CURLOPT_HTTPHEADER => [
      "Authorization: Bearer  ".$session,
      "Content-Type: application/json"
    ],
  ]);
  
  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  if ($httpCode === 401) {
     // echo "Unauthorized: The request requires user authentication.";
      UpdateTokens();
      if(!$flag){
      AddNumberToList($server,$number,$list_id,true);
      }
  }

  $err = curl_error($curl);
  
  curl_close($curl);
  
  if ($err) {
    file_put_contents($logging, "cURL Error #:" . $err."\n", FILE_APPEND | LOCK_EX);
  } else {
  //  echo $response;
  }

}

}

if (!function_exists('GetTokensFromServer')) {  
function GetTokensFromServer($server){
  include 'accounts.php';

  $curl = curl_init();
  $data = [
    "login" => $server["login"],
    "password" => $server["password"]
 ];
  $jsonData = json_encode($data);
  curl_setopt_array($curl, [
    CURLOPT_PORT => $server['port'],
    CURLOPT_URL => $server['url']."/number_list/session",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PUT",
    CURLOPT_POSTFIELDS => $jsonData,
    CURLOPT_HTTPHEADER => [
      "Content-Type: application/json"
    ],
  ]);
  try{
  $response = curl_exec($curl);
 
  $err = curl_error($curl);
  
  curl_close($curl);
  
  if ($err) {
    file_put_contents($logging, "cURL Error #:" . $err."\n", FILE_APPEND | LOCK_EX);
  } else {
    $response = json_decode($response,True);
    if(isset($response['session'])){
      $session = $response['session'];
      $scriptPath = dirname(__FILE__);
      file_put_contents($scriptPath."/".$server['name']."_token",$session);
      //echo $scriptPath;
    }
  }
    } catch (Exception $e) {
      file_put_contents($logging, "cURL Error =>e #:" . $e."\n", FILE_APPEND | LOCK_EX);
    }
      
      
}
}