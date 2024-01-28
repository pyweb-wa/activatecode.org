
<?php

$id = "";
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
if(isset($_GET['check'])){
    $ccnt  = intval($_GET['check']);
    check($id,$ccnt);
    die();

}
$filepath = '/var/www/html/oldChannels/';
$input = file_get_contents('php://input');
$content = gzdecode($input);

file_put_contents($filepath . "test.log", $content . "\n", FILE_APPEND);

try {
    if (isJson($content) == 1) {

        $req_dump = json_decode($content, JSON_UNESCAPED_SLASHES);

        if (isset($req_dump["sig"])) {
            $sig = $req_dump["sig"];
            $myObj = new stdClass();
            $myObj->Code = 0;
            $myObj->Data = $req_dump['updataJson'];
            $count = 0;
            //TODO
            //not work need test
            if (is_array($myObj->Data)) {
                $count = sizeof($myObj->Data);

            }

            $result = stripslashes(json_encode($myObj, JSON_UNESCAPED_SLASHES));
            $result = str_replace('"[{', '[{', $result);
            $result = str_replace('}]"', '}]', $result);

            //file_put_contents('data/'.date('m-d-Y_h_i_s', time()).$id.".txt",$result);
            file_put_contents($filepath . $id, $result);
            // echo file_get_contents('data/'.$sig);
            $check = check($id, $count);

        }
    } else {
        file_put_contents($filepath . "logging.log", 'not json\n', FILE_APPEND);
        echo "not json";
    }
} catch (Exception $e) {
    file_put_contents($filepath . "logging.log", 'Caught exception: ' . $e->getMessage() . "\n", FILE_APPEND);
    //echo 'Caught exception: ',  $e->getMessage(), "\n";
}

function isJson($string)
{
    json_decode($string, JSON_UNESCAPED_SLASHES);
    return json_last_error() === JSON_ERROR_NONE;
}

function check($sig, $count)
{
    require_once '../backend/config.php';

    $filepath = '/var/www/html/oldChannels/';
    $query = "SELECT * FROM `channels_log` WHERE srv_req_id = ?  limit 1 ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sig]);
    $items = $stmt->fetchall();
    if (sizeof($items) == 1) {
        $sql = "UPDATE `channels_log` SET Status=? , return_count=? WHERE srv_req_id=?";
        $stmt1 = $pdo->prepare($sql);
        $stmt1->execute(["Finished", $count, $sig]);
        file_put_contents($filepath . "test.log", '\n countdb= ' . $items[0]['quantity'] . "\ncountserv=" . $count . "\n", FILE_APPEND);
        file_put_contents($filepath . "logging.log", '\n countdb= ' . $items[0]['quantity'] . "\ncountserv=" . $count . "\n", FILE_APPEND);

        //   if ($items[0]['quantity'] != $count) {
        //      checkquantity();
        // }

        return 1;
    }

}

function checkquantity($sig, $count)
{
    //TODO
    if ($count == 0) {
        //refund
    }

    echo "check quentity <br/>";
    return;

}
