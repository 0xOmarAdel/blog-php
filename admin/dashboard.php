<?php

    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php");
        exit();
    }

    require "../conn.php";

    if(isset($_GET['approve'])){
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "UPDATE comment 
                SET status = 1 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }

    if(isset($_GET['delete'])){
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "DELETE FROM comment 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }
  
    if(isset($_POST['delete'])){
        $table = filter_var($_POST['table'], FILTER_SANITIZE_STRING);
        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

        if(!empty($table) && !empty($table)){
            $sql = "DELETE FROM " . $table . " 
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $deleteMsg = "<div class='alert alert-success' role='alert'>The $table with id($id) has been successfully deleted!</div>";
            }else{
                $deleteMsg = "<div class='alert alert-danger' role='alert'>There was an error while deleting This $table, Pleasy try again!</div>";
            }
        }else{
            $deleteMsg = "<div class='alert alert-danger' role='alert'>All fields are required obviously.</div>";
        }
    }

    if(isset($_POST['add-category'])){
        $categoryName = filter_var($_POST['category-name'], FILTER_SANITIZE_STRING);
        $categoryDesc = filter_var($_POST['category-desc'], FILTER_SANITIZE_STRING);

        if(!empty($categoryName) && !empty($categoryDesc)){
            $sql = "INSERT INTO category(name, description) 
                    VALUES(?, ?)";

            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(1, $categoryName, PDO::PARAM_STR);
            $stmt->bindParam(2, $categoryDesc, PDO::PARAM_STR);

            if($stmt->execute()){
                $addCategoryMsg = "<div class='alert alert-success' role='alert'>Category has been successfully added!</div>";
            }else{
                $addCategoryMsg = "<div class='alert alert-danger' role='alert'>There was an error while adding this category, Pleasy try again!</div>";
            }
        }else{
            $addCategoryMsg = "<div class='alert alert-danger' role='alert'>All fields are required obviously.</div>";
        }
    }

    if(isset($_POST['post-article'])){
        $articleCategory = filter_var($_POST['article-category'], FILTER_SANITIZE_STRING);
        $articleTitle = filter_var($_POST['article-title'], FILTER_SANITIZE_STRING);
        $articleDesc =  filter_var($_POST['article-desc'], FILTER_SANITIZE_STRING);

        $images_dir = "../images/";

        $path = $_FILES['article-image']['tmp_name'];
        $name = $_FILES['article-image']['name'];
        $size = $_FILES['article-image']['size'];
        $type = $_FILES['article-image']['type'];
        $error = $_FILES['article-image']['error'];

        $imgExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $unixTimestamp = time();

        $newName = $unixTimestamp . "." . $imgExt;

        if(!empty($articleCategory) && !empty($articleTitle) && !empty($articleDesc) && $size > 0){
            $allowed = array("image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf");

            if(in_array($type, $allowed)) {
                move_uploaded_file($path, $images_dir.$newName);

                $sql = "SELECT id FROM category WHERE name = ?";
                $stmt = $conn->prepare($sql);

                $stmt->bindParam(1, $articleCategory, PDO::PARAM_STR);

                $stmt->execute();

                $articleCategory =  $stmt->fetchColumn();

                $sql = "INSERT INTO article(title, image, description, category_id, admin_id) VALUES(?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                $stmt->bindParam(1, $articleTitle, PDO::PARAM_STR);
                $stmt->bindParam(2, $newName, PDO::PARAM_STR);
                $stmt->bindParam(3, $articleDesc, PDO::PARAM_STR);
                $stmt->bindParam(4, $articleCategory, PDO::PARAM_STR);
                $stmt->bindParam(5, $_SESSION['id'], PDO::PARAM_STR);

                if($stmt->execute()){
                    $postArticleMsg = "<div class='alert alert-success' role='alert'>The article has been successfully posted!</div>";
                }else{
                    $postArticleMsg = "<div class='alert alert-danger' role='alert'>There was an error while posting this article, Pleasy try again!</div>";
                }
            }else{
                $postArticleMsg = "<div class='alert alert-danger' role='alert'>File is not allowed.</div>";
            }
        }else{
            $postArticleMsg = "<div class='alert alert-danger' role='alert'>All fields are required obviously.</div>";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/all.min.css">
        <link rel="stylesheet" href="../css/main-styles.css">
        <link rel="stylesheet" href="style.css">

        <title>Admin - Dashboard</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg">
            <div class="container p-0">
                <a class="navbar-brand" href="">Dashboard</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div>
                    <span class="mr-4"><?php echo $_SESSION['username'] ?></span>
                    <button class="btn btn-primary"><?php echo "<a href='logout.php?logout'>Log out</a>" ?></button>
                </div>
            </div>
        </nav>
        <section class="stats">
            <div class="container p-0">
                <div class="row">
                    <div class="col-6 col-lg-3 stats-box-container">
                        <div class="stats-box">
                            <p>Categories</p>
                            <p>
                                <?php
                                    $sql = "SELECT COUNT(*) FROM category";
                                    $res = $conn->query($sql);
                                    echo $res->fetchColumn();
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 stats-box-container">
                        <div class="stats-box">
                            <p>Articles</p>
                            <p>
                                <?php
                                    $sql = "SELECT COUNT(*) FROM article";
                                    $res = $conn->query($sql);
                                    echo $res->fetchColumn();
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 stats-box-container mt-4 mt-lg-0">
                        <div class="stats-box">
                            <p>Comments</p>
                            <p>
                                <?php
                                    $sql = "SELECT COUNT(*) FROM comment";
                                    $res = $conn->query($sql);
                                    echo $res->fetchColumn();
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 stats-box-container mt-4 mt-lg-0">
                        <div class="stats-box">
                            <p>Visits</p>
                            <p>
                                <?php
                                    $sql = "SELECT views FROM article";
                                    $res = $conn->query($sql);

                                    $totalViews = 0;

                                    while($row = $res->fetch()){
                                        $totalViews += (int)$row->views;
                                    }

                                    echo $totalViews;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="unapproved-comments">
            <div class="container">
                <table>
                    <p class="main-color font-weight-bold">Unapproved Comments</p>

                    <?php
                        $sql = "SELECT * FROM comment WHERE status = 0";
                        $stmt = $conn->prepare($sql);
                
                        $stmt->execute();

                        $result = $stmt->fetchAll();
                        
                        if($result){

                            echo "<tr>
                                    <th style='width: 20%;'>Author</th>
                                    <th style='width: 50%'>Comment</th>
                                    <th style='width: 20%;;>Date</th>
                                    <th style='width: 10%;;></th>
                                </tr>";


                            foreach ($result as $key => $value) {
                                echo "
                                <tr>
                                    <td>$value->author</td>
                                    <td>$value->description</td>
                                    <td>$value->datetime</td>
                                    <td>
                                        <a href='?approve&id=$value->id'><i class='fas fa-check'></i></a>
                                        <a href='?delete&id=$value->id'><i class='fas fa-times'></i></a>
                                    </td>
                                </tr>
                                ";
                            }
                        }else{
                            echo "<div class='alert alert-success' role='alert'>There's no unapproved comments!</div>";
                        }
                
                    ?>
                </table>
            </div>
        </section>
        <section class="delete">
            <div class="container">
                <?php if(isset($deleteMsg)){ echo $deleteMsg; } ?>
                <p class="main-color font-weight-bold">Delete</p>
                <form action="" method="POST">
                    <input type="radio" id="category" name="table" value="category" checked>
                    <label for="category">Category</label>
                    <input type="radio" id="article" name="table" value="article">
                    <label for="article">Article</label>
                    <input type="radio" id="comment" name="table" value="comment">
                    <label for="comment">Comment</label>
                    <input type="number" name="id" class="form-control" placeholder="id">
                    <input type="submit" name="delete" value="Delete" class="btn btn-primary">
                </form>
            </div>
        </section>
        <section class="add-category">
            <div class="container">
                <?php if(isset($addCategoryMsg)){ echo $addCategoryMsg; } ?>
                <p class="main-color font-weight-bold">Add Category</p>
                <form action="" method="POST">
                    <input type="text" class="form-control" name="category-name" placeholder="Category Name">
                    <textarea rows="10" class="form-control" name="category-desc" placeholder="Category Description"></textarea>
                    <input type="submit" class="btn btn-primary" name="add-category" value="Add">
                </form>
            </div>
        </section>
        <section class="post-article">
            <div class="container">
                <?php if(isset($postArticleMsg)){ echo $postArticleMsg; } ?>
                <p class="main-color font-weight-bold">Post Article</p>
                <form action="" method="POST" enctype="multipart/form-data">
                    <select class="form-control" name="article-category">
                        <?php 
                            $sql = "SELECT name FROM category";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();

                            $result = $stmt->fetchAll();

                            foreach ($result as $key => $value) {
                                echo "<option value='$value->name'>$value->name</option>";
                            }
                        ?>
                    </select>
                    <input type="text" class="form-control" name="article-title" placeholder="Article Title">
                    <div class="custom-file mb-4">
                        <input type="file" class="custom-file-input" id="inputGroupFile04" name="article-image">
                        <label class="custom-file-label" for="inputGroupFile04">Upload Image</label>
                    </div>
                    <textarea rows="10" class="form-control" name="article-desc" placeholder="Article Description"></textarea>
                    <input type="submit" class="btn btn-primary"name="post-article" value="Post">
                </form>
            </div>
        </section>
    </body>
</html>