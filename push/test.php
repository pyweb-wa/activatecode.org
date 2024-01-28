<?php

$data = "Your WhatsApp Business code 978-280 You can also tap this link to verify your phone: v.whatsapp.com/978280 Don't share this code with others";
$code = "";
if (strpos(strtolower($data), "whatsapp") !== false 
|| strpos(strtolower($data), "can also tap on this link") !== false 
 ) {
        $pattern = '/\d{3}-\d{3}/';
        preg_match($pattern,$data, $matches);
        if(sizeof($matches) >=1){
        $number = $matches[0];
        $code = str_replace("-", "", $number);
        
        }
    }
echo $code;