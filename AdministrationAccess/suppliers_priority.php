<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['level']) || $_SESSION['level'] != "rootlevel" || $_SESSION["is_super"] != 1) {
    header('Location:login.php');
    die();
}

function getApiList()
{
    require_once './../backend/config.php'; 
    $stmt = $pdo->prepare("SELECT `Id_Api` as 'id', `Name`, `Description`  FROM `foreignapi` WHERE is_deleted=0 order by priority");
    $stmt->execute();
    $apis = $stmt->fetchall();
    return $apis;   
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Brand</title>
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">


    <link rel="stylesheet" href="assets/bootstrap/css/buttons.bootstrap4.min.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <?php
include 'includes/sidebar.php';
?>
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <?php
include 'includes/navbar.php';
?>
                <div class="container-fluid"> 
                    <div class="card shadow mt-3">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Suppliers</p>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                 <!-- here set sortable js -->
                                 <div id="simple-list"  > 
                                    <ul id="apiList" class="list-group col">
                                        <?php 
                                        foreach (getApiList() as $api)
                                        {  

                                            $txt=sprintf(' <li  class="list-group-item bg-danger text-white" id="%s" >%s -->  %s</li>',$api["id"],$api["Name"],$api["Description"]);
                                           // $txt=sprintf('<div class="list-group-item" id="%s" >%s -->  %s</div>',$api["id"],$api["Name"],$api["Description"]);
                                            echo $txt;
                                        } 
                                        ?>
                                    </ul>
                                 </div>
                                <!--  -->
                            </div>



                        </div>
                    </div>
                </div>

            </div>
            <?php
include 'includes/footer.php';
?>

        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a></div>


        <script src="assets/js/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/dataTables.buttons.min.js"></script>
    <script src="assets/js/buttons.bootstrap4.min.js"></script>

    <script src="assets/js/buttons.html5.min.js"></script>
    <script src="assets/js/theme.js"></script>
 

    <script src=" https://sortablejs.github.io/Sortable/Sortable.js"></script>
    <script>
         $('#nav_item_supplier_priority').addClass("active");
        new Sortable(apiList, {
            animation: 150,
            ghostClass: 'bg-warning'
        });

        var $sortableList = $("#apiList");

        var sortEventHandler = function(event, ui){
                   var listValues=[];
                    $('#apiList li').each(function (element) {
                        console.log(this.id);  //id 
                        listValues.push(this.id);
                    }); 
                 console.log(listValues);
                 $.post('apisfxn/update_priority.php', {'apiList':listValues });
               
        };

        $sortableList.sortable({
            stop: sortEventHandler
        });

        // You can also set the event handler on an already existing Sortable widget this way:

        $sortableList.on("sortchange", sortEventHandler);
    </script>

<script>
    $(document).ready(function () { 
        $("#nav_main_title").text("Priority");
    });
    $('#nav_item_supplier_priority').addClass("active");
</script>

</body>

</html>