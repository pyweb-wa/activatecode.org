<?php
session_start();
include 'config.php';


$handle = fopen("../Other/opapi_country.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
        $line = explode(",", $line);
        # if for check leng of line is >4 change posistion
        #next time :()
        $country =  str_replace('"', '', $line[1]);
        $country_code =  str_replace('"', '', $line[2]);

        $query = 'INSERT INTO `foreignapiservice`( `Name`, `code`, `price_in`, `price_out`, `acc_price_out`, `max_numbers`, `availability`, `Id_Foreign_Api`, `description`,  `country_name`,`country`, `country_of_api`,`carrier`, `service_of_api`, `is_deleted`) VALUES ("WhatsApp","wa","0.1","0.12","0.2","9999","9999","12","opapi.lemon","' . $country . '","' . strtoupper($country_code) . '" ,"' . $country_code . '","","","0");';
        echo $query . '<br>';

        // INSERT INTO `foreignapiservice`(`Name`, `code`, `price_in`, `price_out`, `acc_price_out`, `max_numbers`, `availability`, `Id_Foreign_Api`, `description`, `country`, `country_name`, `carrier`, `service_of_api`, `country_of_api`, `is_deleted`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],[value-9],[value-10],[value-11],[value-12],[value-13],[value-14],[value-15])


        // echo $country . " " .  $country_code .   '<br>';
    }

    fclose($handle);
} else {
    // error opening the file.
} 


// $stmt = $pdo->prepare("UPDATE   `foreignapi`set `Name` =? , `Description`=?, `Access_Token`=?, `Refresh_Token`=?, `Valid`=?, `ExpiryDate`=? where `Id_Api`=?");
// $stmt->execute([$name, $Description, $Access_Token, $Refresh_Token, $Valid, $ExpiryDate, $apiId]);
