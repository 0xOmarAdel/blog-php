<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand font-weight-bold main-color" href="index.php">Blog</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav m-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="search.php?search=Search">Top Articles</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="search.php?search=Search">All Articles</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contactUs.php">Contact Us</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0" action="search.php" method="GET">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" name="q">
            <button type="submit" class="btn btn-primary my-2 my-sm-0" name="search" value="Search">
                <i class="fas fa-search"></i>
            </button>
        </form>
        </div>
    </div>
</nav>