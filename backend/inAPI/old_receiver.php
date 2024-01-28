<?php
function isJson($string) {
   json_decode($string,JSON_UNESCAPED_SLASHES);
   return json_last_error() === JSON_ERROR_NONE;
}

$id = "";
if(isset($_GET['id']))
{
   $id = $_GET['id'];
}

$filepath = '../../oldChannels/';
//$content=file_get_contents('testdate.txt');
$content = gzdecode(file_get_contents('php://input'));

file_put_contents($filepath."test.log",$content,FILE_APPEND);

try {
    if( isJson($content) == 1){

        $req_dump = json_decode($content,JSON_UNESCAPED_SLASHES);

       if(isset($req_dump["sig"])){
           $sig=$req_dump["sig"];
file_put_contents($filepath."logging.log","in if \n",FILE_APPEND);

//echo json_encode($req_dump);
	$myObj->Code = 0;
	$myObj->Data = $req_dump['updataJson'];
	$result = stripslashes(json_encode($myObj, JSON_UNESCAPED_SLASHES));
	$result = str_replace('"[{','[{',$result);
	$result = str_replace('}]"','}]',$result);

   //file_put_contents('data/'.date('m-d-Y_h_i_s', time()).$id.".txt",$result);
   file_put_contents($filepath.$id.".txt",$result);
  // echo file_get_contents('data/'.$sig);

    }
       }

else {
 file_put_contents("data/logging.log",'not json\n',FILE_APPEND);
   echo "not json";
}
   } 
   catch (Exception $e) {
file_put_contents("data/logging.log",'Caught exception: '.  $e->getMessage(). "\n",FILE_APPEND);
    //echo 'Caught exception: ',  $e->getMessage(), "\n";
   }