<?php
   include('config.php');
   session_start();

   $countLog = 0;

   $user_check = $_SESSION['login_user'];

   $ses_sql = mysqli_query($db,"select Username from tester where Username = '$user_check' ");
   $ses1 = mysqli_query($db,"select hashedPassword from tester where Username = '$user_check' ");

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   $col = mysqli_fetch_array($ses1,MYSQLI_ASSOC);

   $login_session = $row['Username'];
   $login1 = $col['hashedPassword'];

   if(!isset($_SESSION['login_user'])){
      header("location:Login.php");
   }
?>