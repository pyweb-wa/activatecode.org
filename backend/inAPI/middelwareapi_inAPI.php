<?php
require_once 'simcode_class.php';

class out_api
{

    public function __construct()
    {
        $this->simcode_api = new simcode_api();
    }

    public function check_api_key($api_key)
    {
        return json_decode('{"balance":100}');
    }

    public function getbalance($api_name)
    {
        if ($api_name == "simcode") {
            $data = $this->simcode_api->simcode_getbalance();
            return $data;
        }

    }

    public function getapplist($api_name)
    {
        if ($api_name == "simcode") {
            $data = $this->simcode_api->simcode_getapplist();
            return $data;
        }

    }

}
