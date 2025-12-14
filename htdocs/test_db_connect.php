<?php
// Small CLI/web-friendly DB connectivity test used for debugging
// Usage: php test_db_connect.php
ini_set('display_errors', 0);
error_reporting(0);
require_once 'config.php';

$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    echo 'CONNECT_ERROR: ' . $mysqli->connect_error;
    exit(1);
}

echo 'CONNECT_OK';
$mysqli->close();
?>
