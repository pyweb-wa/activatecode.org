<?php
   $accounts ='[
    {
          "login": "info@myroutetester.com",
          "password": "Inf@321",
          "url":"http://35.195.32.6:8085",
          "port":"8085",
          "name":"35"
      }
      ,
                {
          "login": "andrew@jikatel.com",
          "password": "4BEF53",
          "url":"http://34.105.173.84:8085",
          "port":"8085",
          "name":"34"
      }
  ]';
  $accounts = json_decode($accounts,true);
  $scriptPath = dirname(__FILE__);
  $logging = $scriptPath."/log.txt";
  ?>