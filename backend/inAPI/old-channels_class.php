<?php

class old_channels
{
    public function __construct()
    {
        include 'config.php';
        include_once "mylogger.php";

        $this->logger = new MyLogger();
        $this->pdo = $pdo;

    }
    //public $simcode_token;

    public function getchannels($countries, $uuid, $cnt = null, $first)
    {
        try {
            $result = (object) [
                'ResponseCode' => 1,
                'Msg' => 'OK',
                'Result' => null,
                'ANY' => '',
                'Id' => '',
            ];


            // $response = [
            //     'Id' => '4444444444',
            //     'Code' => 0,
            // ];
            //TODO UNCOMMENT in production


            ###for old crontab-old.php
            $date = date_create();
            $uuid = md5($uuid . date_timestamp_get($date));
            $result->ResponseCode = 0;
            $result->Msg = "OK";
            $result->Result = (object) [
                'Id' => $uuid,

            ];
            $result->Id = $uuid;
            
            unset($result->ANY);
       // $filepath = '/var/www/html/oldChannels/';
       // file_put_contents($filepath . $id,"");
            
            return $result;
            ####################


           // $response = $this->askforchannels($uuid, $cnt, $countries, $first);


        } catch (Exception $e) {
            $this->logger->Add($e->getMessage(), basename(__FILE__));
        }
        //$result->Result = $response;
        //TODO NEED return ID in Result
        // $any = "ANY";
        // if (isset($response["status"])) {
        //     if ($response['status'] == 0) {

        //         if(isset($response['response'])){
        //             $any = sizeof($response['response']);
        //         }



        //         $result->ResponseCode = 0;
        //         $result->Msg = "OK";
        //         $result->Result = (object) [
        //             'Id' => $response['Id'],

        //         ];
        //         $result->Id = $response['Id'];
        //         $result->ANY = $any;
        //     }
        // } else {
        //     $result->Msg = "error";
        // }

        // $this->logger->Add(json_encode($result), basename(__FILE__));
        // //
        // return $result;
    }

    public function askforchannels($uuid, $cnt=1, $countries, $first)
    {

        Date_default_timezone_set ('Asia/Hong_Kong');
        $date = date_create();
        $uuid = md5($uuid . date_timestamp_get($date));
        $p = new DateTime(null, new DateTimeZone('Asia/Hong_Kong'));
        $p = round($p->getTimestamp() *1000);
        $partnerKey = "oxRPoeFZ9XCTnvlG";
        $apikey = '3QKGMwwadU2cTXDN';
        $sig =  md5( "apikey=" .$apikey . "country=".$countries . "t=" .$p .$partnerKey );
        $url = "http://qnvtbj.xjwi5.com/udaa/api/wa/pull/6fogoz?apikey=".$apikey."&country=".$countries."&t=".$p."&sig=".$sig."&cnt=" . $cnt;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_PORT => "80",
        CURLOPT_URL =>  $url, #"http://juwuat.74hcjs.com:10238/nMl4aQIaZ9hU/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 50,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
       // CURLOPT_POSTFIELDS => $myobj,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
        ],

    ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            file_put_contents("/var/www/html/oldChannels/test.log","id: ".$uuid."   ".$response."\n\n",FILE_APPEND);

            $response = json_decode($response, true);
            //$response['billid'] = $uuid;
            if(isset($response['billid'])){
                $this->callback_billid($response['billid']);

            }
            
            $response['Id'] = $uuid;
            if (sizeof($response['response'])>=0){

                $response['Data']=$response['response'];
                unset($response['response']);
                $this->savetofile($response,$uuid);

            }
            return $response;
        }

        return "error";
    }

    public function askforchannels2($uuid, $cnt=1, $countries)
    {

        Date_default_timezone_set ('Asia/Hong_Kong');
        $date = date_create();
        
        $p = new DateTime(null, new DateTimeZone('Asia/Hong_Kong'));
        $p = round($p->getTimestamp() *1000);
        $partnerKey = "oxRPoeFZ9XCTnvlG";
        $apikey = '3QKGMwwadU2cTXDN';
        $sig =  md5( "apikey=" .$apikey . "country=".$countries . "t=" .$p .$partnerKey );
        $url = "http://f5t6an.xjwi5.com:10092/udaa/api/wa/pull/6fogoz?apikey=".$apikey."&country=".$countries."&t=".$p."&sig=".$sig."&cnt=" . $cnt;
        #$url = "http://qnvtbj.xjwi5.com/udaa/api/wa/pull/6fogoz?apikey=".$apikey."&country=".$countries."&t=".$p."&sig=".$sig."&cnt=" . $cnt;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_PORT => "10092",
        CURLOPT_URL =>  $url, #"http://juwuat.74hcjs.com:10238/nMl4aQIaZ9hU/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 50,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
       // CURLOPT_POSTFIELDS => $myobj,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
        ],

    ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        //echo $response;
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            file_put_contents("/var/www/html/oldChannels/test.log","id: ".$uuid."   ".$response."\n\n",FILE_APPEND);

            $response = json_decode($response, true);
            //$response['billid'] = $uuid;
            if(isset($response['billid'])){
                $this->callback_billid($response['billid']);

            }
            
            $response['Id'] = $uuid;
            if(!isset($response['response'])) 
            return "error";
            if (sizeof($response['response'])>=0){

                $response['Data']=$response['response'];
                unset($response['response']);
                $this->savetofile2($response,$uuid);

            }
            return $response;
        }

        return "error";
    }

    private function savetofile2($req_dump,$id)
    {
        $filepath = '/var/www/html/oldChannels/';
        
        try {
  
           
    if(file_exists($filepath.$id)){
        $data = file_get_contents($filepath.$id);
        $data = json_decode($data,true);
        if(isset($data['Data']))
        {
            foreach ($req_dump['Data']  as $value) {
                array_push($data['Data'],$value);
            }


        }
        else{
            $data = $req_dump;
        }

    }
    else{
        $data = $req_dump;
    }

    $data = json_encode($data, JSON_UNESCAPED_SLASHES);
    $data = str_replace('"[{', '[{', $data);
    $data = str_replace('}]"', '}]', $data);
    file_put_contents($filepath . $id, $data);

 
        } catch (Exception $e) {
            file_put_contents($filepath . "logging.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }


    private function savetofile($req_dump,$id)
    {
        $filepath = '/var/www/html/oldChannels/';
        try {
                // if (isset($req_dump->Result['Id'])) {
                //     $sig = $req_dump->Result['Id'];

                //     //echo json_encode($req_dump);
                //     $myObj = new stdClass();
                //     $myObj->Code = 0;
                //     $myObj->Data = $req_dump['updataJson'];
                //     $count = 0;
                //     //TODO
                //     //not work need test
                //     if (is_array($myObj->Data)) {
                //         $count = sizeof($myObj->Data);

                //     }

            $result = json_encode($req_dump, JSON_UNESCAPED_SLASHES);
                //$result = stripslashes(json_encode($req_dump, JSON_UNESCAPED_SLASHES));
            $result = str_replace('"[{', '[{', $result);
            $result = str_replace('}]"', '}]', $result);

                    //file_put_contents('data/'.date('m-d-Y_h_i_s', time()).$id.".txt",$result);

            file_put_contents($filepath . $id, $result,FILE_APPEND);
                    // echo file_get_contents('data/'.$sig);
                    //$check = check($id, $count);

               // }
            // } else {
            //     file_put_contents($filepath . "logging.log", 'not json\n', FILE_APPEND);
            //     echo "not json";
            // }
        } catch (Exception $e) {
            file_put_contents($filepath . "logging.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

    }

    public function isJson($string)
    {
        json_decode($string, JSON_UNESCAPED_SLASHES);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function callback_billid($billid){
        $apikey = '3QKGMwwadU2cTXDN';
        # $url = "http://qnvtbj.xjwi5.com/udaa/api/bill/state?apikey=".$apikey."&billId=".$billid."&state=1";
         $url = "http://f5t6an.xjwi5.com:10092/udaa/api/bill/state?apikey=".$apikey."&billId=".$billid."&state=1";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_PORT => "10092",
        CURLOPT_URL =>  $url, #"http://juwuat.74hcjs.com:10238/nMl4aQIaZ9hU/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 50,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
       // CURLOPT_POSTFIELDS => $myobj,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
        ],

    ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //echo "cURL Error #:" . $err;
        } else {
            
        }

    }
    public function check($sig, $count)
    {
        //require_once 'config_007.php';

        $filepath = '/var/www/html/oldChannels/';
        $query = "SELECT * FROM `channels_log` WHERE srv_req_id = ?  limit 1 ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$sig]);
        $items = $stmt->fetchall();
        if (sizeof($items) == 1) {
            $sql = "UPDATE `channels_log` SET Status=? , return_count=? WHERE srv_req_id=?";
            $stmt1 = $this->pdo->prepare($sql);
            $stmt1->execute(["Finished", $count, $sig]);
            file_put_contents($filepath . "test.log", '\n countdb= ' . $items[0]['quantity'] . "\ncountserv=" . $count . "\n", FILE_APPEND);
            file_put_contents($filepath . "logging.log", '\n countdb= ' . $items[0]['quantity'] . "\ncountserv=" . $count . "\n", FILE_APPEND);

            //   if ($items[0]['quantity'] != $count) {
            //      checkquantity();
            // }

            return 1;
            //array_push
        }

    }

    public function checkquantity($sig, $count)
    {
        //TODO
        if ($count == 0) {
            //refund
        }

        echo "check quentity <br/>";
        return;

    }
}
