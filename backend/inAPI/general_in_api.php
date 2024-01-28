<?php

class IN_API
{

    private function api_call($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_COOKIE => "lang=en-US;",
            CURLOPT_TIMEOUT => 10
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err != null) {
            #TODO error to log
            // echo 'curl error';
            // echo $err;
            return null;
        }
        return $response;
    }

    public function get_balance($url)
    {
        return $this->api_call($url);
    }

    public function getapplist($url)
    {
        return $this->api_call($url);
    }
    public function get_any($url)
    {
        return $this->api_call($url);
    }


    public function getnumber($url)
    {
        return $this->api_call($url);
    }
}
