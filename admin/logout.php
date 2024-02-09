<?php
    session_start();

    if(isset($_SESSION['username'])){
        if(isset($_GET['logout'])){
            session_destroy();
            header("Location: login.php");
            exit();
        }else{
            header("Location: login.php");
            exit();
        }
    }else{
        header("Location: login.php");
            exit();
    }