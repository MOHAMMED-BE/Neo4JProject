<?php

require_once('Connect.php');

session_start();

$idPaper = 0;

if(isset($_SESSION['user']) || $_SESSION['user'] != ''){
    if(isset($_POST['Add'])){

        $id       = $_SESSION['user']['id'];
        $email    = $_SESSION['user']['email'] ;
        $fullName = $_SESSION['user']['fullName'];
        $password = $_SESSION['user']['password'];

        $keyword = $_POST['keywords'] ;
        $description    = $_POST['desc'];
        $title   = $_POST['title'];
        $date    = $_POST['date'];;
        $image   = $_FILES['image']['name'];
        $temp    = $_FILES['image']['tmp_name'];
        $image   = $image;

        $array = array();
        $array = explode(',', $keyword);

        $getIdPaper = $client->run('match(p:Paper) return p, p.idPaper as idPaper order by p.idPaper desc limit 1');
        foreach ($getIdPaper as $idValues) {
            $nodeIdPaper = $idValues->get('p');
            $idPaper=$nodeIdPaper->getProperty('idPaper');

        }
    
        $i = 0;
        $idPaper += 1;
        for($i=0;$i< count($array);$i++){
    
          $client->run('
            merge(a:Author{id:$id, fullName: $fullName, email:$email,password: $password}) 
            merge(p:Paper{idPaper:$idPaper, title:$title, description:$description, image:$image})
            merge(k:Keywords{label:$keyword})
            merge(a)-[:Publish{date:$date}]->(p)
            merge(p)-[:Has]->(k)',
            ['id'=>$id,'fullName'=>$fullName,'email' => $email , 'password' => $password,
            'idPaper'=>$idPaper, 'image'=>$image,'description' => $description , 'title' => $title,
            'keyword' => $array[$i], 'date' => $date]);
    
        } // end for
    
        move_uploaded_file($temp,"../images/".$image);

        echo '<div class="alert alert-success alert-dismissible fade show  fixed-top top-0 text-center" role="alert">
        Article added successfully !
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    
    }

}

else{
    header("location:login.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Article</title>
    <link rel="stylesheet"    href="../src/css/style.css" />
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.js">
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.bundle.js">
    <link rel="stylesheet"    href="../src/bootstrap/css/bootstrap.min.css">
    <script                   src="../src/bootstrap/js/bootstrap.js"></script>
    <script                   src="../src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script                   src="../src/bootstrap/js/bootstrap.min.js"></script>
    <script                   src="../src/bootstrap/js/bootstrap.bundle.js"></script>
    <script                   src="../src/js/jQuery v3.6.0.js"></script>

</head>
<body>

<?php
require_once('header.php');

?>

    <section class='container mt-3 addArt'>
        <form method="POST" class='shadow-lg p-3 rounded mt-6' action='AddArticle.php' enctype="multipart/form-data">
            
            <label for="">Title Of Article : </label>
            <input style='margin-bottom:10px' required class="form-control" name="title" placeholder="Title Of Artcle" type='text' />
            <label for="">Description : </label>
            <input style='margin-bottom:10px' required class="form-control" name="desc" placeholder="Description" type='text' />
            <label for="">Date of Publication : </label>
            <input style='margin-bottom:10px' required class="form-control" name="date" value='<?php echo date('Y-m-d'); ?>' type='date' />
            <label for="">Comma Separated keywords : </label>
            <input style='margin-bottom:10px' required class="form-control" name="keywords" placeholder="Comma Separated keywords" type='text' />
            <label for="">Add Article Image : </label>
            <input style='margin-bottom:10px' required class="form-control" name="image" type='file' />
            <button style='width:150px;letter-spacing: 1.5px;' class="btn btn-warning" name="Add" >Add Article</button> <br> 

        </form>
    </section>

    <script>
        window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 2000);

    </script>

</body>
</html>