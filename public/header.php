
<section style='
        width: 100% !important;
        height: 5vh !important;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 6rem;'>
    </section>
<header class="header" style="top:0;">

<nav class='navbar navbar-expand-lg navbar-light bg-light '>
    <div class='container-fluid ms-5'>
        <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
        <span class='navbar-toggler-icon'></span>
        </button>
        <div class='collapse navbar-collapse' id='navbarSupportedContent'>
        <ul class='navbar-nav me-auto mb-2 mb-lg-0'>

            <li  class='nav-item' style='margin: 0 0 0 0 !important;'>
            <a class='nav-link active' aria-current='page' href='index.php'>Home</a>
            </li>
           
            <li  class='nav-item' style='margin: 0 50px 0 0 !important;'>
            <a class='nav-link active'  href='AddArticle.php' target='_blank'>Add Article</a> <!-- aria-current='page' -->
            </li>
            <li  class='nav-item' style='margin: 0 70px 0 0 !important;'>
            <a class='nav-link active'  href='Recommendation.php'  target='_blank'>Recommendation</a>
            </li>
        </ul>
        <form class='d-flex' method='POST'>
        <?php 
        if(isset($_SESSION['user'])){
            echo "<button name='logout' class='btn btn-outline-dark mx-3'  type='submit'>Log Out</button>";
        }

        else{
            echo "<button name='login' class='btn btn-outline-dark mx-3'  type='submit'>Sign IN</button>";
        }
        ?>
        
        </form>
        </div>
    </div>
</nav>


</header>

<?php
if(isset($_POST['login'])){
    header("Location: login.php" );  
}
if(isset($_POST['logout'])){
    session_destroy();
    header("Location: index.php" );  
}
?>
