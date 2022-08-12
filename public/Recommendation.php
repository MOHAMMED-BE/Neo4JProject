<?php

require_once('Connect.php');

session_start();
if(isset($_SESSION['user'])){
    $idConnected = $_SESSION['user']['id'];
    
if(isset($_POST['collaborate'])){
    $idCollabor = $_POST['collaborate'];

    $client->run('
        match(current:Author{id:$idConnected})
        match(collabor:Author{id:$idCollabor})
        merge(current)-[:inviteToCollabor]->(collabor)',
            [
                'idCollabor'  => $idCollabor,
                'idConnected' => $idConnected
            ]);

        echo '<div class="alert alert-success alert-dismissible fade show  fixed-top top-0 text-center" role="alert">
        Your invitation has been sent successfully !
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';

}

if(isset($_POST['Accept'])){
    $idForCollabor = $_POST['Accept'];

   $client->run('
   match(collabor:Author{id:$idForCollabor})-[CurrentRelation:inviteToCollabor]->(current:Author{id:$idConnected})
   merge (collabor)-[newRelation:colabor]->(current) delete CurrentRelation',
   [
       'idForCollabor'  => $idForCollabor,
       'idConnected' => $idConnected
   ]);

}


if(isset($_POST['Refuse'])){
    $idForCollabor = $_POST['Refuse'];
    $client->run('
    match(collabor:Author{id:$idForCollabor})-[CurrentRelation:inviteToCollabor]->(current:Author{id:$idConnected})
    delete CurrentRelation',
    [
        'idForCollabor'  => $idForCollabor,
        'idConnected' => $idConnected
    ]);
}

    $id    = $_SESSION['user']['id'];
    $email = $_SESSION['user']['email'];
    $i=0;

    $keyArray = array();

    $getKeywords = $client->run(
        'match(a:Author{id:$id})-[:Publish]->(p:Paper)-[:Has]->(keyNode:Keywords)
        return a, keyNode',
    ['id'=>$id]);
    
    foreach($getKeywords as $keyValues) {
        $nodeAuthor = $keyValues->get('a');
        $node = $keyValues->get('keyNode');
        $label = $node->getProperty('label') ; 
        array_push($keyArray,$label);

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
    <title>Recommendation</title>
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
include("header.php");
?>

<section class='container shadow-lg p-3 rounded' style='height: 65vh;'>
         <h2>Recommended Authors</h2>
			<table class='table'>
				<thead class='table-dark text-center'> 
            		<tr>
                        <th>Author Name</th>
        	        	<th>Email</th>
        	        	<th>Collaboration</th>
        	        </tr>
        		</thead>
        		<tbody class='text-center'>
                    <?php

                    $mail = '';
                    $cpt = 0;
                    $mailArray = array();
                    $j=0 ;

                for($i=0;$i < count($keyArray);$i++){

                    $getAuthor = $client->run(
                        'match(a:Author)
                        match(a1:Author{id:$id})-[:Publish]->(p:Paper)-[:Has]->(:Keywords{label:$label})
                        where 
                            not exists((a1)-[:colabor]->(a))
                        and
                            not exists((a1)-[:inviteToCollabor]->(a))
                        and 
                            not exists((a)-[:colabor]->(a1))
                        and
                            not exists((a)-[:inviteToCollabor]->(a1))
                        return distinct a',
                    [
                        'label'=>$keyArray[$i],
                        'id'=> $id
                    ]);

                    foreach($getAuthor as $values) {

                        $node = $values->get('a');
                                $fullName = $node->getProperty('fullName') ; 
                                $email    = $node->getProperty('email') ; 

                                if($_SESSION['user']['email'] == $email){
                                    continue;
                                }

                                array_push($mailArray,$email);

                                if($cpt >= 1){
                                        if($email == $mailArray[0]){
                                            break ;
                                    }
                                }
                                
                                $idAuthor = $node->getProperty('id') ; 
                                

                        echo "<tr>";
                        echo "<td>" . $fullName  . "</td>";
                        echo "<td>". $email . "</td>";
                        echo "<td>
                            <form action='' method='post'>
                                <button class='btn btn-warning' style='width:120px' type='submit' name='collaborate' value='". $idAuthor ."'>Collaborate</button>
                            </form>
                        </td>";
                    echo "</tr>";

                    $cpt++;

                        }
                    }

                    ?>
                    
        		</tbody>
			</table>	
		</section>

        <?php

            $getInvitation = $client->run(
                'match(Invited:Author)-[i:inviteToCollabor]->(Receiver:Author{id:$idReceiver})
                return Invited , count(Invited.id) as totaleInvitation',
                ['idReceiver'=>$id]);
                $totale = 0;
            foreach($getInvitation as $Values) {

                $Invited = $Values->get('Invited');
                $totale = $Values->get('totaleInvitation') ;
            }
                if($totale !=0){

                   echo " <section class='container shadow-lg p-3 rounded mt-6' style='height: 65vh;margin-top: 5rem;'>
                    <h2>Invitations For Collaboration</h2>
                    <table class='table'>
                    <thead class='table-dark text-center'> 
                        <tr>
                            <th>Author Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class='text-center'>";

                $getInvitation = $client->run(
                    'match(Invited:Author)-[i:inviteToCollabor]->(Receiver:Author{id:$idReceiver})
                     return Invited',
                    ['idReceiver'=>$id]);

                foreach($getInvitation as $Values) {

                    $Invited = $Values->get('Invited');
                    $fullName = $Invited->getProperty('fullName') ;
                    $email = $Invited->getProperty('email') ;
                    $idReceiver = $Invited->getProperty('id') ;


                    echo "<tr>";
                    echo "<td>" . $fullName . "</td>";
                    echo "<td>".  $email ."</td>";
                    echo "<td>
                            <form action='' method='post'>
                                <button class='btn btn-outline-success' style='width:100px' type='submit' name='Accept' value='".$idReceiver."'>Accept</button>
                                <button class='btn btn-outline-danger' style='width:100px' type='submit' name='Refuse' value='".$idReceiver."'>Refuse</button>
                            </form>
                        </td>";
                    echo "</tr>";

                }
        		echo"</tbody>
			</table>	
		</section>";
                }

        ?>

<script>
        window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 2000);

    </script>
 
</body>
</html>

<?php

?>

