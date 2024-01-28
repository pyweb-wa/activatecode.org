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
            <form method="post" action="action.php">
                <h2 class="text-center"><strong>Create</strong> an account.</h2>
                <div class="form-group"><input class="form-control" type="text" name="name" placeholder="Name" required ></div>
                <div class="form-group"><input class="form-control" type="email" name="email" placeholder="Email"required ></div>
                <div class="form-group"><input class="form-control" type="password" name="password" placeholder="Password"   minlength="8"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required ></div>
                <div class="form-group"><input class="form-control" type="password" name="password-repeat" placeholder="Password (repeat)"   minlength="8"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required ></div>
                <div class="form-group">
                    <div class="form-check"><label class="form-check-label"><input class="form-check-input" type="checkbox">I agree to the license terms.</label></div>
                </div>
                <div class="form-group"><button class="btn btn-primary btn-block" type="submit">Sign Up</button></div><a class="already" href="login.php">You already have an account? Login here.</a>
            </form>
        </div>
    </div>
    <!-- Modal -->
<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Registration Error</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="errorContent">

        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" style="display:none" id="reset">Reset Password</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <?php
if(isset($_GET['error']) && !empty($_GET['error'])){
    $errorcode=$_GET['error'];
    if($errorcode==1)echo "<script type='text/javascript'>document.getElementById('errorContent').innerHTML ='Mail Input Error Please Check it And Re-register.';</script>";
    if($errorcode==2)echo "<script type='text/javascript'>document.getElementById('errorContent').innerHTML ='User Already Exist'; $('#reset').show();</script>";
    if($errorcode==3)echo "<script type='text/javascript'>document.getElementById('errorContent').innerHTML ='Registration Error, Please Mail The Support Team.';</script>";
    echo "<script type='text/javascript'>
    $(document).ready(function(){
    $('#ModalCenter').modal('show');
    });
    </script>";
}
?>
</body>

</html>