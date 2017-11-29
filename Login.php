<?php
   include("config.php");
   session_start();
   $error = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {

   $ip = $_SERVER['REMOTE_ADDR'];
   mysqli_query($db, "INSERT INTO `IP` (`address` ,`timestamp`) VALUES ('$ip',CURRENT_TIMESTAMP)");
   $result = mysqli_query($db, "SELECT COUNT(*) FROM `IP` WHERE `address` LIKE '$ip' AND `timestamp` > (now() - interval 5 minute) AND inActive = 'True'");
   $count = mysqli_fetch_array($result, MYSQLI_NUM);
   echo $count[0];
   if($count[0] > 3){
  echo "Your are allowed 3 attempts in 10 minutes";
}
else{

      // username and password sent from form 
      
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
      $iterations = 1000;

      $nameResult = mysqli_query($db,"SELECT id FROM Tester WHERE Username = '$myusername'");      
      $nameCount = mysqli_num_rows($nameResult);
      
      if($nameCount == 1)
{

      $updateInactive = "UPDATE IP SET inActive = 'False' WHERE address = '$ip'";
      $result = mysqli_query($db,$updateInactive);


      // Generate a random IV using openssl_random_pseudo_bytes()
      // random_bytes() or another suitable source of randomness
      $salt = "SELECT Salt FROM Tester WHERE Username = '$myusername'";
      $saltReturn = mysqli_query($db,$salt);
      $row = mysqli_fetch_all($saltReturn,MYSQLI_ASSOC);

      $returned =  $row[0]['Salt'];

      $hash = hash_pbkdf2("sha256", $mypassword, $returned, $iterations, 20);

      $sql = "SELECT id FROM Tester WHERE Username = '$myusername' and hashedPassword = '$hash'";
      $result = mysqli_query($db,$sql);

      
      $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($count == 1) {
         
         $_SESSION['login_user'] = $myusername;
         
         header("location: welcome.php");
      }else {
         $error = "Your Login Name or Password is invalid";
      }
}
	   else {
         $error = "Your Login Name or Password is invalid";
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
               
               <form action = "" method = "post">
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
