
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['email'])) {
    header('Location:login.php');
    die();
}

if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
}
require_once './../../backend/config.php';

$GLOBALS['pdo'] = $pdo;
if (isset($_POST["action"])) {
    if ($_POST["action"] == "getcountries") {
        echo (json_encode(getCountries()));
        die();
    } elseif ($_POST["action"] == "get_api_lis") {
        echo (json_encode(getapiList()));
        die();
    } else if ($_POST["action"] == "getUsers") {
        echo (json_encode(getUsers()));
        die();
    } else if ($_POST["action"] == "getAllCountryCodes") {
        echo (json_encode(getAllCountryCodes()));
        die();
    } else if ($_POST["action"] == "getUsersCountry") {
        echo (json_encode(getUsersCountry()));
        die();
    } else if ($_POST["action"] == "showtable") {
        showtable();
        die();
    } else if ($_POST["action"] == "updateuser_country") {
        updateuser_country();
        die();
    } else if ($_POST["action"] == "getusercountries") {
        if (isset($_POST['user_id'])) {
            $user_id = $_POST['user_id'];
            if ($user_id) {
                echo (json_encode(getusercountries($user_id)));
                die();
            }
        }
    } else if ($_POST["action"] == "get_user_country_api") {
        // if(isset($_POST['user_id'])){
        // $user_id = $_POST['user_id']; 
        // if ($user_id){
        header('Content-Type: application/json');
        echo (json_encode(get_user_country_api()));
        die();
        // }

        // }

    } else if ($_POST["action"] == "update") {
        if (isset($_POST["user_id"]) && isset($_POST["countries"]) && isset($_POST["api_list"])) {
            $user_id = $_POST["user_id"];
            $countries = $_POST["countries"];
            $api_list = $_POST["api_list"];
            if ($user_id) {
                $result = updateusers($user_id, $countries, $api_list);
                echo $result;
                die();
            }
        }
    } else if ($_POST["action"] == "clear") {
        if (isset($_POST["user_id"])) {
            $user_id = $_POST["user_id"];
            if ($user_id) {
                $result = clearPermissions($user_id);
                echo $result;
                die();
                // if ($result == "True")
                // {
                //     echo (json_encode('{"msg":"OK"}'));
                //     die();
                // }
            }
        }
    }

    echo (json_encode('{"msg":"error"}'));
}
function getUsersCountry()
{
    $sql1 = "SELECT DISTINCT(source),created_time FROM countries_control ORDER BY created_time DESC";
    $sql2 = "SELECT Id, name,tokens.access_Token FROM users join tokens on tokens.userID = users.Id   WHERE is_deleted=0 ORDER BY name ASC";
    $sql3 = "SELECT * FROM cust_cntry_perm";

    $result1 = $GLOBALS['pdo']->query($sql1)->fetchAll();
    $result2 = $GLOBALS['pdo']->query($sql2)->fetchAll();
    $result3 = $GLOBALS['pdo']->query($sql3)->fetchAll();

    $resultArray = array(
        'country' => $result1,
        'user' => $result2,
        'perm' => $result3
    );

    return json_encode($resultArray);
}

function updateuser_country()
{
    require_once '/var/www/smsmarket/html/backend/redisconfig.php';
    if (isset($_POST["countrySource"]) && isset($_POST["isChecked"]) && isset($_POST["userarray"])) {
        $isChecked = filter_var($_POST['isChecked'], FILTER_VALIDATE_BOOLEAN);
        $countrySource = $_POST['countrySource'];
        $userarray = json_decode($_POST['userarray'], true); 
        if (is_array($userarray)) { 
            if ($isChecked) { 
                $stmt = $GLOBALS['pdo']->prepare("INSERT IGNORE INTO `cust_cntry_perm` (usertoken , country) VALUES (?, ?)");
                foreach ($userarray as $userId) {
                    $stmt->execute([$userId, $countrySource]);
                    if ($redis){
                        $key = "CountryPerm:" . $userId;
                        $redis->sAdd( $key,  $countrySource);
                    }
                }
            } else { 
                $stmt = $GLOBALS['pdo']->prepare("DELETE FROM `cust_cntry_perm` WHERE usertoken  IN (" . implode(',', array_fill(0, count($userarray), '?')) . ") AND country = ?");
                $stmt->execute(array_merge($userarray, [$countrySource]));
                foreach ($userarray as $userId) {
                    if ($redis){
                        $key = "CountryPerm:" . $userId;
                        // $deleted = $redis->hDel($key, "country", $countrySource);    
                        $redis->sRem( $key,  $countrySource);   
                    }
                }


            } 
            echo json_encode(['msg' => 'success']);
        } else { 
            echo json_encode(['msg' => 'error', 'error' => 'Invalid userIds']);
        }
    } else { 
        echo json_encode(['msg' => 'error', 'error' => 'Missing parameters']);
    }
}
function updateuser_country1()
{
    if (isset($_POST["userId"]) && isset($_POST["countrySource"]) && isset($_POST["isChecked"])) {
        $isChecked = filter_var($_POST['isChecked'], FILTER_VALIDATE_BOOLEAN);
        // $userId = $_POST['userId'];
        $userId = json_decode($_POST['userId']);
        $countrySource = $_POST['countrySource'];
        $placeholders = rtrim(str_repeat('?,', count($userId)), ',');

        if ($isChecked){
            // $stmt = $GLOBALS['pdo']->prepare("INSERT IGNORE INTO `cust_cntry_perm` (usertoken , country) VALUES (?,?)" );
            // $stmt->bindValue(1, $userId);
            // $stmt->bindValue(2, $countrySource);
            // $stmt->execute();
            $stmt = $GLOBALS['pdo']->prepare("INSERT IGNORE INTO `cust_cntry_perm` (usertoken , country) VALUES (?, ?)");
            foreach ($userId as $userid) {
                $stmt->execute([$userid, $countrySource]);
            }
         }
        else { 
            // $stmt = $GLOBALS['pdo']->prepare("DELETE FROM `cust_cntry_perm` WHERE usertoken  = ? AND country =?");
            // $stmt->bindValue(1, $userId);
            // $stmt->bindValue(2, $countrySource);
            // $stmt->execute();
            $stmt = $GLOBALS['pdo']->prepare("DELETE FROM `cust_cntry_perm` WHERE usertoken  IN ($placeholders) AND country = ?");
            $stmt->execute(array_merge($userId, [$countrySource]));
        }
    }
    echo (json_encode('{"msg":"success"}'));
}

function getCountries()
{
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM `countryList` where id != 0");
    $stmt->execute([]);
    $logs = $stmt->fetchall();
    return $logs;
}

function getapiList()
{
    $stmt = $GLOBALS['pdo']->prepare("SELECT Id_Api,Name FROM `foreignapi` where is_deleted = 0");
    $stmt->execute([]);
    $logs = $stmt->fetchall();
    return $logs;
}

function showtable()
{

    $sql = "SELECT u.name AS name, GROUP_CONCAT(c.country) AS countries FROM users u JOIN user_countries uc ON u.Id = uc.user_id JOIN countryList c ON uc.country_id = c.id where u.is_deleted = 0";
    if ($_SESSION["is_super"] == 0) {
        $sql = $sql . " and admin_id = " . $_SESSION["id"];
    }
    $sql = $sql . " GROUP BY u.Id";
    $stmt = $GLOBALS['pdo']->query($sql);

    // fetch the results as an array of associative arrays
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // output the results as JSON
    header('Content-Type: application/json');
    echo json_encode($results);
}

function get_user_country_api()
{
    $whereid = "";
    // if (count($user_id)>0){
    //     $userId = array_map('intval', explode(',', $user_id));
    //     $userId = array_map('intval', $userId);
    //     $whereid = "where u.id IN  (".implode(",",$userId ).")";
    // }
    $sql = "SELECT u.name AS user,
                GROUP_CONCAT(DISTINCT (CONCAT(c.country,'-',c.country_char,'-',c.country_code))) AS country,
                GROUP_CONCAT(DISTINCT a.Name) AS api
            FROM users u
            JOIN user_countries utc ON u.id = utc.user_id
            JOIN countryList c ON utc.country_id = c.id
            JOIN user_allowed_api ua ON u.id = ua.user_id
            JOIN foreignapi a ON ua.api_id = a.Id_Api
            where u.is_deleted=0 ";

    if ($_SESSION["is_super"] == 0) {
        $sql = $sql . " and u.admin_id = " . $_SESSION["id"];
    }
    $sql = $sql . " GROUP BY u.id";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();
    // $logs = $stmt->fetchall(PDO::FETCH_ASSOC);
    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }
    $response = array(
        'data' => $data
    );
    return $response;
}

function getusercountries($user_id)
{
    // echo $user_id;
    $values = "";
    $user_id = array_map('intval', $user_id);

    // var_dump($user_id);
    // die();
    $sql = "SELECT  `country_id` from `user_countries`,`users` WHERE  `users`.is_deleted = 0 and `user_id` IN (" . implode(',', array_fill(0, count($user_id), '?')) . ")";

    // $stmt = $GLOBALS['pdo']->prepare("select `country_id` from `user_countries` where `user_id` in ?");
    $stmt = $GLOBALS['pdo']->prepare($sql);
    foreach ($user_id as $i => $id) {
        $stmt->bindValue(($i + 1), $id);
    }
    $stmt->execute();
    $logs = $stmt->fetchall(PDO::FETCH_ASSOC);
    // var_dump($logs);
    if ($logs) {

        $values = array_column($logs, 'country_id');
        $values = array_map('intval', $values);

        // print_r( $values);
        // return $values;


    }

    return $values;
}

//
function getUsers()
{
    $sql = "SELECT id,name,email FROM `users` WHERE is_deleted = 0 ";
    if ($_SESSION["is_super"] == 0) {
        $sql = $sql . " and admin_id = " . $_SESSION["id"];
    }
    $sql = $sql . " order by name asc";

    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}

function getAllCountryCodes()
{
    $sql = "SELECT * FROM th_countrylist order by country_name";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute();
    $logs = $stmt->fetchall();
    return $logs;
}
function update($userId, $countries, $clear = 0)
{
    $userId = array_map('intval', explode(',', $userId));
    $userId = array_map('intval', $userId);

    $sql = "DELETE FROM user_countries WHERE user_id IN (" . implode(',', array_fill(0, count($userId), '?')) . ")"; 
    $deleteStatement = $GLOBALS['pdo']->prepare($sql);
    foreach ($userId as $key => $id) {
        $deleteStatement->bindValue(($key + 1), $id, PDO::PARAM_INT);
    }
    $deleteStatement->execute();
    if ($clear) {
        echo (json_encode('{"status":"OK","msg":"delete all countries"}'));
        die();
    }
    // $countries = array_map('intval', $countries);
    $countryIds = array_map('intval', explode(',', $countries));

    // if (in_array(0, $countryIds)) {
    //     echo (json_encode('{"status":"OK","msg":"delete all countries"}'));
    //     die();
    // } 
    // Prepare the INSERT statement for user_countries table
    $stmt = $GLOBALS['pdo']->prepare("INSERT INTO user_countries (user_id, country_id) VALUES (?, ?)");

    // Loop over the user IDs and country IDs
    foreach ($userId as $user_id) {
        foreach ($countryIds as $country_id) {
            // Bind the user ID and country ID to the statement
            $stmt->bindValue(1, $user_id);
            $stmt->bindValue(2, $country_id);

            // Execute the statement
            $stmt->execute();
        }
    }
    // Insert the new rows into the user_countries table
    // $insertStatement = $GLOBALS['pdo']->prepare('INSERT INTO user_countries (user_id, country_id) VALUES (?, ?)');

    // foreach ($countryIds as $countryId) {
    //     $insertStatement->execute([$userId, $countryId]);
    // }

    echo (json_encode('{"status":"OK","msg":"update succeeded"}'));
    die();
}


function updateusers($userId, $countries, $api_list)
{
    $userId = array_map('intval', explode(',', $userId));
    $countries = array_map('intval', explode(',', $countries));
    $api_list = array_map('intval', explode(',', $api_list));
    $userId = array_map('intval', $userId);
    $countryIds = array_map('intval', $countries);
    $ApiIds = array_map('intval', $api_list);
    if (count($countryIds) > 0) {
        $stmt = $GLOBALS['pdo']->prepare("INSERT INTO user_countries (user_id, country_id) VALUES (?, ?)");
        foreach ($userId as $user_id) {
            foreach ($countryIds as $country_id) {
                $stmt->bindValue(1, $user_id);
                $stmt->bindValue(2, $country_id);
                $stmt->execute();
            }
        }
    }
    if (count($ApiIds) > 0) {
        $stmt2 = $GLOBALS['pdo']->prepare("INSERT INTO user_allowed_api (user_id, api_id) VALUES (?, ?)");
        foreach ($userId as $user_id) {
            foreach ($ApiIds as $ApiId) {
                $stmt2->bindValue(1, $user_id);
                $stmt2->bindValue(2, $ApiId);
                $stmt2->execute();
            }
        }
    }
    echo (json_encode('{"status":"OK","msg":"update succeeded"}'));
    die();
}
function clearPermissions($userId)
{
    $countries_sql = "DELETE FROM user_countries WHERE user_id IN  (" . $userId . ")";
    $deleteCountriesStatement = $GLOBALS['pdo']->prepare($countries_sql);
    $deleteCountriesStatement->execute();


    $api_sql = "DELETE FROM user_allowed_api WHERE user_id IN  (" . $userId . ")";
    $deleteApiStatement = $GLOBALS['pdo']->prepare($api_sql);
    $deleteApiStatement->execute();

    echo (json_encode('{"status":"OK","msg":"D O N E"}'));

    die();
}
#return (json_encode($jarray));
