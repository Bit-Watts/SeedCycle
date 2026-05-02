<?php

// Copy this file to Database.php and update with your credentials
$host     = 'localhost';
$db_name  = 'seed cycle';   // Your database name
$username = 'root';          // Your MySQL username
$password = '';              // Your MySQL password

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_errno());
}
