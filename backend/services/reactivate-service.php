<?php


function reactivate()
{
    include '/var/www/smsmarket/html/backend/config.php'; 
    try {

        $stmt1 = $pdo->prepare("SELECT * FROM `countries_control` where  `reactivate` = 1");
        $stmt1->execute([]);

        $results = $stmt1->fetchAll();

        if(count($results) >= 1){
            $stmt1 = $pdo->prepare("SELECT * FROM `bananaapi-number` where `taked` = 1 and is_finished = 0 ");
            $stmt1->execute([]);
            $results = $stmt1->fetchAll();
            if (sizeof($results) >= 1) {
                foreach ($results as $res) {
                    $stmt = $pdo->prepare('UPDATE `bananaapi-number` SET `taked`=0, taked_time=null,is_finished=0,is_finished_time=null, release_time = NOW() WHERE `id` = ?');
                    $stmt->execute([$res['id']]);
                    echo "reactivating :" . $res['phone_number'] . PHP_EOL;
                }
    
            }
        }

       
    } catch (Exception $e) {
        file_put_contents('/var/www/smsmarket/logging/reactivate_err.log', $e->getMessage(), FILE_APPEND);
        echo($e->getMessage());
    }


}


while (true) {
    try {
        echo " reactivating start " . date('F j, Y g:i a') . PHP_EOL;
        reactivate();
        sleep(2700); 
    } catch (Exception $e) {
        file_put_contents('/var/www/smsmarket/logging/reactivate_err.log', $e->getMessage(), FILE_APPEND);

        echo ($e->getMessage());
    }


}
