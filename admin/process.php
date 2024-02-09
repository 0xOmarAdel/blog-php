<?php
    if(isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        require_once "../conn.php";

        $sql = "SELECT id, username, password
                FROM admin
                WHERE username = ? AND password = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(1, $username, PDO::PARAM_STR);
        $stmt->bindParam(2, $password, PDO::PARAM_STR); 

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($result){
            session_start();
            $_SESSION['id'] = $result[0]->id;
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        }else{
            header("Location: login.php");
            exit();
        }
    };