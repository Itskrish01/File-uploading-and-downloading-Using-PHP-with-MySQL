<?php
include "db.php";

$dir_files = "media/";
$dir_profile = "profile/";

$target_file = $dir_files . basename($_FILES["resume"]["name"]);
$target_profile = $dir_profile . basename($_FILES["profile"]["name"]);

if(isset($_POST['submit'])){
    
    $profile = $_FILES['profile'];
    $profilename = $profile['name'];


    $resume = $_FILES['resume'];
    $resumename = $resume['name'];



    $name = $_POST['name'];
    $username = $_POST['username'];
    $phone = $_POST['number'];





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
      

    $sql = "INSERT INTO user_table (first_name, username, phone_number, file, profile) values ('$name', '$username', '$phone', '$resumename', '$profilename')";
    $result = $connection->query($sql);

    if($result == TRUE){
        echo "Success!";
        header("Location: showdata.php");
    }

    else{
        echo "Falied";
    }

}
?>
