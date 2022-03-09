

 <?php
 if(isset($_GET['error'])){
    $err = $_GET['error'];
    echo $err;
    
 }

    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .form {
            display: flex;
            position: relative;
            top: 200px;
            justify-content: center;

        }

        button {
            display: flex;
            position: relative;
            top: 200px;
            left: 700px;
        }

        a {
            display: flex;
            position: relative;
            top: 200px;
            left: 700px;
        }


        

    </style>

    <title>Home</title>
</head>

<body>

    <form name="myform" method="POST" action="form.php" autocomplete="off" enctype="multipart/form-data">

        <div class="form">
            <table>
                <tr>
                    <td>Name:</td>
                    <td> <input type="text" name="fname" pattern="[a-z]{1,15}"
                            title="Name should only contain lowercase letters. e.g. john" minlength="3" required> </td>
                </tr>
                <tr>
                    <td>username:</td>
                    <td> <input type="text" name="userid" maxlength="20" required>   </td>
                </tr>
                <tr>
                    <td>Phone number:</td>

                    <td> <input type="text" name="phone"
                            title="Phone number should start with 7-9 and remaing 9 digit with 0-9"
                            pattern='[7-9]{1}[0-9]{9}' maxlength="10" required>
                    </td>
                </tr>

                <tr>
                    <td>Upload Resume</td>
                    <td><input type="file" name="file" required></td>
                </tr>

                <tr>
                    <td>Upload Profile picture</td>
                    <td><input type="file" name="ProfilePic" required></td>
                </tr>


                <tr>
                    <td> <input class="btn btn-info" type="submit" name="submit" value="submit"> </td>
                </tr>
            </table>

        </div>
    </form>
    

  

    <a href="showdata.php">Show my data</a>

</body>

</html>
