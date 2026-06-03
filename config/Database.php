<?php

// Prevent browser and service worker from caching PHP responses
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
}

$host     = 'localhost';
$db_name  = 'seed cycle'; // your local database name
$username = 'root';      // default XAMPP username
$password = '';           // default XAMPP password (empty)

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_errno());
}
