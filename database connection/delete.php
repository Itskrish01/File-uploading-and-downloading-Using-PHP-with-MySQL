<?php
include 'db.php';
include "showdata.php";
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $deletion = "DELETE FROM user_table WHERE id = $id";
    $result = $con->query($deletion);
    
    if($result == TRUE){
        echo "Your data has been deleted!";
    }
    else{
        echo "Error";
    }
}

?>