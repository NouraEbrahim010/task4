<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

    
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = md5($_POST['password']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    
    $select = $conn->prepare("SELECT * FROM `users` WHERE email = ? And password =?");
    $select->execute([$email,$pass]);
 $row = $select->fetch(PDO::FETCH_ASSOC);
    if($select->rowCount() > 0){
        if($row['user_type'] == 'admin'){
            $_SESSION['admin_id'] = $row['id'];
            header('location:adminpage.php');}
        
            elseif($row['user_type'] == 'user'){
                $_SESSION['user_id'] = $row['id'];
                header('location:userpage.php');}
        else{
            $message[]='no user found!';
        }
    }else{
        $message[]='incorrect email or password!';
    }

 
 }
 
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
   if(isset($message)){
      foreach($message as $message){
         echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
?>

    <section class="form-container">
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Login now</h3>
            <input type="email" required placeholder="Enter Your Email" class="box" name="email">
            <input type="password" required placeholder="Enter Your Password" class="box" name="password">
            <p>don't have an account? <a href="register.php">Register now</a></p>
            <input type="submit" value="login now" class="btn" name="submit">


        </form>
    </section>
    
</body>
</html>



