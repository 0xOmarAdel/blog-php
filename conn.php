<?php

    $host = "127.0.0.1";
    $user = "root";
    $pass = "";
    $db = "blog";

    try{

        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    } catch(PDOException $e){

        echo "Not Connected : " . $e->getMessage();

}