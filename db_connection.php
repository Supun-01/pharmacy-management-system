<?php
// Database configuration
$host = 'localhost'; // XAMPP default host
$username = 'root';  // XAMPP default username
$password = '';      // XAMPP default password (empty by default)
$dbname = 'pharmacy'; // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } else {
//     echo "Database connected successfully!";
// }
