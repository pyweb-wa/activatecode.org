<?php
ob_start();
session_start();
$email=null;
$keys=null;
$reg=null;
function base64UrlSafeDecode(string $input)
{
   return str_replace(['_', '-'], ['/', '+'], $input);
}
if (!isset($_SESSION['email'])) {   
    header('Location:login.php');
    die();
}
if(isset($_GET['reset']))$reg=1;

$email=$_SESSION['email'];
if(isset($_GET['ENC']) && !empty($_GET['ENC'])){
    try{
        $method = 'AES-256-CBC';
        $key = getenv('NAMESPACED_CRYPTO_KEY');
        //echo  base64UrlSafeDecode($_GET['ENC']);
        list($data, $iv) = explode('|', base64UrlSafeDecode($_GET['ENC']));
        $iv = base64_decode($iv);
        //echo $data;
        $pltxt = openssl_decrypt($data, $method, $key, 0, $iv);
        //echo $pltxt;

        list($email,$keys) = explode('[***]',$pltxt);
      

    }catch(Exception $e){
        echo $e;
        die();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>UserControl</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Dark.css">
    <link rel="stylesheet" href="assets/css/Registration-Form-with-Photo.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div class="register-photo" style="height: 1000px;">
        <div class="form-container">
            <div class="image-holder"></div>
            <form id="activate_frm" method="post" action="action.php">
                <h2 class="text-center"><strong>Verify</strong> your account.</h2>
                <div class="form-group"><input class="form-control" type="text" name="key" id="key" placeholder="Verification Key" value="<?php echo htmlspecialchars($keys); ?>"></div>
                <div class="form-group"><button class="btn btn-primary btn-block" type="submit">Unlock</button></div><a class="already" href=action.php?mail=<?php echo $email ?>>Resend Mail !</a>
                <input id="reg" name="reg" type="hidden" value="<?php echo htmlspecialchars($reg) ?>">
            </form>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    
<script type="text/javascript">
    function submitForm() {
        var keyval = document.getElementById('key').value;
        //alert(keyval);
        if(typeof keyval === 'undefined') {
    } else if(keyval === null){
    }else if(keyval === ''){
    }else{document.getElementById('activate_frm').submit();}
        //document.getElementById('my_form').submit();
    }
    window.onload = submitForm;
</script>
</body>

</html>