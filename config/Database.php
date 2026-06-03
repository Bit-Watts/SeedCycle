<?php

// Prevent browser and service worker from caching PHP responses
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
}

$host     = 'localhost';
$db_name  = 'u500694472_seedcycle';
$username = 'u500694472_seedCycle';
$password = '$eedCycle1';

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_errno());
}
