<?php 
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
    header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
    die();
} 
if (  isset($_POST['apiList'])  )
  { 
    if (is_array($_POST['apiList'])  ) {
        $api_list=$_POST['apiList']; 

        require_once './../../backend/config.php';
        $count=1;
        foreach($api_list as $api_id)
        {
            if((int)$api_id!=0 )
            {
                $stmt = $pdo->prepare("UPDATE   `foreignapi` SET priority =? where `Id_Api`=?");
                $stmt->execute([$count, $api_id]); 
            }
            $count=$count+1; 
        } 
        echo ("list updated");
        die();
    }
} 
echo "something went wrong";
