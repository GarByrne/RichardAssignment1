<?php
$user = 'root';
$pass = '';
$db = 'garryb';
$db = new mysqli('localhost', $user, $pass, $db) or die("Unable to connect");

//$sql = "CREATE DATABASE myDBGar";
//if ($db->query($sql) === TRUE) {
//    echo "Database created successfully";
//} else {
//    echo "Error creating database: " . $db->error;
//}
//
//
//$db2 = new mysqli('localhost', $user, $pass, $db)
//$sql1 = "CREATE TABLE 'ip' (
//'id' INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, 
//hashed_user_agent_Ip varchar(200) CHARACTER SET utf8 NOT NULL,
//time_stamp  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
//active tinyint(1) NOT NULL DEFAULT '1' ) ENGINE=InnoDB DEFAULT CHARSET=latin1
//)";
//
//if ($db->query($sql1) === TRUE) {
//    echo "Table MyGuests created successfully";
//} else {
//    echo "Error creating table: " . $db->error;
//}

?>﻿
