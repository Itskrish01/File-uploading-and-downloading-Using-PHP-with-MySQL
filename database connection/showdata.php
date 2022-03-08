<?php
include 'db.php';




$query = "SELECT * FROM user_table";
echo '<table border="0" cellspacing="2" cellpadding="2"> 
<tr> 
    <td><strong> Name</strong> </td> 
    <td> <strong> userid</strong> </td> 
    <td> <strong> phone Number</strong> </td> 
    <td> Delete your data </td>
    <td> Edit Your data </td>
    <td> Download files </td>
</tr>';

if($rs = $con->query($query)){
while($row = $rs->fetch_assoc()){
    $name = $row["first_name"];
    $user_id = $row["userid"];
    $Pnumber = $row["phone_number"];
    $resume = $row["file"];


    echo '<tr>
    <td>'.$name.'</td>
    <td>'.$user_id.'</td>
    <td>'.$Pnumber.'</td>
    
    <td> <a href="delete.php?id='.$row["id"].'">Delete</a> </td>
    <td> <a href="edit.php?id='.$row["id"].'">Edit</a> </td>
    <td><a href="media/'.$row['file'].'" download>Download</a></td>
            </tr>';
}


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table {
            margin: 0 auto;
            font-size: large;
            border: 1px solid black;
        }
  
       
  
        td {
        
            border: 1px solid black;
        }
  
        th,
        td {
            font-weight: bold;
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
  
        td {
            font-weight: lighter;
        }

        .home{
            position: relative;
            top: 300px;
            left:0;
        }
    </style>
    <title>Document</title>
</head>
<body>
    <a class="home" href="index.html">Add new user</a>
</body>
</html>