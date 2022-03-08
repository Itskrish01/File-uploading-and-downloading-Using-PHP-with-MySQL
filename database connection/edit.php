<?php
include 'db.php';


if (isset($_GET['id'])) {

    $idd = $_GET['id']; 

    $updation = "SELECT * FROM user_table WHERE id=$idd";

    $resu = $con->query($updation); 

         

        while ($row = $resu->fetch_assoc()) {

            $name = $row["first_name"];
            $user_id = $row["userid"];
            $Pnumber = $row["phone_number"];
            

        }
        
   

}

if (isset($_POST['update'])) {

    $name = $_POST['fname'];
    $user_id = $_POST['userid'];
    $Pnumber = $_POST['phone'];
   

    $updateSQL = "UPDATE user_table SET first_name =' $name', userid = '$user_id', phone_number = '$Pnumber' WHERE id='$idd'";
    $res = $con->query($updateSQL);
    if($res){
        echo "We have updated your record!";
    } 
    else{
        echo "Sorry";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form name="myform" method="POST">

        <div class="form">
            <table>
                <tr>
                    <td>Name:</td>
                    <td> <input type="text" name="fname" value="<?php echo $name ?>"> </td>
                </tr>
                <tr>
                    <td>user_id:</td>
                    <td> <input type="text" name="userid" value="<?php echo $user_id ?>"> </td>
                </tr>
                <tr>
                    <td>Phone number:</td>
                    <td> <input type="text" name="phone" value="<?php echo $Pnumber ?>"> </td>
                </tr>
                
               
                <tr>
                    <td> <input type="submit" name="update" value="submit"> </td>
                </tr>
            </table>

        </div>
    </form>

    <a href="showdata.php">Show my data</a>
</body>

</html>

