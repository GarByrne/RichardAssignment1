<?php
include("config.php");
session_start();
$error = '';

if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $hashOfUser = $ip . $userAgent;
        $iterations = 1000;

        $salt = "salty";
        $hash = hash_pbkdf2("sha256", $hashOfUser, $salt, $iterations, 32);

        $result = mysqli_query($db,"SELECT COUNT(hashedUserAgentIP) AS Count FROM ip WHERE hashedUserAgentIP = '$hash' AND `timestamp` > (now() - interval 5 minute) AND inActive = True");
        $row = mysqli_fetch_all($result,MYSQLI_ASSOC);

        if($row[0]['Count'] >= 3)
            {
                echo "Your are allowed 3 attempts in 5 minutes";
            }
        else
            {
                mysqli_query($db, "INSERT INTO `ip` (`hashedUserAgentIP` ,`timestamp`) VALUES ('$hash',CURRENT_TIMESTAMP)");
                $myusername = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
                $mypassword = mysqli_real_escape_string($db,$_POST['password']);

                $salt = "SELECT hashedPassword FROM tester WHERE Username = '$myusername'";
                $saltReturn = mysqli_query($db,$salt);
                $row = mysqli_fetch_all($saltReturn,MYSQLI_ASSOC);
                    
                $arr = (array)$row;
                if (empty($arr)) 
                    {
                        $error = "Your Username($myusername) or Password is invalid";
                    }
                else
                    {
                        $returned = $row[0]['hashedPassword'];
                        $array =  explode( '$', $returned );
                        $iterations = 1000;
                        $hash = hash_pbkdf2("sha256", $mypassword, $array[1], $iterations, 32);
                        $saltyHash = '$' . $array[1] . '$' . $hash;
                        $nameResult = mysqli_query($db,"SELECT id FROM tester WHERE Username = '$myusername' and hashedPassword = '$saltyHash'");
                        $nameCount = mysqli_num_rows($nameResult);
                        if($nameCount == 1)
                            {
                                $result = mysqli_query($db,"SELECT id FROM tester WHERE Username = '$myusername' and hashedPassword = '$saltyHash'");
                                $count = mysqli_num_rows($result);
                                $query = "UPDATE ip SET inActive = False ";
                                $result = mysqli_query($db,$query);

                                if($count == 1) 
                                    {
                    			         $_SESSION['login_user'] = $myusername;
                    			         header("location: welcome.php");
                                    }
                            }
                    }
            }
    }
?>
<html>

   <head>
      <title>Login Page</title>

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
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Login</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post" autocomplete = "off">
                  <label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
                  <label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/>
                  <input type = "button" value = " Registration " onclick="window.location.href='Register.php'"/><br />
               </form>

               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>

            </div>

         </div>

      </div>

   </body>
</html>
