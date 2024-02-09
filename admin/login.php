<?php
    session_start();

    if(isset($_SESSION['username'])){
        header("Location: dashboard.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <title>Page Title</title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">
        <link rel='stylesheet' type='text/css' href='style.css'>
    </head>
    <body>
        <div>
            <div class="form">
                <form action="process.php" method="POST" onSubmit="return validate_form(this)">
                    <h1>Log in</h1>
                    <input type="text" name="username" placeholder="Username">
                    <input type="password" name="password" placeholder="Password">
                    <input type="submit" name="login" value="Log in">
                </form>
            </div>
        </div>
        <script src="script.js"></script>
    </body>
</html>