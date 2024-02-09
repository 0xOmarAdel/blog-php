<?php
    require_once "conn.php";
?>

<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Blog - Search</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
	<link rel="stylesheet" href="css/main-styles.css">
    <link rel="stylesheet" href="css/nav.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/search.css">
</head>
<body>
    <?php include "nav.php"; ?>
	<div class="container">
		<div class="row m-0">
            <div class="col-md-4 col-xl-2">
                <div class="search-options">
                    <form action="" method="GET">
                        <p class="heading font-weight-bold">Advanced Options</p>
                        <input type="search" class="form-control mb-3" name="q" placeholder="Search" value="<?php 
                                                                                                                if(isset($_GET['q'])){
                                                                                                                    echo filter_var($_GET['q'], FILTER_SANITIZE_STRING);;
                                                                                                                } 
                                                                                                            ?>">
                        <select class="form-control mb-3" name="orderType">
                            <?php
                                if(isset($_GET['orderType'])){
                                    if($_GET['orderType'] == 'ASC'){
                                        echo "<option value='ASC' selected>Ascending</option>
                                              <option value='DESC'>Descending</option>";
                                    }else if($_GET['orderType'] == 'DESC'){
                                        echo "<option value='ASC'>Ascending</option>
                                              <option value='DESC' selected>Descending</option>";
                                    }else{
                                        echo "<option value='' selected disabled hidden>Order Type</option>
                                              <option value='ASC'>Ascending</option>
                                              <option value='DESC'>Descending</option>";
                                    }
                                }else{
                                    echo "<option value='' selected disabled hidden>Order Type</option>
                                          <option value='ASC'>Ascending</option>
                                          <option value='DESC'>Descending</option>";
                                }
                            ?>
                        </select>
                        <select class="form-control mb-3" name="orderBy">
                            <?php
                                if(isset($_GET['orderBy'])){
                                    if($_GET['orderBy'] == 'views'){
                                        echo "<option value='datetime'>Date</option>
                                              <option value='views' selected>Views</option>";
                                    }else if($_GET['orderBy'] == 'datetime'){
                                        echo "<option value='datetime' selected>Date</option>
                                              <option value='views'>Views</option>";
                                    }else{
                                        echo "<option value='' selected disabled hidden>Order By</option>
                                              <option value='datetime'>Date</option>
                                              <option value='views'>Views</option>";
                                    }
                                }else{
                                    echo "<option value='' selected disabled hidden>Order By</option>
                                          <option value='datetime'>Date</option>
                                          <option value='views'>Views</option>";
                                }
                            ?>
                        </select>
                        <p class="heading font-weight-bold mt-5">Categories in results</p>
                        <?php
                            $sql = "SELECT name, id
                                    FROM category 
                                    ORDER BY datetime";

                            $stmt = $conn->prepare($sql);
                            $stmt->execute();

                            while($row = $stmt->fetch()){
                                if(isset($_GET['category'])){
                                    foreach($_GET['category'] as $key => $value) {
                                        if($value == $row->id){
                                            $output = "<input type='checkbox' id='$row->name' name='category[]' value='$row->id' checked>
                                                       <label for='$row->name'>$row->name</label><br>";
                                            break;
                                        }else{
                                            $output = "<input type='checkbox' id='$row->name' name='category[]' value='$row->id'>
                                                       <label for='$row->name'>$row->name</label><br>";
                                        }
                                    }
                                    echo $output;
                                }else{
                                    echo "<input type='checkbox' id='$row->name' name='category[]' value='$row->id' checked>
                                          <label for='$row->name'>$row->name</label><br>";
                                }
                            }
                        ?>

                        <button type="submit" class="btn btn-primary mt-4" value="Search" name="search">
                            <i class="fas fa-search pr-2"></i>Search
                        </button>
                    </form>
                </div>
            </div>
			<div class="col-md-8 col-xl-6">
                <?php
                if(isset($_GET['search'])){
                    if(isset($_GET['orderType'])){ 
                        $orderType = filter_var($_GET['orderType'], FILTER_SANITIZE_STRING);
                    }else{ 
                        $orderType = "DESC"; 
                    }

                    if(isset($_GET['orderBy'])){ 
                        $orderBy = filter_var($_GET['orderBy'], FILTER_SANITIZE_STRING);
                    }else{ 
                        $orderBy = "views"; 
                    }
                        
                    $sql = "SELECT * FROM article";
            
                    if(isset($_GET['q']) || isset($_GET['category'])){ $sql .= " WHERE "; }

                    if(isset($_GET['q'])){ $sql .= "(title LIKE ? OR description LIKE ?)"; }

                    if(isset($_GET['q']) && isset($_GET['category'])){ $sql .= " AND "; }

                    if(isset($_GET['category'])){
                        $sql .= "(";
                        foreach($_GET['category'] as $c){
                            $sql .= "category_id = ? OR ";
                        }

                        $sql = substr($sql, 0, -3);
                        $sql .= ")";
                    }

                    if($orderBy == "views"){$sql .= " ORDER BY views";} else{$sql .= " ORDER BY datetime";}
                    if($orderType == "ASC"){$sql .= " ASC";} else{$sql .= " DESC";}
            
                    if(isset($_GET['page'])){
                        $page = $_GET['page'];
                    }else{
                        $page = 1;
                    }

                    $number = ($page * 6) - 6;
                        
                    $sql2 = $sql;
                    $sql .= " LIMIT $number,6";


                    $stmt = $conn->prepare($sql);
                    $stmt2 = $conn->prepare($sql2);
        
                    if(isset($_GET['q'])){
                        $q = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
        
                        $stmt->bindValue(1, "%$q%", PDO::PARAM_STR);
                        $stmt->bindValue(2, "%$q%", PDO::PARAM_STR);

                        $stmt2->bindValue(1, "%$q%", PDO::PARAM_STR);
                        $stmt2->bindValue(2, "%$q%", PDO::PARAM_STR);

                        if(isset($_GET['category'])){
                            $n = 3;
                            foreach($_GET['category'] as $c){
                                $stmt->bindValue($n, filter_var($_GET['category'][$n-3], FILTER_SANITIZE_STRING), PDO::PARAM_STR);

                                $stmt2->bindValue($n, filter_var($_GET['category'][$n-3], FILTER_SANITIZE_STRING), PDO::PARAM_STR);
                                $n++;
                            }
                        }
                    }else{
                        if(isset($_GET['category'])){
                            $n = 1;
                            foreach($_GET['category'] as $c){
                                $stmt->bindValue($n, filter_var($_GET['category'][$n-1], FILTER_SANITIZE_STRING), PDO::PARAM_STR);

                                $stmt2->bindValue($n, filter_var($_GET['category'][$n-1], FILTER_SANITIZE_STRING), PDO::PARAM_STR);
                                $n++;
                            }
                        }
                    }
                    
                    $stmt->execute();

                    if($stmt2->execute()){
                        $count = $stmt2->rowCount();

                        if($count > 0){
                            echo "<div class='alert alert-success mt-5 mt-md-0' role='alert'>
                                Found $count results.
                              </div>";
                        }else{
                            echo "<div class='alert alert-danger mt-5 mt-md-0' role='alert'>
                                    No results.
                                  </div>";
                        }
                    }
                }
                
                    while($row = $stmt->fetch()){
                        echo "<div class='card w-100 mb-5'>
                                <div class='position-relative'>
                                    <img class='card-img-top' src='images/$row->image' alt='Card image cap'>
                                    <span class='views position-absolute font-weight-bold'>Views : $row->views</span>
                                </div>
                                <div class='card-body'>
                                    <h5 class='card-title'>$row->title</h5>
                                    <a href='article.php?id=$row->id' class='heading'>Read</a>
                                </div>
                            </div>";
                    }
                ?>

                <nav class="m-auto" aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                            if(isset($count)){
                                $currentLink = basename($_SERVER['REQUEST_URI']);

                                // Removing the subscribe msg if it exists
                                $pattern = '/&subscribeMsg=[0-9]/';
                                $currentLink = preg_replace($pattern, '', $currentLink);
    
                                $pattern = '/&page=[\d]+/';
                                if(!preg_match($pattern, $currentLink)){$currentLink .= "&page=1";}

                                $paginationNumber = ceil($count / 6);

                                if($paginationNumber > 1){

                                    if($page > 1){
                                        $prev = $page - 1;

                                        $link = preg_replace($pattern, "&page=$prev", $currentLink);

                                        echo "<li class='page-item'>
                                                <a class='page-link' href='$link' aria-label='Previous'>
                                                    <span aria-hidden='true'>&laquo;</span>
                                                    <span class='sr-only'>Previous</span>
                                                </a>
                                            </li>";
                                    }else{
                                        echo "<li class='page-item disabled'>
                                                <a class='page-link' href='#' aria-label='Previous'>
                                                    <span aria-hidden='true'>&laquo;</span>
                                                    <span class='sr-only'>Previous</span>
                                                </a>
                                            </li>";
                                    }

                                    for ($i=1; $i <= $paginationNumber; $i++){
                                        $link = preg_replace($pattern, "&page=$i", $currentLink);
                                        if($page == $i){
                                            echo "<li class='page-item active'><a class='page-link' href='$link'>$i</a></li>";
                                        }else{
                                            echo "<li class='page-item'><a class='page-link' href='$link'>$i</a></li>";
                                        }
                                    }

                                    if($page < $paginationNumber){
                                        $prev = $page + 1;

                                        $link = preg_replace($pattern, "&page=$prev", $currentLink);

                                        echo "<li class='page-item'>
                                                <a class='page-link' href='$link' aria-label='Next'>
                                                    <span aria-hidden='true'>&raquo;</span>
                                                    <span class='sr-only'>Next</span>
                                                </a>
                                            </li>";
                                    }else{
                                        echo "<li class='page-item disabled'>
                                                <a class='page-link' href='#' aria-label='Next'>
                                                    <span aria-hidden='true'>&raquo;</span>
                                                    <span class='sr-only'>Next</span>
                                                </a>
                                            </li>";
                                    }
                                }
                            }
                        ?>
                    </ul>
                </nav>
			</div>
			<div class="col-xl-4">
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