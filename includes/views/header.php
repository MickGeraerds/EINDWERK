<?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['submit']) && $_POST['submit'] == "profile"){
                //
            }
            if(isset($_POST['submit']) && $_POST['submit'] == "logout"){
                $userHandler->logout();
            }
        }
    ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="styles/bootstrap-theme.min.css">
    <link rel="stylesheet" href="styles/header.css">
</head>
    <section id="header">
        <div id="headerContainer" class="clearfix">
            <form id="currencyContainer">
                
            </form>
            <form id="playerContainer" method="post">
                <span id="greetings">Hello</span>
                <span id="playerName"><?php if(isset($_COOKIE['display']['username'])) {echo $_COOKIE['display']['username'];} ?></span>
                <span></span>
               <input class="btn" type="submit" id="logout" name="submit" value="profile">
               <input class="btn" type="submit" id="logout" name="submit" value="logout">
            </form>
        </div>
    </section>
</html>