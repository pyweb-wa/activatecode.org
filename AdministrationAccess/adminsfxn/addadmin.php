 <?php

    session_start();
    if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel") {
        header('Location:https://' . $_SERVER["SERVER_NAME"] . '/404.php', true, 301);
        die();
    }

    require_once './../../backend/mylogger.php';
    $logger = new MyLogger();
    $logger->Add("test", basename(__FILE__));
    if (
        isset($_POST['email'])
        && isset($_POST['name'])
        && isset($_POST['is_super'])
        && isset($_POST['Passwd'])
        && isset($_POST['Is_Activated'])
        && strlen($_POST['Passwd'] >= 8)
    ) {
        if (
            !empty($_POST['email'])
            && !empty($_POST['is_super'])
            && !empty($_POST['Is_Activated'])

        ) {
            try {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $passw =  password_hash($_POST['Passwd'], PASSWORD_DEFAULT);
                ($_POST['is_super'] == "true" ? $is_super = 1 : $is_super = 0);
                ($_POST['Is_Activated'] == "true" ? $Is_Activated = 1 : $Is_Activated = 0);

                require_once './../../backend/config.php';
                $stmt = $pdo->prepare(" INSERT INTO `cms_users` (  `email`,`name`,`Passwd`, `is_super`, `Is_Activated`) VALUES (  ?,?, ?, ?, ? ) ");
                $stmt->execute([$email, $name, $passw, $is_super,  $Is_Activated]);
                $userId = $pdo->lastInsertId();

                $stmt4 = $pdo->prepare("SELECT `Id_User`, `name`,`email`,`is_super`,`Passwd`,   `Is_Activated`
                from cms_users where Id_User = ?");
                $stmt4->execute([$userId]);
                $newuser = $stmt4->fetch();
                if ($newuser["is_super"] == "1") {
                    $newuser["is_super"] = true;
                } else {
                    $newuser["is_super"] = false;
                }
                if ($newuser["Is_Activated"] == "1") {
                    $newuser["Is_Activated"] = true;
                } else {
                    $newuser["Is_Activated"] = false;
                }
                echo (json_encode($newuser));
                die();
            } catch (Exception $e) {
                $logger->Add($e->getMessage(), basename(__FILE__));
            }
        }
    }

