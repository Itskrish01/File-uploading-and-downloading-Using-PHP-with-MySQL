<?php
 
    // importing the database connection file
    include 'db.php';

    // where the file will be stored
    $target_dir = "media/";

    // returning path of the file
    $target_file = $target_dir.basename($_FILES["file"]["name"]);

    // storing extension of a file in a variable
    $filetype = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $ableToUpload = 1;


    // Checking if file is valid or not
    if($filetype != "jpg" && $filetype != "jpeg" && $filetype != "png" && $filetype != "pdf"){
      echo "Sorry, But only JPEG, PDF, PNG and JPG are allowed to be uploaded! <br>";
      $ableToUpload = 0;
    }


    // When we press the submit button of our form
    if(isset($_POST['submit']))
    {

        //storing details and file of user in variables
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
            echo "Sorry, there was an error uploading your file.<br>";
          }
        }
    }
    
    
if($ableToUpload == 1){


      if($name){
        $sql = "INSERT INTO user_table (first_name, userid, phone_number, file) VALUES ('$name', '$user_id', '$Pnumber', '$filename')";
                $rs = mysqli_query($con, $sql);

                

            
            if($rs)
            {
                echo "Successfully saved";
                
                
            }
            else{
                echo "failed to add the details into your database!";
            }
      }
      else{
        echo "Name section is required!";
      }
}
    

      
      
 

        
    
?>