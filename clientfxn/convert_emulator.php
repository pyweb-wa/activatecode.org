<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}
$user = (int) $_SESSION['id'];
$filename = '';
if (isset($_GET['fname'])) {
    $filename = $_GET['fname'];
} else if (isset($_POST['fname'])) {
    $filename = $_POST['fname'];
} else {
    echo "forbidden 405";
    die();

}

$allowedPages = [];
$items = getallowedPages($user);

foreach ($items as $item) {
    array_push($allowedPages, $item['srv_req_id']);
}

$file_path = "/var/www/html/oldChannels/";
if (!file_exists($file_path)) {
    $file_path = "../oldChannels/";
}

$check = false;
if (in_array($filename, $allowedPages)) { // && file_exists($file_path . $filename )) {
    StartConvert($filename,$file_path);

}else{
    echo "forbidden 403";
    die();

}

function StartConvert($filename, $path)
{
    if (file_exists($path."emulator/" . $filename . ".zip")) {
        //echo "exist";
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($path."emulator/" . $filename . ".zip"));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path."emulator/" . $filename . ".zip"));
        readfile($path."emulator/" . $filename . ".zip");

    } else {

        $data = file_get_contents($path . $filename);
        $data = json_decode($data, true);
        $path = $path."emulator/";
        mkdir($path . $filename);
        foreach ($data['Data'] as $nb) {
            //var_dump($nb);
            if (array_key_exists("a",$nb)){
                $rc2 = $nb['a'];
                $me = $nb['b'];
                $accname = $nb['n'];
                if(!$accname) $accname = "waAccount";
                $version = $nb['o'];
                $me = str_replace('-', '+', $me);
                $me = base64_decode($me);
                $keystore = $nb['c'];
                $keystore = str_replace('-', '+', $keystore);
                $keystore = base64_decode($keystore);
                preg_match_all('!\d+!', $me, $phone);
            //var_dump($phone);
                $phone = $phone[0];
                $cc = $phone[0];
                $phone_without_cc = $phone[2];
            }else{
                $rc2 = $nb['rc'];
                $me = $nb['me'];
                $accname = $nb['pn'];
                if(!$accname) $accname = "waAccount";
                $version = $nb['wa_version'];
                $me = str_replace('-', '+', $me);
                $me = base64_decode($me);
                $keystore = $nb['keystore'];
                $keystore = str_replace('-', '+', $keystore);
                $keystore = base64_decode($keystore);
                preg_match_all('!\d+!', $me, $phone);
            //var_dump($phone);
                $phone = $phone[0];
                $cc = $phone[0];
                $phone_without_cc = $phone[2];
            }
            mkdir($path . $filename . "/" . $phone[1]);
            //system("cp -a " . $path . "com.whatsapp " . $path . $filename . "/" . $phone[1] . "/");
            recursive_copy($path . "com.whatsapp",$path . $filename . "/" . $phone[1] . "/com.whatsapp");
            file_put_contents($path . $filename . "/" . $phone[1] . "/com.whatsapp/files/me", $me);
            file_put_contents($path . $filename . "/" . $phone[1] . "/com.whatsapp/files/rc2", $rc2);
            file_put_contents($path . $filename . "/" . $phone[1] . "/com.whatsapp/shared_prefs/keystore.xml", $keystore);
            xmlregisterphone($path . $filename . "/" . $phone[1] . "/com.whatsapp/shared_prefs/", $cc, $phone_without_cc);
            sed($path . $filename . "/" . $phone[1] . "/com.whatsapp/shared_prefs/", $cc, $phone_without_cc,$accname,$version);

        }
        zipanddownload($path . $filename, $path . $filename . ".zip");
    }

}
function sed($file,$cc, $phone,$name,$version)
{
    $file = $file."com.whatsapp_preferences_light.xml";

    system("sed -i 's/AAAAAph/".$phone."/g'  ".$file);
    system("sed -i 's/BBBBBversion/".$version."/g' ".$file );
    system("sed -i 's/CCCCCjid/".$cc.$phone."/g' ".$file );
    system("sed -i 's/DDDDDcc/".$cc."/g' ".$file );
    system("sed -i 's/EEEEEpushname/".$name."/g' ".$file );


}
function zipanddownload($dir, $zip_file)
{

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
    //deleteDir($dir);
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

function getallowedPages($id){
    include './../backend/config.php';
    $query = "SELECT * FROM `channels_log` WHERE Id_user = ?  order by TimeStmp desc";


    $arrayParams = [];
    $stmt =$pdo->prepare($query);
    $stmt->execute([$id]);
    $items = $stmt->fetchall();
    
    return $items;
    //array_push

}

function recursive_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recursive_copy($src .'/'. $file, $dst .'/'. $file);
			} else {
				copy($src .'/'. $file,$dst .'/'. $file);
			}
		}
	}
	closedir($dir);
}


function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}