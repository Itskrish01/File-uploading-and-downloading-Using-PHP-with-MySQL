<?php
include "db.php";

$dir_files = "media/";
$dir_profile = "profile/";

$target_file = $dir_files . basename($_FILES["resume"]["name"]);
$target_profile = $dir_profile . basename($_FILES["profile"]["name"]);

$ableToUpload = 1;

$filetype = strtolower(pathinfo($target_profile,PATHINFO_EXTENSION));
$filetype1 = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if($filetype != "jpg" && $filetype != "jpeg" && $filetype != "png" && $filetype != "pdf"){
    $fileerror = "Only JPEG, PDF, PNG and JPG are allowed to be uploaded! <br>";
    header("Location: index.php?fileerror=$fileerror");
    $ableToUpload = 0;
}

if($filetype1 != "jpg" && $filetype1 != "jpeg" && $filetype1 != "png" && $filetype1 != "pdf"){
    $fileerror1 = "Only JPEG, PDF, PNG and JPG are allowed to be uploaded! <br>";
    header("Location: index.php?fileerror1=$fileerror1");
    $ableToUpload = 0;
}


if(isset($_POST['submit'])){
    
    $profile = $_FILES['profile'];
    $profilename = $profile['name'];


    $resume = $_FILES['resume'];
    $resumename = $resume['name'];



    $name = $_POST['name'];
    $username = $_POST['username'];
    $phone = $_POST['number'];



    if($ableToUpload == 0){
        echo "Sorry, Your file was not uploaded!";
       
      }

    else{
        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["resume"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    
    
    
        if (move_uploaded_file($_FILES["profile"]["tmp_name"], $target_profile)) {
            echo "The file ". htmlspecialchars( basename( $_FILES["profile"]["name"])). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    
      
if($ableToUpload == 1){
    $sql = "INSERT INTO user_table (first_name, username, phone_number, file, profile) values ('$name', '$username', '$phone', '$resumename', '$profilename')";
    $result = $connection->query($sql);

    if($result == TRUE){
        echo "Success!";
        header("Location: showdata.php");
        
    }

    else{
        echo "Falied";
        $err = mysqli_error($connection);
        header("Location: index.php?error=$err");
    }
}
   

}
?>
