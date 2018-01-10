<?php
//Page to logout. The session is also destroyed
   include("session.php");
   
   if(session_destroy()) {
      header("Location: Login.php");
   }
?>
