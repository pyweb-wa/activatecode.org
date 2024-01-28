<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function send_mail($name,$email,$key=null,$subj=null,$msg=null,$reset=false)
{
    echo $email;
    echo $name;
    try{ 
        require 'PHPMailer/src/Exception.php'; 
        require 'PHPMailer/src/PHPMailer.php'; 
        require 'PHPMailer/src/SMTP.php';
        $mail = new PHPMailer(True); 
        $mail->isSMTP(); 
        $mail->Host = 'smtp.hostinger.com'; 
        $mail->Port = 587; 
        $mail->SMTPSecure = 'tls'; 
        $mail->SMTPAuth = true; 
        $mail->Username = "noreply@mixsimverify.com"; 
        $mail->Password = 'NoreplyP@$$is123'; 
        $mail->setFrom('noreply@mixsimverify.com', 'No-Reply'); 
        $mail->addAddress($email, $name);  
        $mail->Subject = $subj; 
        $message = "<b>Dear Mr. ". $name ."</b>\n";
        $message .= "</br>Welcome To SMS Platform,\n You can verify your account using This Code </br><b> ".$key."</b> \n";
        $message .= "</br>Or With using this Link : "  .generate_url($email,$key);
        if($reset) $message .= "&reset=" . generateRandomString(5,false);
        $message .= "</br><b>"; 
        $mail->Body = $message;
        if($msg != null)$mail->Body = $msg;
        $mail->IsHTML(true);     
        $mail->send();
        $_SESSION['email'] = $email;
        // if($msg==null){
        // header('Location:Unlock.php');
        // die();}
    }
    catch(Exception $ex){
            echo $ex;
            header('Location:register.php?error=3');
            die();
    }
}
function getClientIp() {
    $ip = $_SERVER['REMOTE_ADDR'];

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $ip;
}
session_start();
  include 'actionfxn/actfxn.php';

if(isset($_SERVER['HTTP_REFERER'])){
    $url=basename($_SERVER['HTTP_REFERER']);
    ////////////////////////////////////////////////////////////
    //login
    ////////////////////////////////////////////////////////////
    if(startsWith(strtolower($url),"login")){
        if (isset($_POST['email']) && isset($_POST['password'])) {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $email = $_POST['email'];

                if (strlen($email) > 63 || (strpos($email, '@') == false)) {

                    header('Location:login.php?error=1');
                }
                $passwd = password_hash($_POST['password'], PASSWORD_DEFAULT);
                require_once 'backend/config.php';
                $stmt = $pdo->prepare("SELECT `Id`,`email`,`Passwd`,`Name`,`Balance`,`Is_Activated` FROM `users` WHERE `email` =? ");
                $stmt->execute([$email]);
                $json = $stmt->fetch();
                if ($json["Passwd"]) {
                    if (password_verify($_POST['password'], $json["Passwd"])) {
                        $user_id = $json["Id"];
                        $user_ip = getClientIp();
                        $stmt_log = $pdo->prepare("INSERT INTO `user_login_log` (`uid`, `uip`) Values (?,?) ");
                        $stmt_log->execute([$user_id,$user_ip]);                
                        if ($json["Is_Activated"]==1)
                        {
                            $_SESSION['valid'] = true;
                            $_SESSION['timeout'] = time();
                            $_SESSION['user_email'] = $email;
                            $_SESSION['id'] = $json["Id"];
                            $_SESSION['name'] = $json["Name"];
                            $_SESSION['balance'] = $json["Balance"];
                            $_SESSION['api_key'] = get_token($pdo, $json["Id"]);
                            header('Location:dashboard.php');
                            die();
                        }else{
                            $_SESSION['email'] = $email;
                            header('Location:Unlock.php');
                            die();
                        }
                    }
                    //  else {
                    //     $stmt2 = $pdo->prepare("update  `users` set `Passwd`=? WHERE `email` =? ");
                    //     $stmt2->execute([$passwd, $email]);
                    //     #$json = $stmt2->fetch();
                    //     echo ("pass updated");
                    //     die();
                    // }
                }
            }
        }
        
        header('Location:login.php?error=2');
    }
    ////////////////////////////////////////////////////////////
    //register
    ////////////////////////////////////////////////////////////
    else if(startsWith(strtolower($url),"register")){
        if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password-repeat'])) {
            if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password-repeat']) && $_POST['password']== $_POST['password-repeat']) {
                $email = $_POST['email'];
                $name = $_POST['name'];
                if (strlen($email) > 63 || (strpos($email, '@') == false)) {

                    header('Location:register.php?error=1');
                    die();
                }
                $passwd = password_hash($_POST['password'], PASSWORD_DEFAULT);
                require_once 'backend/config.php';
                $stmt = $pdo->prepare("SELECT `Id` FROM `users` WHERE `email` =? ");
                $stmt->execute([$email]);
                if($stmt->rowCount()>0){
                    header('Location:register.php?error=2');
                    die();
                }else{
                    try{
                            $rnd=generateRandomString();
                            $stmt = $pdo->prepare("INSERT INTO `users` (`name`, `passwd`, `email`, `activation_key`) Values (?,?,?,?) ");
                            $stmt->execute([$name,$passwd,$email,$rnd]);
                            $userId = $pdo->lastInsertId(); 
                            if ($userId) {
                                $access_token =   hash('sha256', generateRandomString() . time());
                                $futureDate = date('Y-m-d', strtotime('+1 year'));
                                $stmt3 = $pdo->prepare("INSERT INTO `tokens`( `userID`, `access_Token`, `expiry_date`, `Valid`, `Tag`) VALUES (?,?,?,?,?)");
                                $stmt3->execute([$userId, $access_token, $futureDate, 1, " "]);
                            }
                            send_mail($name,$email,$rnd,"SMS Platform Verification");
                            header('Location:Unlock.php');

                    }catch(Exception $ex){
                            echo $ex;
                            header('Location:register.php?error=3');
                            die();
                    }



                }

            }

        }

    }
    ////////////////////////////////////////////////////////////
    //unlock
    //////////////////////////////////////////////////////////// 
    else if(startsWith(strtolower($url),"unlock")){
        if(isset($_GET['mail']) && !empty($_GET['mail'])){
                //initial req to unlock the account 
                require_once 'backend/config.php';
                $stmt = $pdo->prepare("SELECT `Id`,`email`,`Passwd`,`Name`,`Balance`,`activation_key` FROM `users` WHERE `email` =? ");
                $stmt->execute([$_GET['mail']]);
                $json = $stmt->fetch();
                send_mail($json['Name'],$json['email'],$json['activation_key'],"SMS Platform Verification");
        }
        if(isset($_POST['key']) && !empty($_POST['key'])){
             
            require_once 'backend/config.php';
            try{
                //TODO need to check if key is equal to our key 
                //if true set user active
                $stmt0 = $pdo->prepare("SELECT * FROM `users` WHERE `email` =? and `activation_key` =? ");
                $stmt0->execute([$_SESSION['email'],$_POST['key']]);
                $res0=$stmt0->fetchall();
                if ($res0[0]) {
                    if ($res0[0]['email']==$_SESSION['email']) { //key is OK
                        //generate new active code in db to prevent repeat attack
                        $newKey=generateRandomString();
                        $stmt = $pdo->prepare("Update `users` set `Is_Activated`=1 and activation_key=? WHERE `email` =? and `activation_key` =? ");
                        $stmt->execute([$_SESSION['email'],$newKey,$_POST['key']]);
                        if(isset($_POST['reg'])){   //used for pass reset  
                            if((int)($_POST['reg'])==1){
                            $temppass = generateRandomString(12,false);
                            $passwd = password_hash($temppass, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("Update `users` set `passwd`=? WHERE `email` =?");
                            $stmt->execute([$passwd,$_SESSION['email']]);
                            send_mail($_SESSION['email'],$_SESSION['email'],$_POST['key'],"SMS Platform Reset Password","Your New Password is : ". $temppass);
                            header('Location:login.php?reset=1');
                            die();
                            }
                        }
                        header('Location:login.php');
                    }
                }
                
                //else print error 
                

            }catch(Exception $ex){
                echo $ex;
                header('Location:Unlock.php?error=1');

            }
            die();

        }
        
    }
    ////////////////////////////////////////////////////////////
    //reset password request -->send email to verify 
    ////////////////////////////////////////////////////////////
    else if(startsWith(strtolower($url),"resetpass")){
        if(isset($_POST['email']) && !empty($_POST['email'])){

                require_once 'backend/config.php';
                // generate new active key  done
                //insert to db    done 
                // send to mail    done 
                $newKey=generateRandomString();
                $stmt = $pdo->prepare("UPDATE   `users` SET `activation_key`=? WHERE `email` =? ");
                $stmt->execute([$newKey,$_POST['email']]);
                $stmt2 = $pdo->prepare("SELECT Name from    `users` WHERE `email` =? ");
                $stmt2->execute([$_POST['email']]);
                $json = $stmt2->fetch();
            if ($json['Name']){
                session_unset();                    //clear all and set only email
                $_SESSION['email']=$_POST['email'];
                send_mail($json['Name'], $_POST['email'], $newKey, "SMS Platform Verification", null, true);
                // echo('Location:Unlock.php?reset=GFt');
                // die();
                header('Location:Unlock.php?reset=GFt');
            }else {
                header('Location:login.php');
            }
        }
        

        
    }
    
    else echo $url;

    
}
