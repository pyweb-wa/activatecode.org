<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location:../login.php');
    die();
}
$customer_id = $_SESSION["id"];
// echo '123';


$inipath = php_ini_loaded_file();


$data = file_get_contents($_FILES["fileElem"]["tmp_name"]);
//echo $fileContent;


//echo realpath(dirname(__FILE__));
//var_dump( $data);
//die();
readobj($data);
function sed($file, $cc, $phone, $name, $version)
{
    $file = $file . "com.whatsapp_preferences_light.xml";

    system("sed -i 's/AAAAAph/" . $phone . "/g'  " . $file);
    system("sed -i 's/BBBBBversion/" . $version . "/g' " . $file);
    system("sed -i 's/CCCCCjid/" . $cc . $phone . "/g' " . $file);
    system("sed -i 's/DDDDDcc/" . $cc . "/g' " . $file);
    system("sed -i 's/EEEEEpushname/" . $name . "/g' " . $file);
}
function readobj($data)
{

    $data = "[" . $data . "]";
    $data = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data);
    $data =  preg_replace('/[[:cntrl:]]/', '', $data);

    $new = json_decode($data, true);
    if ($new === null) {
     
        // json is malformed
        // you can modify the data here as needed
        $fixedJson = str_replace('}{"', '},{"', $data);
        $new = json_decode($fixedJson, true);
        if ($new == null) {
            // re-encode the data as a JSON string
            echo "not a json format " . json_last_error();
            die();
            // echo json_encode($fixedData);
        }
    }
     $filename = time();
    // print_r($data);
    if (sizeof($new) < 1) {
        echo "error in size " . sizeof($new);
        die();
    }
    mkdir("../temp/emulator/" . $filename);
    foreach ($new as $nb) {
        //var_dump($nb);
        if(isset($nb['rc2']) &&
        isset($nb["pushName"]) &&
        isset($nb["keystore"]) &&
        isset($nb["me"]) )
        {
            $rc2 = $nb['rc2'];
            $me = $nb['me'];
            $accname = $nb["pushName"];
            $keystore = $nb['keystore'];

        }
        else{
            continue;
        }
        $me = str_replace('-', '+', $me);
        $me = base64_decode($me);
        $keystore = str_replace('-', '+', $keystore);
        $keystore = base64_decode($keystore);
        preg_match_all('!\d+!', $me, $phone);
        //var_dump($phone);
        $phone = $phone[0];
       
        if (!$accname) $accname = "waAccount";
       // $version = $nb['waVersion'];
        $cc = $phone[0];

        $phone_without_cc = $phone[2];

        // echo $phone."<br>".$cc."<br>".$phone_without_cc;
        // die();
        //var/www/html/sms-platform/clientfxn
        $phonePath = "../temp/emulator/" . $filename . "/" . $phone[1];
        mkdir($phonePath);
        system("cp -a ../temp/com.whatsapp " . $phonePath);
        file_put_contents($phonePath . "/com.whatsapp/files/me", $me);
        file_put_contents($phonePath . "/com.whatsapp/files/rc2", $rc2);
        file_put_contents($phonePath . "/com.whatsapp/shared_prefs/keystore.xml", $keystore);
        xmlregisterphone($phonePath . "/com.whatsapp/shared_prefs/", $cc, $phone_without_cc);
        xmlpreflight($phonePath . "/com.whatsapp/shared_prefs/", $cc, $phone_without_cc);
        # sed($phonePath. "/com.whatsapp/shared_prefs/", $cc, $phone_without_cc,$accname,$version);

        //    zipanddownload("temp/emulator/".$phone[1]."/","temp/emulator/".$phone[1].".zip");
        //echo "rc2: ".$rc2. "<br> me: ".$me."<br> key: ".$keystore."<br>phone: ".$phone[1];
        //var_dump($phone);
        // break;

    }
    if (file_exists("../temp/emulator/" . $filename)) {
        zipanddownload("../temp/emulator/" . $filename, "../temp/emulator/" . $filename . ".zip");
    } else {
        echo "The file $filename does not exist";
    }
}
function zipanddownload($dir, $zip_file)
{
    // Get real path for our folder
    $rootPath = realpath($dir);

    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        if (!$file->isDir()) {
            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        } else {
            if ($relativePath !== false) {
                $zip->addEmptyDir($relativePath);
            }
        }
    }

    // Zip archive will be created only after closing object
    $zip->close();
    system("rm -r " . $dir);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($zip_file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zip_file));
    readfile($zip_file);
}
function xmlpreflight($dirname, $cc, $phone)
{

    $dom = new DOMDocument();
    $dom->load($dirname . 'com.whatsapp_preferences_light.xml');
    $library = $dom->documentElement;
    //echo $library->childNodes->item(11)->nodeValue;
    $library->childNodes->item(11)->nodeValue = $phone;
    $library->childNodes->item(43)->nodeValue = $cc . $phone;
    $library->childNodes->item(89)->nodeValue = $cc;
    // 2nd way #$library->getElementsByTagName('book')->item($cnt-1)->getElementsByTagName('title')->item(0)->nodeValue .= ' Series';

    // 3rd Way
    // $library->childNodes->item($cnt-1)->childNodes->item(0)->nodeValue .= ' Series';
    //header("Content-type: text/xml");
    $dom->save($dirname . 'com.whatsapp_preferences_light.xml');
}

function xmlregisterphone($dirname, $cc, $phone)
{
    $dom = new DOMDocument();
    $dom->load($dirname . 'registration.RegisterPhone.xml');
    $library = $dom->documentElement;
    //var_dump($library);
    $cnt = $library->childNodes->length;
    //echo $cnt;
    $library->childNodes->item(1)->nodeValue = $phone;
    $library->childNodes->item(7)->nodeValue = $phone;
    $library->childNodes->item(11)->nodeValue = $cc;
    $library->childNodes->item(13)->nodeValue = $cc;
    // 2nd way #$library->getElementsByTagName('book')->item($cnt-1)->getElementsByTagName('title')->item(0)->nodeValue .= ' Series';

    // 3rd Way
    // $library->childNodes->item($cnt-1)->childNodes->item(0)->nodeValue .= ' Series';
    //header("Content-type: text/xml");
    $dom->save($dirname . 'registration.RegisterPhone.xml');
}

function isJson($string)
{

    json_decode($string, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);

    return json_last_error() === JSON_ERROR_NONE;
}
