<?php

// Prevent browser and service worker from caching PHP responses
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
}

// Global asset() helper — appends cache-busting version to asset URLs
if (!function_exists('asset')) {
    function asset(string $path): string {
        $fsPath     = ltrim(preg_replace('#^\.\./+#', '', $path), '/');
        $publicRoot = dirname(__DIR__) . '/public/';
        $absPath    = $publicRoot . $fsPath;
        $version    = file_exists($absPath) ? filemtime($absPath) : time();
        return $path . '?v=' . $version;
    }
}

$host     = 'localhost';
$db_name  = 'u500694472_seedcycle';
$username = 'u500694472_seedCycle';
$password = '$eedCycle1';

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_errno());
}
