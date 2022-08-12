<?php
require_once('vendor\autoload.php');
require_once('Connect.php');
session_start();

if(isset($_POST['btn-Login'])){

    $results = $client->run('
          match (a:user{email:$email, password:$password}) 
          RETURN 
                a.id AS id ,
                a.fullName AS fullName ,
                a.email AS email ,
                a.password AS password ',
          ['email' => $_POST['email-login'], 'password' => $_POST['password-login']]);

    if($results != null) {
      $_SESSION['user'] = '';
      foreach ($results as $result) {
        $_SESSION['user'] = ["id" => $result->get('id').PHP_EOL ,
                              "fullName" => $result->get('fullName').PHP_EOL ,
                              "email" => $result->get('email').PHP_EOL ,
                              "password" => $result->get('password').PHP_EOL];
                              header("location:index.php");
      } // end foreach

      echo '<div class="alert alert-warning alert-dismissible fade show  fixed-top top-0 text-center" role="alert">
              email or password  not correct !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            $_POST['btn-Login'] = $_POST['email-login'] =  $_POST['password-login'] = null;
      }// end if

  } // end btn-login

  $id = 0 ;

  if(isset($_POST['btn-Signup'])) {

    $getEmail = $client->run('
          match(userNode:user{email:$email}) 
          RETURN userNode',
          ['email' => $_POST['email-signup']]);

    $email = '';
      foreach ($getEmail as $values) {
        $nodeMail = $values->get('userNode');
        $email = $nodeMail->getProperty('email');
           
      }

      if($email != $_POST['email-signup']){
        $getId = $client->run('match(node:user)
                    return node, node.id as id 
                    order by node.id desc limit 1');
          foreach ($getId as $idValues) {
              $nodeId = $idValues->get('node');
              $id=$nodeId->getProperty('id');
          }

        $id += 1;

        $client->run('
            merge(a:user{fullName:$fullName, id:$id,
            email:$email, password: $password})',
            ['id' => $id, 'fullName' => $_POST['fullName'], 
            'email' => $_POST['email-signup'], 'password' => $_POST['password-signup']]);

      echo '<div class="alert alert-success alert-dismissible fade show  fixed-top top-0 text-center" role="alert">
            Your account has been created successfully !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
          }

          else{
            echo '<div class="alert alert-danger alert-dismissible fade show  fixed-top top-0 text-center" role="alert">
              Account already exist !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
          }
    }
   
  
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <title>Login</title>
    <link rel="stylesheet" href="../src/css/login-style.css" />
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.js">
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet"    href="../src/bootstrap/js/bootstrap.bundle.js">
    <link rel="stylesheet"    href="../src/bootstrap/css/bootstrap.min.css">
    <script                   src="../src/bootstrap/js/bootstrap.js"></script>
    <script                   src="../src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script                   src="../src/bootstrap/js/bootstrap.min.js"></script>
    <script                   src="../src/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../src/js/jQuery v3.6.0.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <style>
    
  </style>

  </head>
  <body>
    <div class="wrapper">
      <div class="title-text">
        <div class="title login">
          Login
        </div>
        <div class="title signup">
          Signup
        </div>
      </div>
      <div class="form-container">
        <div class="slide-controls">
          <input type="radio" name="slide" id="login" checked />
          <input type="radio" name="slide" id="signup" />
          <label for="login" class="slide login">Login</label>
          <label for="signup" class="slide signup">Signup</label>
          <div class="slider-tab"></div>
        </div>
        <div class="form-inner">
          <form action="#" class="login" method="post">
            <div class="field">
              <input
                type="text"
                placeholder="email Address"
                name="email-login"
                required
              />
            </div>
            <div class="field">
              <input
                type="password"
                placeholder="password"
                name="password-login"
                required
              />
            </div>
            <div class="field _btn">
              <div class="_btn-layer"></div>
              <input type="submit" value="Login" name="btn-Login" />
            </div>
            <div class="signup-link">
              Not a member?
              <a href="">Signup now</a>
            </div>
          </form>
          <form action="#" class="signup" method="post">
            <div class="field">
              <input type="text" placeholder="Full Name" name="fullName" required />
            </div>
           
            <div class="field">
              <input
                type="email"
                placeholder="email Address"
                name="email-signup"
                required
              />
            </div>
            <div class="field">
              <input
                type="password"
                placeholder="password"
                name="password-signup"
                required
              />
            </div>
            <div class="field _btn">
              <div class="_btn-layer"></div>
              <input type="submit" value="Signup" name="btn-Signup" />
            </div>
          </form>
        </div>
      </div>
    </div>
    <script>
      const loginText = document.querySelector('.title-text .login')
      const loginForm = document.querySelector('form.login')
      const login_btn = document.querySelector('label.login')
      const signup_btn = document.querySelector('label.signup')
      const signupLink = document.querySelector('form .signup-link a')
      signup_btn.onclick = () => {
        loginForm.style.marginLeft = '-50%'
        loginText.style.marginLeft = '-50%'
      }
      login_btn.onclick = () => {
        loginForm.style.marginLeft = '0%'
        loginText.style.marginLeft = '0%'
      }
      signupLink.onclick = () => {
        signup_btn.click()
        return false
      }
      

    window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove(); 
        });
    }, 2000);

    </script>
  </body>
</html>
