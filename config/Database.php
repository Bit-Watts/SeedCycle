<?php

$host     = 'localhost';
$db_name  = 'seed cycle'; // your local database name
$username = 'root';      // default XAMPP username
$password = '';           // default XAMPP password (empty)

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_errno());
}
