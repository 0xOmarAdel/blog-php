<?php
    require_once "conn.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <title>Blog - Home</title>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/all.min.css">
        <link rel="stylesheet" href="css/main-styles.css">
        <link rel="stylesheet" href="css/nav.css">
        <link rel="stylesheet" href="css/sidebar.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <?php include "nav.php"; ?>
        <div class="container">
            <div class="row m-0">
                <div class="col-xl-7">
                    <?php
                        $sql = "SELECT *
                                FROM article
                                ORDER BY datetime DESC
                                LIMIT 1";
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();

                        $row = $stmt->fetch();

                        $id = $row->id;
                        $categoryId = $row->category_id;

                        // Category Name
                        $sql = "SELECT name FROM category WHERE id = ?";
                        $stmt = $conn->prepare($sql);

                        $stmt->bindParam(1, $categoryId, PDO::PARAM_INT);

                        $stmt->execute();

                        $category = $stmt->fetchAll()[0]->name;

                        echo"<div class='first-artical mb-5'>
                                <div class='row mx-0'>
                                    <div class='col-12 p-0'>
                                        <div class='first-artical-img position-relative'>
                                            <img src='images/$row->image' alt='$row->title'>
                                            <span class='views position-absolute font-weight-bold'>Views : $row->views</span>
                                        </div>
                                    </div>
                                    <div class='col-12 p-4'>
                                        <p class='first-artical-title mb-0'>$row->title</p>
                                        <p><a href='search.php?category%5B%5D=$categoryId&search=Search' class='heading'>- $category</a></p>
                                        <a href='article.php?id=$row->id' class='heading'>Read</a>
                                    </div>
                                </div>
                            </div>";
                    ?>
                    <div class="row">
                    <?php
                        $sql = "SELECT *
                                FROM article
                                WHERE id != ?
                                ORDER BY datetime DESC
                                LIMIT 6";
                        
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(1, $id, PDO::PARAM_INT);
                        $stmt->execute();
                        

                        while($row = $stmt->fetch()){
                            $categoryId = $row->category_id;

                            // Category Name
                            $sql = "SELECT name FROM category WHERE id = ?";
                            $stmt2 = $conn->prepare($sql);

                            $stmt2->bindParam(1, $categoryId, PDO::PARAM_INT);

                            $stmt2->execute();

                            $category = $stmt2->fetchAll()[0]->name;


                            echo"<div class='col-md-6 px-3 pb-5'>
                                    <div class='card'>
                                        <div class='position-relative'>
                                            <img src='images/$row->image' class='card-img-top' alt='$row->title'>
                                            <span class='views position-absolute font-weight-bold'>Views : $row->views</span>
                                        </div>
                                        <div class='card-body'>
                                            <h5 class='card-title'>$row->title</h5>
                                            <p><a href='search.php?category%5B%5D=$categoryId&search=Search' class='heading'>- $category</a></p>
                                            <a href='article.php?id=$row->id' class='heading'>Read</a>
                                        </div>
                                    </div>
                            </div>";
                        }
                    ?>
                    </div>
                </div>
                <div class="col-xl-4 offset-xl-1">
                    <div class="side-bar">
                        <?php include "sidebar.php"; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include "footer.php"; ?>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>