<?php
include("config.php");
session_start(); // starts a session
$error = ''; // variable to hold error message 

if($_SERVER["REQUEST_METHOD"] == "POST") // If the method is post continue
    {
        $ip = $_SERVER['REMOTE_ADDR']; // gets the ip address of the user and assigns it to a variable
        $userAgent = $_SERVER['HTTP_USER_AGENT']; // gets the user agent details of the user and assigns it to a variable

        $hashOfUser = $ip . $userAgent; // joins the ip and user agent and assigns it to a vatiable
        $iterations = 1000; // number of iterations to use in the hashing algorithm

        $salt = "salty"; // salt for the hashing of the ip+useragent. Using the same salt as it does not matter if it is bo=roken
        $hash = hash_pbkdf2("sha256", $hashOfUser, $salt, $iterations, 32); // hashes the ip+useragent and assigns it to a variable

        $result = mysqli_query($db,"SELECT COUNT(hashedUserAgentIP) AS Count FROM ip WHERE hashedUserAgentIP = '$hash' AND `timestamp` > (now() - interval 5 minute) AND inActive = True"); // query to check the database for matching useragent+ip and couunts them if they are still active
        $row = mysqli_fetch_all($result,MYSQLI_ASSOC);

        if($row[0]['Count'] >= 3) // if the value returned by the query is 3 or more.
            {
                echo "Your are allowed 3 attempts in 5 minutes";
            }
        else
            {
                mysqli_query($db, "INSERT INTO `ip` (`hashedUserAgentIP` ,`timestamp`) VALUES ('$hash',CURRENT_TIMESTAMP)"); // query to insert the hashed useragent+ip into the database
                $myusername = filter_var($_POST['username'],FILTER_SANITIZE_STRING); // takes the entered username and Strip tags thenand assigns it to a variable
                $mypassword = mysqli_real_escape_string($db,$_POST['password']); //filters out special characters in a string and assigns to a variable

                $salt = "SELECT hashedPassword FROM tester WHERE Username = '$myusername'"; // query to return the hashepassword and salt of the username entered
                $saltReturn = mysqli_query($db,$salt); // run the query
                $row = mysqli_fetch_all($saltReturn,MYSQLI_ASSOC); // mysqli_fetch_all returns a array and assigns it to the variable
                    
                $arr = (array)$row; // turns the variable into an array and assigns it to a variable
                if (empty($arr)) // checks if the array is empty
                    {
                        $error = "Your Username($myusername) or Password is invalid";
                    }
                else // if the array is not empty
                    {
                        $returned = $row[0]['hashedPassword']; // position 0 holds the hashed password and assigns it to the variable
                        $array =  explode( '$', $returned ); // seperates the hashed password by the dollar sign which gives the salt and the hashed password inside a array
                    
                        $iterations = 1000; // the amount of iterarions you want to use in the hashing algorithm
                        $hash = hash_pbkdf2("sha256", $mypassword, $array[1], $iterations, 32); // hashing algorithm to hash the password
                        $saltyHash = '$' . $array[1] . '$' . $hash; // Concatenating the hashed password with the salt andd dollar signs eg. $Salt$HashedPassword
                        $nameResult = mysqli_query($db,"SELECT id FROM tester WHERE Username = '$myusername' and hashedPassword = '$saltyHash'");// query to select the username with the matching username and hashedpassword
                        $nameCount = mysqli_num_rows($nameResult);
                        if($nameCount == 1)
                            {
                                $result = mysqli_query($db,"SELECT id FROM tester WHERE Username = '$myusername' and hashedPassword = '$saltyHash'"); // query to select the username with the matching username and hashedpassword
                                $count = mysqli_num_rows($result);// counts the number of rows in the variable result
                                $query = "UPDATE ip SET inActive = False "; // query to set the inActive flag to false on successful login 
                                $result = mysqli_query($db,$query); // runs the query

                                if($count == 1) 
                                    {
                    			         $_SESSION['login_user'] = $myusername;
                    			         header("location: welcome.php"); // goes to the welcome page
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
