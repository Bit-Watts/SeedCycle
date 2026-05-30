<?php

$host     = 'localhost';
$db_name  = 'u500694472_seedcycle';
$username = 'u500694472_seedCycle'; // Capital C for username
$password = '$eedCycle1';

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_errno());
}
