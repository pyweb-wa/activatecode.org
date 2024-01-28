<?php
require_once "validate_token.php";
if (!checkTokenInDatabase()) {
    header('Location: index.php');
    exit(); 
}
if (!isset($_GET['code'])) {
    die();
}
if ($_GET['code'] != "azxsdcfrevgtrrd4fg3") {
    die();
}

include '/var/www/smsmarket/html/backend/config.php';

try {
    // Fetch data from the table
    $sql = "SELECT * FROM `countries_control`";
    $result = $pdo->query($sql);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    // Ensure a source parameter is present
    if (isset($_GET['source'])) {
        $source = urldecode($_GET['source']);

        try {
            $deleteSql = "DELETE FROM `countries_control` WHERE `source` = ?";
            $stmt = $pdo->prepare($deleteSql);
            $stmt->execute([$source]);
            echo "Record deleted successfully";
        } catch (PDOException $e) {
            echo "Error deleting record: " . $e->getMessage();
        }


        header("Location: " . $_SERVER['PHP_SELF'] . "?code=" . urlencode($_GET['code']));
        exit;
    }
}



// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $source = $_POST['delete'];
    try {
        $deleteSql = "DELETE FROM `countries_control` WHERE `source` = ?";
        $stmt = $pdo->prepare($deleteSql);
        $stmt->execute([$source]);
        echo "Record deleted successfully";
    } catch (PDOException $e) {
        echo "Error deleting record: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    //var_dump($_POST);die();
    // Process the form data and insert into the table
    $source = $_POST['source'];
    $country = $_POST['country'];
    $source = $country . "_" . $source;

    try {
        $insertSql = "INSERT INTO `countries_control` (`source`,`country_id`,`country`,`country_char`,`country_code` ) VALUES (?, (SELECT id from countryList Where country_char = ?),(SELECT country from countryList Where country_char = ?),? ,(SELECT country_code from countryList Where country_char = ?) )";
        $stmt = $pdo->prepare($insertSql);
        $stmt->execute([$source, $country, $country, $country, $country]);

        // $sql2 = "SELECT tokens.access_Token FROM users join tokens on tokens.userID = users.Id   WHERE is_deleted=0 ORDER BY name ASC";
        // $users_tokens = $pdo->query($sql2)->fetchAll(PDO::FETCH_ASSOC);        
        // $stmt2 = $pdo->prepare("INSERT IGNORE INTO `cust_cntry_perm` (usertoken , country) VALUES (?, ?)");
        // foreach ($$users_tokens as $usertoken) {
        //     $user_token = $usertoken['access_Token'];
        //     $stmt2->execute([$user_token , $source]);
        //     if ($redis){
        //         $key = "CountryPerm:" . $user_token;
        //         $redis->sAdd( $key,  $source);
        //     }
        // }


        header("Location: " . $_SERVER['PHP_SELF'] . "?code=" . urlencode($_GET['code']));
        exit;
    } catch (PDOException $e) {
        echo "Error adding record: " . $e->getMessage();
    }
}
 
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOX Information</title>
    <link rel="stylesheet" href="./css/fontawesome-all.min.css">
    <link rel="stylesheet" href="./css/toastr.min.css">
    <link rel="stylesheet" href="./css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"/>

</head>
<?php include "menu.php"; ?>
<body class="hold-transition sidebar-mini" ng-app="countryapp" ng-controller="CountryController">
    <div class="wrapper" >
        <div class="content-wrapper">
            <div class="container mt-4" style="padding: 20px;">
                <div class="row">
                    <div class="col"><h2>BOX Information</h2></div>
                    <div class="col"><button style="float: right;" class="btn btn-primary" id="openModalBtn">Add Record</button></div>
                </div>                
                <!-- Display table data -->
                <table id="table_id" class="table table-striped table-info">
                    <thead>
                        <tr>
                            <th>Country ID</th>
                            <th>Source</th>
                            <th>Enabled</th>
                            <th>Start</th>
                            <th>Stop</th>
                            <th>MStart</th>
                            <th>MStop</th>
                            <th>Created Time</th>
                            <th>Reactivate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $row) {
                            echo "<tr>";
                            echo "<td>{$row['country_id']}</td>";
                            echo "<td>{$row['source']}</td>";
                            echo "<td>{$row['enabled']}</td>";
                            echo "<td>{$row['start']}</td>";
                            echo "<td>{$row['stop']}</td>";
                            echo "<td>{$row['mstart']}</td>";
                            echo "<td>{$row['mstop']}</td>";
                            echo "<td>{$row['created_time']}</td>";
                            echo "<td>{$row['reactivate']}</td>";
                            echo "<td>
                    <button class='btn btn-danger' onclick=\"confirmDelete('" . "?code=" . urlencode($_GET['code']) . "&action=delete&source=" . urlencode($row['source']) . "','" . $row['source'] . "')\">Delete</button>
                  </td>";
                  echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog" aria-labelledby="addRecordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRecordModalLabel">Add Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="source">Source:</label>
                                <input type="text" class="form-control" id="source" name="source" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Country:</label>
                                <select data-flag="true" name="countryy">
                                <option value="AF">Afghanistan <li class="fi fi-gr" aria-hidden="true"></li></option>
                                </select>
                                <select class="selectpicker countrypicker f16" data-live-search="true" data-flag="true" name="country"></select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add">Add Record</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.min.js"></script>
    <script src="./js/adminlte.min.js"></script>
    <script src="./js/select_country.js"></script> 
    <script>
        document.getElementById('openModalBtn').addEventListener('click', function() {
            $('#addRecordModal').modal('show');
        });

        function confirmDelete(deleteUrl, source) {
            if (confirm("Are you sure you want to delete  [ " + source + " ]?")) {
                location.href = deleteUrl;
            } else {}
        }
    </script>
</body>

</html>