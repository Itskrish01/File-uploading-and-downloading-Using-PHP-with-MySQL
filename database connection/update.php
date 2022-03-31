<?php

include "db.php";

if(isset($_GET['id'])){
    $id = $_GET['id']; 

    $updation = "SELECT * FROM user_table WHERE id=$id";

    $result = $connection->query($updation); 

         

    while ($row = $result->fetch_assoc()) {

        $name = $row["first_name"];
        $lastname = $row["last_name"];
        $username = $row["username"];
        $number = $row["phone_number"];
        

    }

}


if(isset($_POST['update'])){

    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $number = $_POST['number'];


    $sql = "UPDATE user_table SET first_name = '$name', last_name = '$lastname', username = '$username', phone_number = '$number' WHERE id='$id'";

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
    
    <style>
        * {
  margin: 0;
  padding: 0;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  font-family: "Open Sans", sans-serif;
  background: #e2e2e2;
}

svg {
  position: fixed;
  top: 10px;
  left: 180px;
}

.container {
  position: relative;
  top: 100px;
  left: 35%;
  display: block;
  margin-bottom: 80px;
  width: 500px;
  height: 530px;
  background: #fff;
  border-radius: 5px;
  overflow: hidden;
  z-index: 1;
}

h2 {
  padding: 40px;
  font-weight: lighter;
  text-transform: uppercase;
  color: #414141;
}

input {
  display: block;
  height: 50px;
  width: 90%;
  margin: 0 auto;
  border: none;
}
input::placeholder {
  -webkit-transform: translateY(0px);
  transform: translateY(0px);
  -webkit-transition: 0.5s;
  transition: 0.5s;
}
input:hover, input:focus, input:active:focus {
  color: #ff5722;
  outline: none;
  border-bottom: 1px solid #ff5722;
}
input:hover::placeholder, input:focus::placeholder, input:active:focus::placeholder {
  color: #ff5722;
  position: relative;
  -webkit-transform: translateY(-20px);
  transform: translateY(-20px);
}

.email,
.pwd {
  position: relative;
  z-index: 1;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  padding-left: 20px;
  font-family: "Open Sans", sans-serif;
  color: #858585;
  font-weight: lighter;
  -webkit-transition: 0.5s;
  transition: 0.5s;
}

button {
  cursor: pointer;
  display: inline-block;
  float: left;
  width: 100%;
  height: 60px;
  margin-top: 20px;
  border: none;
  font-family: "Open Sans", sans-serif;
  text-transform: uppercase;
  color: #fff;
  -webkit-transition: 0.5s;
  transition: 0.5s;
}
button:nth-of-type(1) {
  background: #673ab7;
}
button:nth-of-type(2) {
  background: #ff5722;
}
button span {

  display: block;
  margin: -10px 20%;
  -webkit-transform: translateX(0);
  transform: translateX(0);
  -webkit-transition: 0.5s;
  transition: 0.5s;
}


label{
    padding: 45px;
    font-size: 13px;
}




    </style>
    <title>Updation</title>
</head>
<body>
    <form method="post">


<div class="container">
  <h2>Edit your details</h2>
     <label>FIRST NAME</label>
    <input type="text" name="name" class="email" value="<?php echo $name; ?>">
<br>
<label>LAST NAME</label>
    <input type="text" name="lastname" class="pwd" value="<?php echo $lastname; ?>">
    <br>
    <label>USERNAME</label>
    <input type="text" name="username" class="pwd" value="<?php echo $username; ?>">
    <br>
    <label>PHONE NUMBER</label>
    <input type="text" name="number" maxlength="10" class="pwd" value="<?php echo $number; ?>">

  <a href="#" class="link">
 
  </a>
  <br/>
  
  <button class="signin" name="update">
    <span>save changes</span>
  </button>
 

 
 
</div>
        
   
    </form>
</body>
</html>
