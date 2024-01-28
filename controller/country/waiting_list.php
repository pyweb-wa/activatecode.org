<?php
require_once "validate_token.php";
if (!checkTokenInDatabase()) {
    header('Location: index.php');
    exit(); 
}
ini_set('memory_limit', '512M');
//<?php

if (!isset($_GET['code'])) {
    die();
}
if ($_GET['code'] != "azxsdcfrevgtrrd4fg3") {
    die();
}

include '/var/www/smsmarket/html/backend/redisconfig.php';

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    // Ensure a source parameter is present
    if (isset($_GET['source'])) {
        $sourceToDelete = urldecode($_GET['source']);
        $sourceList = json_decode($redis->get("source_list"), true);
        $keyIndex = array_search($sourceToDelete, $sourceList);
        if ($keyIndex !== false) {
            unset($sourceList[$keyIndex]);
            $redis->set("source_list", json_encode(array_values($sourceList)));
        }
        $redis->del($sourceToDelete);
        header("Location: " . $_SERVER['PHP_SELF'] . "?code=" . urlencode($_GET['code']));
        exit;
    }
}else if (isset($_GET['action']) && $_GET['action'] === 'download'){
    if (isset($_GET['source'])) {
        $sourceToDownload = urldecode($_GET['source']);
        $dt = $redis->get($sourceToDownload);
        $dt = json_decode($dt, true);
        if (!empty($dt) && count($dt)>0){
            $phoneNumbers = [];
            foreach ($dt as $jsonObject) {
                if (isset($jsonObject['phone_number'])) {
                    $phoneNumbers[] = $jsonObject['phone_number'];
                }
            }
            $phoneNumbersText = implode("\n", $phoneNumbers);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $_GET['source'] . '.txt"');
            echo $phoneNumbersText;
            exit; // Add exit to stop execution after sending file
        }
    }
}

$status = $redis->get("source_list");
$jsonList = json_decode($status, true);

$itemTotals = array();
foreach ($jsonList as $jsonItem) {
    $status = $redis->get($jsonItem);
    $status = json_decode($status, true);
    $count = 0;
    if ($status) {
        $count = count($status);
    }
    $data = array(
        'source' => $jsonItem,
        'total' => $count
    );
    $itemTotals[] = $data;
}
$redis->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usage Statistics</title>
    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
    <link rel="stylesheet" href="./css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/22.2.6/css/dx.light.css" />
    <link rel="stylesheet" href="./css/adminlte.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.22/datatables.min.css" />

</head>

<style>

    .table-container {
        max-height: 400px;
        /* Adjust as needed */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }



    .table-container thead th {
        position: sticky;
        /* Makes the table headers sticky */
        top: 0;
        /* Sticks the headers to the top of the container */
    }

    .styled-table,
    .styled-table th,
    .styled-table td {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Arial', sans-serif;
        color: #333;
        border: 1px solid #ddd;
    }

    .styled-table th,
    .styled-table td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        width: 25%;
        /* Divides the table equally into three columns */
        text-align: center;
        /* Centers the text within the columns */
        font-size: 1em;
        /* Adjust as needed */
    }

    .styled-table th {
        font-size: 1.1em;
        /* Make the header font size slightly larger */
        background-color: #343A40;
        color: white;
    }

    .styled-table tr:hover {
        background-color: #f5f5f5;
    }

    .styled-table tr:nth-child(odd) {
        background-color: white;
    }

    /* Style for light green rows */
    .styled-table tr:nth-child(even) {
        background-color: #D1D3D4;
    }
</style>

<body class="hold-transition sidebar-mini" ng-app="countryapp" ng-controller="CountryController">
<div class="wrapper">
    <?php include "menu.php"; ?>
    <div class="content-wrapper">
        <section class="content">
            <h2>Waiting List Table Results</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Total</th>
                        <th>Download</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itemTotals as $row) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['source']); ?></td>
                            <td><?php echo htmlspecialchars($row['total']); ?></td>
                            <td>
                            <button class="btn btn-link" onclick="Download('<?php echo "?code=" . urlencode($_GET['code']) . "&action=download&source=" . urlencode($row['source']); ?>','<?php echo $row['source']; ?>')">
                            <i class="fas fa-cloud-download-alt fa-lg" style="color: green;"></i>
                            </button>
                            </td>
                            <td>
                                <button class="btn btn-link" onclick="confirmDelete('<?php echo "?code=" . urlencode($_GET['code']) . "&action=delete&source=" . urlencode($row['source']); ?>','<?php echo $row['source']; ?>')"> 
                                <i class="fas fa-trash-alt fa-lg" style="color: red;"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.2.0
        </div>
        <strong>Copyright &copy; 2015-2023 <a href="#">ProDev.io</a>.</strong> All rights reserved.
    </footer>

</div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./js/bootstrap.bundle.min.js"></script>
<script src="./js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>


<script>
    $(document).ready(function() {
        // var dataTable = $('#table_id').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     pageLength: 20,
        //     "order": [
        //         [0, "desc"]
        //     ],
        //     "info": true,
        //     "dom": '<"top"f><"top"lp>rt<"bottom"lp><"top"f><"clear">',
        //     ajax: {
        //         "url": "Chile_api.php",
        //         "dataSrc": function(json) {
        //             $('#totalRecords').text(json.recordsTotal);
        //             $('#total_taked').text(json.recordsTotal_taked);
        //             return json.data;
        //         }
        //     }
        // });
        // function refreshTable() {
        //     dataTable.ajax.reload(null, false); 
        // }
        // setInterval(refreshTable, 2000);
    });

    function confirmDelete(deleteUrl, source) {
        if (confirm("Are you sure you want to delete  [ " + source + " ]?")) {
            location.href = deleteUrl;
        } else {}
    }
    function Download(downloadsource,source){
        location.href = downloadsource;
    }
</script>

</html>