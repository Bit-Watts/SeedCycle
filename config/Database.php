<?php

$host     = 'localhost';
$db_name  = 'seed_cycle';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("COnnection failed: " . mysqli_connect_errno());
    
}