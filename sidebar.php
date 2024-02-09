<?php
    require_once "conn.php";

    if(isset($_POST['subscribe'])){
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if(!empty($email)){
            // Checking if the email already existed in the database
            $sql = "SELECT *
            FROM subscriber
            WHERE email = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            if($stmt->execute()){

                $result = $stmt->fetchAll();

                if($result){
                    $subscribeMsg = 0;
                }else{
                    // if not, then insert it in the database
                    $sql = "INSERT INTO subscriber(email)
                            VALUES(?)";

                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(1, $email, PDO::PARAM_STR);
                    $stmt->execute();

                    $subscribeMsg = 1;
                }

                if($_SERVER['QUERY_STRING'] == ""){
                    $query = "";
                }else{
                    $query = $_SERVER['QUERY_STRING'];

                    $pattern = '/(&)?subscribeMsg=[0-9]/';
                    $query = preg_replace($pattern, '', $query);

                    if($query != ""){
                        $query = $query . '&';
                    }

                }

                $url =  basename($_SERVER['PHP_SELF'] ). '?' . $query . 'subscribeMsg=' . $subscribeMsg;

                echo "<script> window.location.replace('" . $url . "');</script>";
            }else{
                $subscribeMsg = 0;
            }

        }
    }
?>

<div class="subscribe">
    <p class="heading font-weight-bold mb-4">Subscribe to our blog</p>
    <?php
        if(isset($_GET['subscribeMsg'])){
            if($_GET['subscribeMsg'] == 1){
                echo "<div class='alert alert-success' role='alert'>You have successfully subscribed</div>";
            }elseif($_GET['subscribeMsg'] == 0){
                echo "<div class='alert alert-danger' role='alert'>This email has already subscribed</div>";
            }
        }
    ?>
    <form action="" method="POST">
        <input type="email" placeholder="E-mail" class="form-control" name="email" required>
        <input type="submit" value="Subscribe" class="btn btn-primary py-2" name="subscribe">
    </form>
</div>
<div class="most-popular">
    <p class="heading font-weight-bold mb-4">Most popular articles</p>
    <?php
        // Getting the top 3 articles ordered by the number of views
        $sql = "SELECT id, title, image
                FROM article 
                ORDER BY views DESC
                LIMIT 3";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        while($row = $stmt->fetch()){
            echo "<a href='article.php?id=$row->id'>
                    <div class='most-popular-post mb-4'>
                        <div class='row mx-0'>
                            <div class='col-4 p-0'>
                            <img src='images/$row->image' alt='$row->title' class='w-100'>
                            </div>
                            <div class='col-8 p-4 d-flex align-items-center'>
                                <p class='m-0 main-color'>$row->title</p>
                            </div>
                        </div>
                    </div>
                </a>";
        }
    ?>
</div>
<div class="categories mt-5">
    <p class="heading font-weight-bold">Categories</p>
    <ul>
        <?php
            // Getting all the categories ordered by the datetime
            $sql = "SELECT id, name
                    FROM category 
                    ORDER BY datetime";

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            while($row = $stmt->fetch()){

                // Getting the number of the articles each category have
                $sql2 = "SELECT COUNT(id) AS count
                     FROM article
                     WHERE category_id = ?";

                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindParam(1, $row->id, PDO::PARAM_INT);
                $stmt2->execute();

                $count = $stmt2->fetchAll()[0]->count;

                echo "<li class='position-relative mb-3'>
                        <a href='search.php?category%5B%5D=$row->id&search=Search'>$row->name</a>
                        <span>$count</span>
                    <li>";
            }
        ?>
    </ul>
</div>