<?php


header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
   include('config.php');
   session_start();// Start new session

   $user_check = $_SESSION['login_user']; // Assigns the logged in users username to the variable %user_check

   $ses_sql = mysqli_query($db,"select Username from tester where Username = '$user_check' "); // query to return a username
   $ses1 = mysqli_query($db,"select hashedPassword from tester where Username = '$user_check' "); // query to return a hashed password

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC); // runs the query and assigns the returned data to the vatiable
   $col = mysqli_fetch_array($ses1,MYSQLI_ASSOC); // runs the query and assigns the returned data to the vatiable

   $login_session = $row['Username']; // assigns the username stored in the object row to the variable
   $login1 = $col['hashedPassword']; // assigns the username stored in the object col to the variable

   if(!isset($_SESSION['login_user'])){
      header("location:Login.php"); // Send to Login
   }
?>
