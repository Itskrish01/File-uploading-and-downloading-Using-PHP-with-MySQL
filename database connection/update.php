<?php

include "db.php";

if(isset($_GET['id'])){
    $id = $_GET['id']; 

    $updation = "SELECT * FROM user_table WHERE id=$id";

    $result = $connection->query($updation); 

         

    while ($row = $result->fetch_assoc()) {

        $name = $row["first_name"];
        $username = $row["username"];
        $number = $row["phone_number"];
        

    }

}


if(isset($_POST['update'])){

    $name = $_POST['name'];
    $username = $_POST['username'];
    $number = $_POST['number'];


    $sql = "UPDATE user_table SET first_name = '$name', username = '$username', phone_number = '$number' WHERE id='$id'";

    $res = $connection->query($sql);

    if($res == TRUE){
        echo "Success";
        header("Location: showdata.php");
    }

    else{
        echo "Failed";
    }
}


?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Updation</title>
</head>
<body>
    <form method="post">
        <table>
            <tr>
                <td>name: </td>
                <td><input type="text" name="name" value="<?php echo $name; ?>"></td>
            </tr>

            <tr>
                <td>username: </td>
                <td><input type="text" name="username" value="<?php echo $username; ?>"></td>
            </tr>

            <tr>
                <td>phone number: </td>
                <td><input type="text" name="number" value="<?php echo $number; ?>"></td>
            </tr>

            <tr>
                <td><input type="submit" name="update" value="Update"></td>
            </tr>
        </table>
    </form>
</body>
</html>
