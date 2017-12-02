<?php
$user = 'root';
$pass = '';
$db = 'GarryB';
$db = new mysqli('localhost', $user, $pass, $db) or die("Unable to connect");
echo "you connected";
?>﻿
