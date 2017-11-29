<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once('includes/classes/userHandler.php');
    
    $userHandler = new userHandler();
    $loggedIn = $userHandler->checkLoggedIn();    
    if(isset($loggedIn) && $loggedIn == 1) {
            include('includes/views/header.php'); 
            include('includes/views/game.php');
            include('includes/views/footer.php');
    }
    else{
        include('includes/views/login.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <script src="includes/js/jquery.min.js"></script>
    <script src="includes/js/bootstrap.min.js"></script>
    <script src="includes/js/jquery.custom-scrollbar.js"></script>
    <!-- Costum made  -->
</head>
<body>
<?php
        if(isset($loggedIn) && $loggedIn == 1) { ?>
            <script src="includes/js/ajaxHandler.js"></script>
            <script src="includes/js/gameUIHandler.js"></script>
            <script src="includes/js/combatHandler.js"></script><?php
        }
        else { ?>
            <script src="includes/js/loginUIHandler.js"></script><?php
        }
?>
    <!-- Latest compiled and minified JavaScript -->  
        
  
</body>
</html>