<?php
// Include the database connection
include_once '../db_connection.php';

// Get data from the POST request
$user_id = $_POST['user_id'];
$medicine_id = $_POST['medicine_id'];
$quantity = $_POST['quantity'] ?? 1;

// Check if the data is valid
if (!$user_id || !$medicine_id || !$quantity) {
    echo "Invalid data.";
    exit;
}

// Insert the order into the orders table
$sql = "INSERT INTO orders (user_id, medicine_id, quantity, status) 
        VALUES ('$user_id', '$medicine_id', '$quantity', 'pending')";

if ($conn->query($sql) === TRUE) {
    echo "Order successfully placed!";
} else {
    echo "Error: " . $conn->error;
}
