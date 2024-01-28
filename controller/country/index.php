<?php

require_once "validate_token.php";
if (checkTokenInDatabase()) {
    header('Location: cards.php?action=display');
    exit(); 
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: #222;
        }
        .gradient-custom {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body>
    <section class="vh-100 gradient-custom d-flex align-items-center">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <div class="pb-1">
                                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                                <p class="text-white-50 mb-5">Please enter your login and password!</p>
                                <div class="form-outline form-white mb-4">
                                    <input type="text" id="typeTextX" class="form-control form-control-lg" />
                                    <label class="form-label" for="typeTextX">Username</label>
                                </div>
                                <div class="form-outline form-white mb-4">
                                    <input type="password" id="typePasswordX" class="form-control form-control-lg" />
                                    <label class="form-label" for="typePasswordX">Password</label>
                                </div>
                                <button class="btn btn-outline-light btn-lg px-5" onClick="performLogin()" id="login_btn">Login</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script> 
        // function checkAutoLogin() { 
        //     if (document.cookie.indexOf('token=') !== -1) { 
        //         var token = document.cookie.split('token=')[1].split(';')[0]; 
        //         if (validateClientToken(token)) { 
        //             alert('Auto-login successful!'); 
        //         }
        //     }
        // } 
        // window.onload = checkAutoLogin;
        function performLogin() { 
            var user = document.getElementById('typeTextX').value;
            var password = document.getElementById('typePasswordX').value; 
            if (user.trim() === '' || password.trim() === '') {
            } else {
                var credentials = {
                    user: user,
                    password: password
                }; 
                $.ajax({
                    type: 'POST',
                    url: 'validate_usr.php',
                    data: JSON.stringify(credentials),
                    contentType: 'application/json',
                    success: function (response) { 
                        var response = JSON.parse(response);
                        console.log(response);
                        if (response.token) { 
                            document.cookie = 'token=' + response.token + '; path=/'; 
                            window.location.href = 'cards.php?action=display';
                        } else { 
                            if (response.error) {
                                alert('Login failed: ' + response.error);
                            } else {
                                alert('Login failed. Please check your credentials.');
                            }
                        }
                    },
                    error: function (xhr, status, error) { 
                        alert('Login failed. Please try again later.');
                        console.error(xhr.responseText);
                    }
                });
            }
        }
    </script>

</body>

</html>
