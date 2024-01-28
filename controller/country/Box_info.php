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

function get_app(){
    include '/var/www/smsmarket/html/backend/config.php';
    $sql = "SELECT * FROM `application_code`";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $res;


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
        require_once '/var/www/smsmarket/html/backend/redisconfig.php';
        $deleteSql = "DELETE FROM `countries_control` WHERE `source` = ?";
        $stmt = $pdo->prepare($deleteSql);
        $stmt->execute([$source]);



        $sql2 = "SELECT tokens.access_Token FROM users join tokens on tokens.userID = users.Id   WHERE is_deleted=0 ORDER BY name ASC";
        $users_tokens = $pdo->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
        $accessTokens = [];
        
        foreach ($users_tokens as $userToken) {
            $accessTokens[] = $userToken['access_Token'];
            if ($redis){
                $key = "CountryPerm:" . $userToken['access_Token'];
                $redis->sRem( $key,  $source);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM `cust_cntry_perm` WHERE usertoken  IN (" . implode(',', array_fill(0, count($accessTokens), '?')) . ") AND country = ?");
        $stmt->execute(array_merge($accessTokens, [$source]));
      

        echo "Record deleted successfully";
    } catch (PDOException $e) {
        echo "Error deleting record: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    //var_dump($_POST);die();
    // Process the form data and insert into the table
    require_once '/var/www/smsmarket/html/backend/redisconfig.php';
    $source = $_POST['source'];
    $country = $_POST['country'];
    $source = $country."_".$source;
    $application = strtolower($_POST['application']);

    try {
        $insertSql = "INSERT INTO `countries_control` (`source`,`country_id`,`country`,`country_char`,`country_code`,`application` ) VALUES (?, (SELECT id from countryList Where country_char = ?),(SELECT country from countryList Where country_char = ?),? ,(SELECT country_code from countryList Where country_char = ?),? )";
        $stmt = $pdo->prepare($insertSql);
        $stmt->execute([$source,$country,$country,$country,$country,$application]);

        $sql2 = "SELECT tokens.access_Token FROM users join tokens on tokens.userID = users.Id   WHERE is_deleted=0 ORDER BY name ASC";
        $users_tokens = $pdo->query($sql2)->fetchAll(PDO::FETCH_ASSOC);        
        $stmt2 = $pdo->prepare("INSERT IGNORE INTO `cust_cntry_perm` (usertoken , country) VALUES (?, ?)");
        foreach ($users_tokens as $usertoken) {
            $user_token = $usertoken['access_Token'];
            $stmt2->execute([$user_token , $source]);
            if ($redis){
                $key = "CountryPerm:" . $user_token;
                $redis->sAdd( $key,  $source);
            }
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?code=" . urlencode($_GET['code']));
        exit;
    } catch (PDOException $e) {
        echo "Error adding record: " . $e->getMessage();
    }
}

// Close the connection
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOX Information</title>

    

   
  <link rel="stylesheet" href="//unpkg.com/bootstrap-select@1.12.4/dist/css/bootstrap-select.min.css" type="text/css" />
  <link rel="stylesheet" href="//unpkg.com/bootstrap-select-country@4.0.0/dist/css/bootstrap-select-country.min.css" type="text/css" />
  <link rel="stylesheet" href="./css/fontawesome-all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="./css/toastr.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">

   
    <link rel="stylesheet" href="./css/adminlte.min.css">


    <!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">-->
    <link rel="stylesheet" href="//unpkg.com/bootstrap@3.3.7/dist/css/bootstrap.min.css" type="text/css" /> 
    <script src="//unpkg.com/jquery@3.4.1/dist/jquery.min.js"></script>
  <script src="//unpkg.com/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
  <script src="//unpkg.com/bootstrap-select@1.12.4/dist/js/bootstrap-select.min.js"></script>
  <script src="//unpkg.com/bootstrap-select-country@4.0.0/dist/js/bootstrap-select-country.min.js"></script>

</head>



<body class="hold-transition sidebar-mini" ng-app="countryapp" ng-controller="CountryController">

  
    
    <?php include "menu.php"; ?>

        <div class="content-wrapper">
      
        <div class="container mt-4">
    <h2>BOX Information</h2>

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
            <th>Application</th>
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
            echo "<td>{$row['application']}</td>";
            echo "<td>
                    <button class='btn btn-danger' onclick=\"confirmDelete('" . "?code=" . urlencode($_GET['code']) . "&action=delete&source=" . urlencode($row['source']) . "','" . $row['source'] . "')\">Delete</button>
                  </td>";

            echo "</tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Add Record Form -->
    <h2>Add Record</h2>
    <form method="post" action="">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="source">Source:</label>
            <input type="text" class="form-control" id="source" name="source" required>
        </div>

        <div class="form-group col-md-3">
            <label for="country">Country:</label>
            <select class="selectpicker countrypicker form-control" data-live-search="true"  data-flag="true" name="country"></select>
        </div>

        <div class="form-group col-md-3">
            <label for="Application">Application:</label>
            <select class="selectpicker form-control" data-live-search="true"   name="application">
                <?php
                $applications = get_app();
                foreach ($applications as $row) {
                    $selected = ($row['id'] == "wa") ? 'selected' : '';
                    echo '<option value="' . $row['application'] . '" ' . $selected . '>' . $row['application'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" name="add">Add Record</button>
</form>
</div>

    </div>
   
    </div>
    <!-- <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.2.0
            </div>
            <strong>Copyright &copy; 2015-2023 <a href="#">ProDev.io</a>.</strong> All rights reserved.
        </footer> -->
        <script>
    function confirmDelete(deleteUrl, source) {
        if (confirm("Are you sure you want to delete  [ " + source + " ]?")) {
            location.href = deleteUrl;
        } else {}
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select-country@4.2.0/dist/js/bootstrap-select-country.min.js"></script>
<script src="./js/adminlte.min.js"></script>
</body>
</html>