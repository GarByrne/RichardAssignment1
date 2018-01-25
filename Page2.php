<?php

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

 // code to allow user to change their password
include("config.php");
include("session.php");

if($_SERVER["REQUEST_METHOD"] == "POST") // If the method is post continue
    {
        if (isset($_POST['password1']) && isset($_POST['password2'])) // if the 2 variables are set continue
            {
                $password1 = $_POST['password1']; // setting the variable to the old password entered
                $password2 = $_POST['password2']; // setting the variable to the new password entered
                $password3 = $_POST['password3']; // setting the variable to the new password entered
                $username = $login_session; // setting the variable to the username of the currently logged in user

                $saltReturn = mysqli_query($db,"SELECT hashedPassword FROM tester WHERE Username = '$username'"); // query to return the hashepassword and salt of the logged in user
                $row = mysqli_fetch_all($saltReturn,MYSQLI_ASSOC); // mysqli_fetch_all returns a array and assigns it to the variable
                $returned =  $row[0]['hashedPassword']; // position 0 holds the hashed password and assigns it to the variable
                $array =  explode('$', $returned ); // seperates the hashed password by the dollar sign which gives the salt and the hashed password inside a array
            
                $iterations = 1000; // the amount of iterarions you want to use in the hashing algorithm
                $hash = hash_pbkdf2("sha256", $password1, $array[1], $iterations, 32); // hashing algorithm to hash the password
                $saltyHash = '$' . $array[1] . '$' . $hash; // Concatenating the hashed password with the salt andd dollar signs eg. $Salt$HashedPassword

                if ($login1 != $saltyHash) // if the hashedpassword stored in login1 does not matche the hashedpassword stored in salthash
                    {
                        echo "Old Password is incorrect"; 
                    }
                elseif((!preg_match("#.*^(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password2))) // if old password is correct checks if entered new password is >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)
                    {
                            echo "Password not complex enough, >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)";
                            echo " eg Password14$";
                    }
                elseif($password2 === $password3)
                    {
                        $passwordBHash = $password2; // assigns the new passowrd to the variable
                        $salt = random_bytes(32); // random series of chars are generated. The size will be 32 bytes long
                        $hash = hash_pbkdf2("sha256", $passwordBHash, $salt, $iterations, 32); // hashing algorithm to hash new password
                        $saltHash = '$' . $salt . '$' . $hash; //  Concatenating the hashed password with the salt andd dollar signs eg. $Salt$HashedPassword
                        $result = mysqli_query($db,"UPDATE tester SET hashedPassword = '$saltHash' WHERE Username = '$login_session'");
                        header("location:Logout.php"); // query to update the users stored password
                    }
                else
                    {
                        echo "Passwords do not match"; 
                    }
            }
   }
?>
<html>

   <head>
      <title>Change Password</title>

      <style type = "text/css">
         body {
            font-family:Arial, Helvetica, sans-serif;
            font-size:14px;
         }

         label {
            font-weight:bold;
            width:100px;
            font-size:14px;
         }

         .box {
            border:#666666 solid 1px;
         }
      </style>

   </head>

   <body bgcolor = "#FFFFFF">

      <div align = "center">
         <div style = "width:300px; border: solid 1px #333333; " align = "left">
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Change Password</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post">
                  <label>Old Password  :</label><input type = "password" name = "password1" class = "box" /><br/><br />
                  <label>New Password  :</label><input type = "password" name = "password2" class = "box" /><br/><br />
                  <label>Re-enter New Password  :</label><input type = "password" name = "password3" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>

            </div>

         </div>

      </div>

   </body>
</html>
