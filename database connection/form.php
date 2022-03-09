<?php
 
    // importing the database connection file
    include 'db.php';

    // where uploaded files will be stored
    $target_dir = "media/";
    $profile_target_dir = "profile/";

    // returning filename from path
    $target_file = $target_dir.basename($_FILES["file"]["name"]);
    $profile_target_file = $profile_target_dir.basename($_FILES["ProfilePic"]["name"]);

    // storing extension of a file in a variable
    $filetype = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $ableToUpload = 1;


    // Checking if file is valid or not
    if($filetype != "jpg" && $filetype != "jpeg" && $filetype != "png" && $filetype != "pdf"){
      echo "Sorry, But only JPEG, PDF, PNG and JPG are allowed to be uploaded! <br>";
      $ableToUpload = 0;
    }


    // operation starts after pressing the submit
    if(isset($_POST['submit']))
    {

        // storing details and file of user in variables
        $profile = $_FILES['ProfilePic'];
        $profilename = $profile['name'];
        $name = $_POST['fname'];
        $user_id = $_POST['userid'];
        $Pnumber = $_POST['phone'];
        $file = $_FILES['file'];       
        $filename = $file['name'];

      

        // if everything is ok, try to upload file
        if($ableToUpload == 0){
          echo "Sorry, Your file was not uploaded!";
         
        }
        else{
          
          // Where the file uploading process starts
          if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            echo "The file ". htmlspecialchars( basename($_FILES['file']['name'])). " has been uploaded.<br>";
          } 
          
          
          else {
            // check if your code is error free
            echo "Sorry, there was an error uploading your file.<br>";
          }
        }
      
    }





  
  if($ableToUpload == 1){
    
    
      if($name){

        if (move_uploaded_file($_FILES['ProfilePic']['tmp_name'], $profile_target_file)) {
          echo "The img ". htmlspecialchars( basename($_FILES['ProfilePic']['name'])). " has been uploaded.<br>";
        } 
        // here we send our details to the database that we created
        $sql = "INSERT INTO user_table (first_name, userid, phone_number, file, profile) VALUES ('$name', '$user_id', '$Pnumber', '$filename', '$profilename')";
        $rs = mysqli_query($con, $sql);
        
        
        
        
        
        if($rs)
        {
          echo "Successfully saved";
          header("Location: showdata.php");
          exit();
          
          
        }
            else{
              echo "failed to add the details into your database!<br>";
              die(mysqli_error($con));
            }
          }
          else{
            echo "Name section is required!";
          }
        }
        
        
        
        
        
        
        
        ?>
