<?php
include("config.php");
include("session.php");

if($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        if (isset($_POST['password1']) && isset($_POST['password2']))
            {
                $password1 = $_POST['password1'];
                $password2 = $_POST['password2'];
                $username = $login_session;

                $saltReturn = mysqli_query($db,"SELECT hashedPassword FROM tester WHERE Username = '$username'");
                $row = mysqli_fetch_all($saltReturn,MYSQLI_ASSOC);
                $returned =  $row[0]['hashedPassword'];
                $array =  explode('$', $returned );

                $iterations = 1000;
                $hash = hash_pbkdf2("sha256", $password1, $array[1], $iterations, 32);
                $saltyHash = '$' . $array[1] . '$' . $hash;

                if ($login1 != $saltyHash)
                    {
                        echo "Password does not match";
                    }
                elseif((!preg_match("#.*^(?=.{8,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password2)))
                    {
                        echo "Password not complex enough";
                    }
                else
                    {
                        $passwordBHash = $password2;
                        $salt = random_bytes(32);
                        $hash = hash_pbkdf2("sha256", $passwordBHash, $salt, $iterations, 32);
                        $saltHash = '$' . $salt . '$' . $hash;
                        $result = mysqli_query($db,"UPDATE tester SET hashedPassword = '$saltHash' WHERE Username = '$login_session'");
                        header("location:Logout.php");
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
                  <input type = "submit" value = " Submit "/><br />
               </form>

            </div>

         </div>

      </div>

   </body>
</html>
