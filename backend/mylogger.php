<?php

class MyLogger
{
    /// const FILENAME = dirname(__FILE__)."/logging/logging.log";
    //"/var/www/html/sms-platform/logging/logging.log";

    public function Add($error, $script_name = null,$filename="logging.log")
    {
        $logPath = "/var/www/smsmarket/logging/";

        $log = "[-] ".(string) $error . " ==> script: " . $script_name . "  datetime: " . date('m/d/Y h:i:s a', time()) . "\n";
        $filepath =  $logPath.$filename; 
        file_put_contents($filepath, $log, FILE_APPEND);
    }
}
