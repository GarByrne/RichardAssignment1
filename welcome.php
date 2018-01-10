

<?php
   include("session.php");
// page with links to Password change and to logout
?>
<html>
   
   <head>
      <title>Welcome </title>
   </head>
   
   <body>
      <h1>Welcome <?php echo $login_session; ?></h1>
      <h2><a href = "Page2.php">Password Change</a></h2>
      <h2><a href = "Logout.php">Sign Out</a></h2>
   </body>
   
</html>
