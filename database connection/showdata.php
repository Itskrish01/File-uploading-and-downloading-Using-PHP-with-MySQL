<?php 

include "db.php";

$sql = "SELECT * FROM user_table";

$result = $connection->query($sql);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>database</title>

    <style>

        body{
            font-family: 'Fredoka', sans-serif;
            font-size: 20px;
        }
        table, th, td{
            border: 1px black solid;
        }

        th{
           padding: 15px;
        }

        td{
            padding: 30px;
        }

        .container{
            position: relative;
            display: flex;
            justify-content: center;
            top: 100px
        }

        .newuser{
            position: relative;
            top: 0;
        }

        a{
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>
<a class="newuser" href="index.php">add new user</a>
<div class=container>

    <table class="table">
        
        <thead>
            
            <tr>
                
                <th>profile</th>
                
                <th>Name</th>

                <th>username</th>
                
                <th>phone number</th>
                
                <th>delete</th>
                
                <th>edit</th>
                
                <th>Download resume</th>
                
            </tr>
            
        </thead>
        
        <tbody> 
            
            <?php

if ($result->num_rows > 0) {
    
    while ($row = $result->fetch_assoc()) {
        
        ?>

<tr> 
<?php

    $resume = $row["file"];

?>
    
        <td><img style="border-radius:50%; width: 100px" src="profile/<?php echo $row['profile']; ?>" alt=""></td>
        
        <td><?php echo $row['first_name']; ?></td>
        
        <td><?php echo $row['username']; ?></td>

        <td><?php echo $row['phone_number']; ?></td>

        <td><a class="btn btn-danger" href="delete.php?id=<?php echo $row['id']; ?>">Delete</a></td>

        <td><a class="btn btn-primary" href="update.php?id=<?php echo $row['id']; ?>">Edit</a></td>

        <td><a class="btn btn-success" href="profile/<?php echo $row['file'] ?>" download>Download</td>
        
</tr>                       
        
<?php }

}

?>                

</tbody>

</table>
</div>


</body>
</html>
