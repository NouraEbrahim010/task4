<?php

include 'config.php';
session_start();
$admin_id= $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:login.php');
 }

if(isset($_POST['update'])){
    $name=$_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email=$_POST['email'];
    $update_profile =$conn->prepare("UPDATE`users` SET name = ?, email = ? WHERE id = ?");
    $update_profile->execute([$name,$email,$admin_id]);
    $old_img=$_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_folder = 'uploaded_img/'.$image;
    if(!empty($image)){

        if($image_size > 2000000){
            $message[] = 'image size is too large!';
    }else{
        $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
        $update_image->execute([$image, $admin_id]);
        
        if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('uploaded_img/'.$old_img);
            $message[] = 'image has been updated!';
         }
    }

}
   $old_pass = $_POST['old_pass'];
   $previous_pass = md5($_POST['previous_pass']);
   $previous_pass = filter_var($previous_pass, FILTER_SANITIZE_STRING);
   $new_pass = md5($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = md5($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if(!empty($previous_pass) || !empty($new_pass) || !empty($confirm_pass)){
      if($previous_pass != $old_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'confirm password not matched!';
      }else{
         $update_password = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_password->execute([$confirm_pass, $admin_id]);
         $message[] = 'password has been updated!';
      }
   }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile Update</title>
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

<h1 class="title">Update<span>Admin</span>Profile</h1>

<section class="update-profile-container">

<?php
      $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
      $select_profile->execute([$admin_id]);
      $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="post" enctype="multipart/form-data">
   <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
      <div class="flex">
         <div class="inputBox">
            <span>Username : </span>
            <input type="text" name="name" required class="box" placeholder="Enter Your Username" value="<?= $fetch_profile['name']; ?>">
            <span>Email : </span>
            <input type="email" name="email" required class="box" placeholder="Enter Your Email" value="<?= $fetch_profile['email']; ?>">
            <span>Profile Pic : </span>
            <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
            <span>Old Password :</span>
            <input type="password" class="box" name="previous_pass" placeholder="Enter Previous Password" >
            <span>New Password :</span>
            <input type="password" class="box" name="new_pass" placeholder="Enter New Password" >
            <span>Confirm Password :</span>
            <input type="password" class="box" name="confirm_pass" placeholder="Confirm New Password" >
         </div>
      </div>
      <div class="flex-btn">
         <input type="submit" value="update profile" name="update" class="btn">
         <a href="adminpage.php" class="option-btn">go back</a>
      </div>
   </form>
</section>
</body>
</html>