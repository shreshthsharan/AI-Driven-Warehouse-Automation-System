<?php
// Database connection settings
$host = 'localhost';
$user = 'root';
$password = 'asdf1998'; // Replace with your actual MySQL root password
$database = 'warehouse_inv';

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully!";
?>
