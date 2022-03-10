<?php
 
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300&display=swap" rel="stylesheet">
    <title>Home</title>

    <style>
        body {
            font-family: 'Fredoka', sans-serif;
            background-image: url(https://i.pinimg.com/originals/4a/94/26/4a94268541d7a0ed95a8be5138e8a288.jpg);
            background-repeat: no-repeat;
            background-size: 110%;
            font-weight: bold;
        }

        .container {
            border-radius: 10px;
            background: white;
            position: relative;
            top: 90px;
            width: 800px;
            height: 660px;
            box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;
            padding-bottom: 40px;
        }

        form {
            position: relative;
            top: 20px;
            padding: 20px 30px 40px;

        }

        .form-group {
            padding: 5px 0 20px;
        }

        .border {
            border-radius: 0;
            border-top: none !important;
            border-right: none !important;
            border-left: none !important;
            border-bottom: 1px #a6a6a6 solid !important;
        }



        .inline {
            padding: 0 -1px 10px 0;
        }



        input:focus {
            border-bottom: 1px #005aa8 solid !important;
            box-shadow: none !important;
        }

        a {
            position: absolute;
            top: -200px;
            left: -200px;
        }

        .button {
            padding: 90px;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
        }



        .btn {
            outline: none;
            position: relative;
            top: 50px;
            left: 100px;
            width: 500px;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px, rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
        }

        .err {
            color: #ff3b30;
        }


        .labels label {
            position: absolute;
            transition: .3s;
            color: #878787;
            font-weight: lighter;
            pointer-events: none;
        }

        .labels input:focus~label,
        .labels input:valid~label {
            font-size: 13px;
            transform: translateY(-23px);
            color: black;
            font-weight: bold;
        }

        @media only screen and (max-width: 768px) {

            body{
                background: url(https://images.pexels.com/photos/355209/pexels-photo-355209.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500);
                background-repeat: no-repeat;
                background-size: 100%;
            }

            .btn{
                left: 70px;
                width: 300px;
            }

            .lastname{
                width: 240px;
            }


            
        }
    </style>
</head>

<body>
    <div class="container">
        <p class="err pb-2 pt-3 ps-4"><?php 

        if(isset($_GET['error'])){
            $err = $_GET['error'];
            echo "Username already exists!";
         }


         if(isset($_GET['fileerror'])){
             $fileerror = $_GET['fileerror'];
             echo "Wrong file type! Only JPEG, PDF, PNG and JPG are allowed to be uploaded!";
         }

         
        
        ?></p>
        <h3 class="ps-4 pt-2">Enter your details here</h3>
        <form action="data.php" method="POST" enctype="multipart/form-data">

            <div class="form-row">
                <div class="labels">
                    <div class="inline" style="display: flex;">
                        <div class="form-group col-md-6 inline" style="padding-right: 20px;">

                            <input type="text" name="name" class="form-control border" id="inputEmail4" placeholder=" "
                                required>
                            <label style="top: 30px;">First name</label>
                        </div>
                        <div class="form-group col-md-6 inline">

                            <input type="text" name="lastname" class="lastname form-control border" id="inputPassword4" required>
                            <label style="top: 30px;" for="inputPassword4">Last name</label>
                        </div>
                    </div>
                    <div class="form-group">

                        <input type="text" name="username" class="form-control border" id="inputPassword4" required>
                        <label style="top: 90px;" for="inputPassword4">username</label>
                    </div>
                    <div class="form-group">

                        <input type="text" name="number" class="form-control border phone" id="exampleInputEmail1"
                            class="number-only" maxlength="10" pattern="[7-9]{1}[0-9]{9}" required>
                        <label style="top: 155px;" for="inputPassword4">Phone number</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputAddress">resume</label>
                <input type="file" name="resume" class="form-control" id="inputAddress" placeholder="1234 Main St"
                    required>
            </div>
            <div class="form-group">
                <label for="inputAddress2">profile</label>
                <input type="file" name="profile" class="form-control" id="inputAddress2"
                    placeholder="Apartment, studio, or floor">
            </div>


            <button type="submit" name="submit" class="btn btn-primary mt-2 mb-2">S U B M I T</button>



            <a href="showdata.php">show my data</a>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>

        </form>
    </div>


    <script>
        $(function () {

            $('.number-only').keyup(function (e) {
                    if (this.value != '-')
                        while (isNaN(this.value))
                            this.value = this.value.split('').reverse().join('').replace(/[\D]/i, '')
                            .split('').reverse().join('');
                })
                .on("cut copy paste", function (e) {
                    e.preventDefault();
                });

        });
    </script>
</body>

</html>
