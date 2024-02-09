<?php
    if(isset($_GET['id'])){
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        require_once "conn.php";

        // Making sure that the id is a valid article id
        $sql = "SELECT * 
                FROM article 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll();

        if($result){
            // Article Information
            $id = $result[0]->id;
            $title = $result[0]->title;
            $image = $result[0]->image;
            $description = $result[0]->description;
            $datetime = $result[0]->datetime;
            $views = $result[0]->views;
            $categoryId = $result[0]->category_id;
            $adminId = $result[0]->admin_id;

            // Category Name
            $sql = "SELECT name 
                    FROM category 
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $categoryId, PDO::PARAM_INT);
            $stmt->execute();

            $category = $stmt->fetchAll()[0]->name;

            // Admin Information
            $sql = "SELECT username, image, info 
                    FROM admin 
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $adminId, PDO::PARAM_INT);
            $stmt->execute();

            $result =  $stmt->fetch();

            $adminUsername = $result->username;
            $adminImage = $result->image;
            $adminInfo = $result->info;

            // Adding a new visit
            $visitor_ip = $_SERVER['REMOTE_ADDR'];
            $date = date('Y-m-d');

            // Query to know whether the visitor's ip has visited the same article before
            $sql = "SELECT * 
                    FROM visitor 
                    WHERE ip = ? AND article_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $visitor_ip, PDO::PARAM_STR);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch();

            // If the visitor's ip already visited this article then this if statement will be executed
            // if not, then the else statment will be executed which will insert a new tuple in the table and increase the article's views by 1
            if($row){
                // if the lase visit date from the visitor's ip on this article is not the same as today's date
                // then, update the visite date to today's date and increase the views by 1
                if($row->visit_date != $date){
                    $sql = "UPDATE visitor
                            SET visit_date = ?
                            WHERE ip = ? AND article_id = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(1, $date, PDO::PARAM_STR);
                    $stmt->bindParam(2, $visitor_ip, PDO::PARAM_STR);
                    $stmt->bindParam(3, $id, PDO::PARAM_INT);
                    $stmt->execute();

                    $sql = "UPDATE article
                            SET views = ?
                            WHERE id = ?";

                    $views += 1;

                    $stmt = $conn->prepare($sql);          
                    $stmt->bindParam(1, $views, PDO::PARAM_STR);
                    $stmt->bindParam(2, $id, PDO::PARAM_INT);
                    $stmt->execute();
                    
                }
            }else{
                $sql = "INSERT INTO visitor(ip, visit_date, article_id)
                        VALUES(?, ?, ?)";

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(1, $visitor_ip, PDO::PARAM_STR);
                $stmt->bindParam(2, $date, PDO::PARAM_STR);
                $stmt->bindParam(3, $id, PDO::PARAM_INT);

                $stmt->execute();

                $sql = "UPDATE article
                            SET views = ?
                            WHERE id = ?";

                    $views += 1;

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(1, $views, PDO::PARAM_STR);
                    $stmt->bindParam(2, $id, PDO::PARAM_INT);
                    $stmt->execute();
            }
        }else{
            header("Location: index.php");
            exit();
        }

    }else{
        header("Location: index.php");
        exit();
    }

    if(isset($_POST['add-comment'])){
        $commentAuthor = filter_var($_POST['comment-author'], FILTER_SANITIZE_STRING);
        $commentDesc = filter_var($_POST['comment-desc'], FILTER_SANITIZE_STRING);

        if(!empty($commentAuthor) && !empty($commentDesc)){
            $sql = "INSERT INTO comment(author, description, article_id) VALUES(?, ?, ?)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(1, $commentAuthor, PDO::PARAM_STR);
            $stmt->bindParam(2, $commentDesc, PDO::PARAM_STR);
            $stmt->bindParam(3, $id, PDO::PARAM_INT);

            if($stmt->execute()){
                $status = 1;
            }else{
                $status = 0;
            }

            if($_SERVER['QUERY_STRING'] == ""){
                $query = "";
            }else{
                $query = $_SERVER['QUERY_STRING'] . '&';
            }
            
            $url =  basename($_SERVER['PHP_SELF'] ). '?' . $query . 'status=' . $status;
                            
            echo "<script> window.location.replace('" . $url . "'); </script>";
        }
    }
?>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
	<link rel="stylesheet" href="css/main-styles.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/article.css">
</head>
<body>
    <?php include "nav.php"; ?>
	<div class="container">
		<div class="row m-0">
			<div class="col-xl-7">
				<div class="article pb-5">
					<div class="article-banner">
                        <img src="images/<?php echo $image; ?>" alt="<?php echo $title; ?>" class="article-img w-100">
						<span class="views position-absolute font-weight-bold">Views : <?php echo $views; ?></span>
					</div>
					<div class="mt-4">
					    <span class="article-title heading font-weight-bold"><?php echo $title; ?></span>
					    <br>
					    <span class="article-category"><a href="<?php echo"search.php?category%5B%5D=$categoryId&search=Search"?>" class='heading'><?php echo $category; ?></a></span>                        
					</div>
					<p class="article-desc"><?php echo $description; ?></p>
                    <span class="text-muted"><?php echo date("m-d-Y", strtotime($datetime)); ?></span>
                </div>
				<div class="author-info border-top py-5">
					<div class="row">
						<div class="col-2">
                        <img src="images/<?php echo $adminImage; ?>" alt="<?php echo $adminUsername; ?>" class="w-100 rounded-circle">
						</div>
						<div class="col-10">
							<p class="author-name font-weight-bold heading"><?php echo $adminUsername; ?></p>
							<p class="author-desc"><?php echo $adminInfo; ?></p>
						</div>
					</div>
				</div>
				<div class="read-also border-top pt-5">
					<p class="heading font-weight-bold mb-4">Read Also</p>
					<div class="row">
                        <?php 
                            $sql = "SELECT id, title,image
                                    FROM article 
                                    WHERE category_id = ? AND id != ?
                                    LIMIT 6";

                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(1, $categoryId, PDO::PARAM_INT);
                            $stmt->bindParam(2, $id, PDO::PARAM_INT);
                            $stmt->execute();

                            $result = $stmt->fetchAll();

                            if($result){
                                foreach ($result as $key => $value) {    
                                    echo "<div class='col-6 col-md-4 mb-5'>
                                            <div class='read-also-article'>
                                                <a href='article.php?id=$value->id'>
                                                    <img src='images/$value->image' alt='$value->title' class='w-100'>
                                                    <p class='mt-3 main-color'>$value->title</p>
                                                </a>
                                            </div>
                                        </div>";
                                }
                            }else{
                                echo "<div class='alert alert-danger w-100' role='alert'>Couldnt find any related articles for this article</div>";
                            }

                        ?>
					</div>
				</div>
                <div class="commenting-area border-top py-5">
                    <p class="heading font-weight-bold">Leave a comment</p>
                    <?php
                        if(isset($_GET['status'])){
                            if($_GET['status'] == 1){
                                echo "<div class='alert alert-success' role='alert'>Your comment has been successfully added, wait till the admin approve it.</div>";
                            }elseif($_GET['status'] == 0){
                                echo "<div class='alert alert-danger' role='alert'>There was an error while adding your comment.</div>";
                            }
                        }
                    ?>
                    <form action="" method="POST" onSubmit="return validate_form(this)">
                        <input type="text" class="form-control mb-2" name="comment-author" placeholder="Your Name" required>
                        <textarea rows="5" class="form-control mb-3" name="comment-desc" placeholder="Your comment" required></textarea>
                        <button type="submit" class="btn btn-primary" name="add-comment">
                            <i class="fas fa-pen pr-2"></i>Submit
                        </button>
                    </form>
                </div>
                <div class="comments-section border-top py-5">
                    <p class="heading font-weight-bold">Comments</p>
                    <div class="comments">
                        <?php 
                            $sql = "SELECT author, description, datetime
                                    FROM comment 
                                    WHERE article_id = ? AND status = 1
                                    ORDER BY datetime DESC";

                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(1, $id, PDO::PARAM_INT);
                            $stmt->execute();

                            $result = $stmt->fetchAll();

                            if($result){
                                foreach ($result as $key => $value) {
                                    $date = date("m-d-Y", strtotime($value->datetime));
    
                                    echo "<div class='row'>
                                            <div class='col-md-10'>
                                                <p>$value->description<span class='main-color'>$value->author</span></p>
                                            </div>
                                            <div class='col-md-2 d-flex justify-content-md-center align-items-md-center mb-4 mb-md-0'>
                                                <span class='datetime'>$date</span>
                                            </div>
                                        </div>";
                                }
                            }else{
                                echo "<div class='alert alert-danger' role='alert'>There's no comments on this article.<br>Be the first one to write a comment!</div>";
                            }
                        ?>
                    </div>
                </div>
			</div>
			<div class="col-xl-4 offset-xl-1">
                <div class="side-bar">
                    <?php include "sidebar.php"; ?>
                </div>
				<div class="recommendations mt-5">
					<p class="heading font-weight-bold">Recommendations</p>
					<ul>
                        <?php 
                            $sql = "SELECT id, title, views
                                    FROM article 
                                    WHERE category_id = ? AND id != ?
                                    ORDER BY views 
                                    LIMIT 20";

                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(1, $categoryId, PDO::PARAM_INT);
                            $stmt->bindParam(2, $id, PDO::PARAM_INT);
                            $stmt->execute();

                            $result = $stmt->fetchAll();

                            if($result){
                                foreach ($result as $key => $value) {    
                                    echo "<li class='position-relative mb-3'>
                                            <a href='article.php?id=$value->id' class='d-inline-block'>$value->title</a>
                                            <span>$value->views</span>
                                          <li>";
                                }
                            }else{
                                echo "<div class='alert alert-danger' role='alert'>Couldnt find any recommendations for this article</div>";
                            }

                        ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
    <?php include "footer.php"; ?>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>