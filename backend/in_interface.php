<?php

session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}

echo "server running OK";
require_once 'inAPI/middelwareapi_inAPI.php';
$in_api = new in_api();
$_MY = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_MY = $_POST;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $_MY = $_GET;
    }
}

if ($_MY) {
    $result = (object) [
        'ResponseCode' => 0,
        'Msg' => 'OK',
        'Result' => null,
    ];

    if (isset($_MY['api_key'])) {
        $api_key = $_MY['api_key'];
        $_check = $in_api->check_api_key($api_key);
        if ($_check == true) {
            //var_dump( $_MY );
            if (isset($_MY['action'])) {
                if ($_MY['action'] == 'getbalance') {
                    $result->Result = $in_api->getbalance($api_key);

                    echo json_encode($result);
                    die();
                } elseif ($_MY['action'] == 'getapplist') {
                    $result->Result = $in_api->getapplist();
                    echo json_encode($result);
                    die();
                } elseif ($_MY['action'] == 'getnumber') {
                    if ((isset($_MY['appId']))
                        && (isset($_MY['country']))
                    ) {
                        $carrier = null;
                        if (isset($_MY['carrier'])) {
                            $carrier = $_MY['carrier'];
                        }
                        $result->Result = $in_api->getnumber($api_key, $_MY['appId'], $_MY['country'], $carrier = $carrier);
                        echo json_encode($result);
                        die();
                    }
                } elseif ($_MY['action'] == 'code') {
                    if (isset($_MY['id'])) {
                        $result->Result = $in_api->get_verificationcode($api_key, $_MY['id']);
                        echo json_encode($result);
                        die();
                    }
                }
            }
        }
    }
    $result->ResponseCode = 1;
    $result->Msg = 'ERORR';
    $result->Result = 'Bad request';

    echo json_encode($result);
}
