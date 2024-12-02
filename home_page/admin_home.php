<?php
// Start the session
session_start();

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the login page if not authorized
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Home</title>
</head>

<body>
    <h1>Hello Admin</h1>
    <p>Welcome to the Admin Dashboard.</p>
    <a href="logout.php">Logout</a>
</body>

</html>