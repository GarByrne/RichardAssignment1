<?php
include("config.php");

header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');

if($_SERVER["REQUEST_METHOD"] == "POST") // If the method is post continue
    {
        if (isset($_POST['username']) && isset($_POST['password']))
        {
            $username = $_POST['username']; // setting the variable to the entered username
            $password = $_POST['password']; // setting the variable to the entered password
            
            $ip = $_SERVER['REMOTE_ADDR']; // gets the ip address of the user and assigns it to a variable
            $userAgent = $_SERVER['HTTP_USER_AGENT']; // gets the user agent details of the user and assigns it to a variable

            $hashOfUser = $ip . $userAgent; // joins the ip and user agent and assigns it to a vatiable
            $iterations = 1000; // number of iterations to use in the hashing algorithm

            $salt = "salty"; // salt for the hashing of the ip+useragent. Using the same salt as it does not matter if it is bo=roken
            $hash = hash_pbkdf2("sha256", $hashOfUser, $salt, $iterations, 32); // hashes the ip+useragent and assigns it to a variable

            $result = mysqli_query($db,"SELECT COUNT(hashedUserAgentIP) AS Count FROM ip WHERE hashedUserAgentIP = '$hash' AND `timestamp` > (now() - interval 5 minute) AND inActiveReg = True"); // query to check the database for matching useragent+ip and couunts them if they are still active
            $row = mysqli_fetch_all($result,MYSQLI_ASSOC);

            if($row[0]['Count'] >= 3) // if the value returned by the query is 3 or more.
                {
                    echo "Your are allowed to create 3 Users in 5 minutes";
                }
            else
                {
                    mysqli_query($db, "INSERT INTO `ip` (`hashedUserAgentIP` ,`timestamp`, `inActive`) VALUES ('$hash',CURRENT_TIMESTAMP, 'True')"); // query to insert the hashed useragent+ip into the database
                
                    if (isset($_POST['username']) && isset($_POST['password'])) // if the 2 variables are set continue
                    {
                        $username = $_POST['username']; // setting the variable to the entered username
                        $password = $_POST['password']; // setting the variable to the entered password
                        
                        if (mysqli_num_rows(mysqli_query($db,"SELECT Username FROM tester WHERE Username='$username'")) != 0) // query to check if the username already exists
                        {
                            echo "Username already exists";
                        }
                        elseif((!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password))) // query to check if the password is strong enough. >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)
                        {
                            echo "Password not complex enough, >= 8 and <= 20 chars, contains a-z A-Z 0-9 and special chars(£,$ and % etc)";
                            echo "eg Password14$";
                        }
                        else
                        {
                            $iterations = 1000; // the amount of iterarions you want to use in the hashing algorithm
                            $salt = random_bytes(32); //hashing algorithm to hash the password
                            $hash = hash_pbkdf2("sha256", $password, $salt, $iterations, 32); // hashing algorithm to hash new password
                            $saltHash = '$' . $salt . '$' . $hash; //  Concatenating the hashed password with the salt andd dollar signs eg. $Salt$HashedPassword
                            $result = mysqli_query($db,"INSERT INTO tester (Username, hashedPassword) VALUES  ('$username', '$saltHash')");
                            header("location:Login.php"); // query to insert the newly created user into the database
                        }
                    }
                }
        }  
    } 
?>
<html>

   <head>
      <title>Register User</title>

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
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Register</b></div>

            <div style = "margin:30px">

               <form action = "" method = "post" autocomplete = "off">
                  <label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
                  <label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>

            </div>

         </div>

      </div>

   </body>
</html>
