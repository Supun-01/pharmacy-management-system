<?php
// Start the session
session_start();

// Check if the user is logged in and has the user role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Redirect to the login page if not authorized
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Home</title>
</head>

<body>
    <h1>Hello User</h1>
    <p>Welcome to your User Dashboard.</p>
    <a href="logout.php">Logout</a>
</body>

</html>