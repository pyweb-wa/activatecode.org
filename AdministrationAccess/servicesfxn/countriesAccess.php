
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
    } 
    elseif ($_POST["action"] == "get_api_lis") {
        echo (json_encode(getapiList()));
        die();
    } 
    else if ($_POST["action"] == "getUsers") {
        echo (json_encode(getUsers()));
        die();

        
    }
    else if ($_POST["action"] == "showtable") {
        showtable();
        die();

        
    }
    else if ($_POST["action"] == "getusercountries") {
        if(isset($_POST['user_id'])){
            $user_id = $_POST['user_id']; 
            if ($user_id){
                echo (json_encode(getusercountries($user_id)));
                die();
            }
           
        }
  
    }
    else if ($_POST["action"] == "update") {
        if (isset($_POST["user_id"]) && isset($_POST["countries"])) {
            $user_id = $_POST["user_id"];
            $countries = $_POST["countries"];
                if($user_id){

                $result = update($user_id, $countries);
                if ($result == "True")
                {
                    echo (json_encode('{"msg":"OK"}'));
                    die();

                }
            }
           
        }
    }
    else if ($_POST["action"] == "clear") {
        if (isset($_POST["user_id"])) {
            $user_id = $_POST["user_id"];
                if($user_id){
                $result = update($user_id,"",1);
                if ($result == "True")
                {
                    echo (json_encode('{"msg":"OK"}'));
                    die();

                }
            }
           
        }
    }

    echo (json_encode('{"msg":"error"}'));

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
function showtable(){

    $sql = "SELECT u.name AS name, GROUP_CONCAT(c.country) AS countries FROM users u JOIN user_countries uc ON u.Id = uc.user_id JOIN countryList c ON uc.country_id = c.id where u.is_deleted = 0 GROUP BY u.Id";
    $stmt = $GLOBALS['pdo']->query($sql);

    // fetch the results as an array of associative arrays
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // output the results as JSON
    header('Content-Type: application/json');
    echo json_encode($results);


}
function getusercountries($user_id)
{
   // echo $user_id;
    $values = "";
    $user_id = array_map('intval', $user_id);

    // var_dump($user_id);
    // die();
    $sql = "SELECT  `country_id` from `user_countries`,`users` WHERE    `users`.is_deleted = 0 and `user_id` IN (".implode(',', array_fill(0, count($user_id), '?')).")";

    // $stmt = $GLOBALS['pdo']->prepare("select `country_id` from `user_countries` where `user_id` in ?");
    $stmt = $GLOBALS['pdo']->prepare($sql);
    foreach ($user_id as $i => $id) {
        $stmt->bindValue(($i+1), $id);
    }
    $stmt->execute();
    $logs = $stmt->fetchall(PDO::FETCH_ASSOC);
   // var_dump($logs);
    if($logs){
        
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
    $stmt = $GLOBALS['pdo']->prepare("SELECT id,name,email FROM `users` WHERE is_deleted = 0 order by name asc");
    $stmt->execute([]);
    $logs = $stmt->fetchall();
    return $logs;
}

function update($userId, $countries, $clear =0)
{
    $userId = array_map('intval', explode(',', $userId));
    $userId = array_map('intval', $userId);
   
    $sql = "DELETE FROM user_countries WHERE user_id IN (".implode(',', array_fill(0, count($userId), '?')).")";
    //$deleteStatement = $GLOBALS['pdo']->prepare('DELETE FROM user_countries WHERE user_id = ?');
    $deleteStatement = $GLOBALS['pdo']->prepare($sql);
    foreach ($userId as $key => $id) {
        $deleteStatement->bindValue(($key+1), $id, PDO::PARAM_INT);
    }
    $deleteStatement->execute(); 
    if ($clear){
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
        $stmt =$GLOBALS['pdo']->prepare("INSERT INTO user_countries (user_id, country_id) VALUES (?, ?)");

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


#return (json_encode($jarray));
