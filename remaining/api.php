<?php
function getdata($name){

    $users =  array( 
      'Jikatel' => '3441U1f804a42b6d20f2b5ab3b87e8fbd9d24',
      'digitalsim' =>'8c203af7fe0f1496d5dfe5567a63301ab49fe3f9',
      'lnovaro' =>'3621U27b0c45b7c77d614bbc55210c5176064',
      'saliba' =>'3743U404cbb9722e040cdbe2b2c8701cc429a',
      'All' => 'd87c448044defb778f33158d8ccf94a20531d600'
    );
    $key = $users[$name];
    $url = "https://45-79-8-214.ip.linodeusercontent.com/ruapi/rusender.php?key=".$key."&action=GET_SERVICES";
    
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
      ),
    ));
    $response = curl_exec($curl);
    if(curl_error($curl)) {
      $error_msg = curl_error($curl);
      echo "cURL Error: " . $error_msg;
    }
    
    curl_close($curl);
    $data = json_decode($response, true);

    $country_list = array(
      'Afghanistan' => 'AF',
      'Albania' => 'AL',
      'Algeria' => 'DZ',
      'American Samoa' => 'AS',
      'Andorra' => 'AD',
      'Angola' => 'AO',
      'Anguilla' => 'AI',
      'Antarctica' => 'AQ',
      'Antigua and Barbuda' => 'AG',
      'Argentina' => 'AR',
      'Armenia' => 'AM',
      'Aruba' => 'AW',
      'Australia' => 'AU',
      'Austria' => 'AT',
      'Azerbaijan' => 'AZ',
      'Bahamas' => 'BS',
      'Bahrain' => 'BH',
      'Bangladesh' => 'BD',
      'Barbados' => 'BB',
      'Belarus' => 'BY',
      'Belgium' => 'BE',
      'Belize' => 'BZ',
      'Benin' => 'BJ',
      'Bermuda' => 'BM',
      'Bhutan' => 'BT',
      'Bolivia' => 'BO',
      'Bosnia and Herzegovina' => 'BA',
      'Botswana' => 'BW',
      'Brazil' => 'BR',
      'British Indian Ocean Territory' => 'IO',
      'British Virgin Islands' => 'VG',
      'Brunei' => 'BN',
      'Bulgaria' => 'BG',
      'Burkina Faso' => 'BF',
      'Burundi' => 'BI',
      'Cambodia' => 'KH',
      'Cameroon' => 'CM',
      'Canada' => 'CA',
      'Cape Verde' => 'CV',
      'Cayman Islands' => 'KY',
      'Central African Republic' => 'CF',
      'Chad' => 'TD',
      'Chile' => 'CL',
      'China' => 'CN',
      'Christmas Island' => 'CX',
      'Cocos Islands' => 'CC',
      'Colombia' => 'CO',
      'Comoros' => 'KM',
      'Cook Islands' => 'CK',
      'Costa Rica' => 'CR',
      'Croatia' => 'HR',
      'Cuba' => 'CU',
      'Curacao' => 'CW',
      'Cyprus' => 'CY',
      'Czech Republic' => 'CZ',
      'Democratic Republic of the Congo' => 'CD',
      'Denmark' => 'DK',
      'Djibouti' => 'DJ',
      'Dominica' => 'DM',
      'Dominican Republic' => 'DO',
      'East Timor' => 'TL',
      'Ecuador' => 'EC',
      'Egypt' => 'EG',
      'El Salvador' => 'SV',
      'Equatorial Guinea' => 'GQ',
      'Eritrea' => 'ER',
      'Estonia' => 'EE',
      'Ethiopia' => 'ET',
      'Falkland Islands' => 'FK',
      'Faroe Islands' => 'FO',
      'Fiji' => 'FJ',
      'Finland' => 'FI',
      'France' => 'FR',
      'French Polynesia' => 'PF',
      'Gabon' => 'GA',
      'Gambia' => 'GM',
      'Georgia' => 'GE',
      'Germany' => 'DE',
      'Ghana' => 'GH',
      'Gibraltar' => 'GI',
      'Greece' => 'GR',
      'Greenland' => 'GL',
      'Grenada' => 'GD',
      'Guam' => 'GU',
      'Guatemala' => 'GT',
      'Guernsey' => 'GG',
      'Guinea' => 'GN',
      'Guineasau' => 'GW',
      'Guyana' => 'GY',
      'Haiti' => 'HT',
      'Honduras' => 'HN',
      'Hong Kong' => 'HK',
      'Hungary' => 'HU',
      'Iceland' => 'IS',
      'India' => 'IN',
      'Indonesia' => 'ID',
      'Iran' => 'IR',
      'Iraq' => 'IQ',
      'Ireland' => 'IE',
      'Isle of Man' => 'IM',
      'Italy' => 'IT',
      'Ivory Coast' => 'CI',
      'Jamaica' => 'JM',
      'Japan' => 'JP',
      'Jersey' => 'JE',
      'Jordan' => 'JO',
      'Kazakhstan' => 'KZ',
      'Kenya' => 'KE',
      'Kiribati' => 'KI',
      'Kosovo' => 'XK',
      'Kuwait' => 'KW',
      'Kyrgyzstan' => 'KG',
      'Laos' => 'LA',
      'Latvia' => 'LV',
      'Lebanon' => 'LB',
      'Lesotho' => 'LS',
      'Liberia' => 'LR',
      'Libya' => 'LY',
      'Liechtenstein' => 'LI',
      'Lithuania' => 'LT',
      'Luxembourg' => 'LU',
      'Macau' => 'MO',
      'Macedonia' => 'MK',
      'Madagascar' => 'MG',
      'Malawi' => 'MW',
      'Malaysia' => 'MY',
      'Maldives' => 'MV',
      'Mali' => 'ML',
      'Malta' => 'MT',
      'Marshall Islands' => 'MH',
      'Mauritania' => 'MR',
      'Mauritius' => 'MU',
      'Mayotte' => 'YT',
      'Mexico' => 'MX',
      'Micronesia' => 'FM',
      'Moldova' => 'MD',
      'Monaco' => 'MC',
      'Mongolia' => 'MN',
      'Montenegro' => 'ME',
      'Montserrat' => 'MS',
      'Morocco' => 'MA',
      'Mozambique' => 'MZ',
      'Myanmar' => 'MM',
      'Namibia' => 'NA',
      'Nauru' => 'NR',
      'Nepal' => 'NP',
      'Netherlands' => 'NL',
      'Netherlands Antilles' => 'AN',
      'New Caledonia' => 'NC',
      'New Zealand' => 'NZ',
      'Nicaragua' => 'NI',
      'Niger' => 'NE',
      'Nigeria' => 'NG',
      'Niue' => 'NU',
      'North Korea' => 'KP',
      'Northern Mariana Islands' => 'MP',
      'Norway' => 'NO',
      'null' => '0',
      'Oman' => 'OM',
      'Pakistan' => 'PK',
      'Palau' => 'PW',
      'Palestine' => 'PS',
      'palestine' => 'PS',
      'Panama' => 'PA',
      'Papua New Guinea' => 'PG',
      'Paraguay' => 'PY',
      'Peru' => 'PE',
      'Philippines' => 'PH',
      'Pitcairn' => 'PN',
      'Poland' => 'PL',
      'Portugal' => 'PT',
      'Puerto Rico' => 'PR',
      'Qatar' => 'QA',
      'Republic of the Congo' => 'CG',
      'Reunion' => 'RE',
      'Romania' => 'RO',
      'Russia' => 'RU',
      'Rwanda' => 'RW',
      'Saint Barthelemy' => 'BL',
      'Saint Helena' => 'SH',
      'Saint Kitts and Nevis' => 'KN',
      'Saint Lucia' => 'LC',
      'Saint Martin' => 'MF',
      'Saint Pierre and Miquelon' => 'PM',
      'Saint Vincent and the Grenadines' => 'VC',
      'Samoa' => 'WS',
      'San Marino' => 'SM',
      'Sao Tome and Principe' => 'ST',
      'Saudi Arabia' => 'SA',
      'Senegal' => 'SN',
      'Serbia' => 'RS',
      'Seychelles' => 'SC',
      'Sierra Leone' => 'SL',
      'Singapore' => 'SG',
      'Sint Maarten' => 'SX',
      'Slovakia' => 'SK',
      'Slovenia' => 'SI',
      'Solomon Islands' => 'SB',
      'Somalia' => 'SO',
      'South Africa' => 'ZA',
      'South Korea' => 'KR',
      'South Sudan' => 'SS',
      'Spain' => 'ES',
      'Sri Lanka' => 'LK',
      'Sudan' => 'SD',
      'Suriname' => 'SR',
      'Svalbard and Jan Mayen' => 'SJ',
      'Swaziland' => 'SZ',
      'Sweden' => 'SE',
      'Switzerland' => 'CH',
      'Syria' => 'SY',
      'Taiwan' => 'TW',
      'Tajikistan' => 'TJ',
      'Tanzania' => 'TZ',
      'Thailand' => 'TH',
      'Togo' => 'TG',
      'Tokelau' => 'TK',
      'Tonga' => 'TO',
      'Trinidad and Tobago' => 'TT',
      'Tunisia' => 'TN',
      'Turkey' => 'TR',
      'Turkmenistan' => 'TM',
      'Turks and Caicos Islands' => 'TC',
      'Tuvalu' => 'TV',
      'U.S. Virgin Islands' => 'VI',
      'Uganda' => 'UG',
      'Ukraine' => 'UA',
      'United Arab Emirates' => 'AE',
      'United Kingdom' => 'GB',
      'United States' => 'US',
      'Uruguay' => 'UY',
      'Uzbekistan' => 'UZ',
      'Vanuatu' => 'VU',
      'Vatican' => 'VA',
      'Venezuela' => 'VE',
      'Vietnam' => 'VN',
      'Wallis and Futuna' => 'WF',
      'Western Sahara' => 'EH',
      'Yemen' => 'YE',
      'Zambia' => 'ZM',
      'Zimbabwe' => 'ZW');



      
    $applications = array( 
      'wa' => 'Whatsapp',
      'go' =>'Google',
      'tg' =>'Telegram',
      'fb' =>'Facebook',
      'mm' =>'Microsoft',
      'wb' =>'Wechat',
      'ig' =>'Instagram',
      'yl' =>'Yalla',
      'vi' =>'Viber');

    $extracted_data = array();

    foreach ($data['countryList'] as $itms) {
      $countryName = $itms['country'];
      $countryCode = $country_list[$countryName];
      $operatorMap = $itms['operatorMap']['Any'];
      foreach ($applications as $key => $value) {
        if (isset($operatorMap[$key])) {
          $applicationName = $value;
          $applicationCode = $key;
          $applicationCount = $operatorMap[$key];
          $extracted_data[] =  [
            'application' => $applicationName,
            'country_code' =>$countryName ." - ".$countryCode,
            'count' =>$applicationCount,
            'app_code' =>$applicationCode];
        }    
      }
    }
    usort($extracted_data, function($a, $b) {
      return strcmp($a['application'], $b['application']);
    });
    return $extracted_data; 

}

if(isset($_POST['action'],$_POST['option'])){
   
    if($_POST['action'] == "get"){
       
        $name = $_POST['option'];
        $res =  getdata($name);
        echo json_encode($res);
    }
}
