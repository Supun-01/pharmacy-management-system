<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Redirect to login page if not authenticated or not a user
    header("Location: ../login.php");
    exit;
}

// Handle logout request
if (isset($_POST['logout'])) {
    // Destroy the session and redirect to login page
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Retrieve user data from the session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
</head>

<body>
    <h1>Welcome to the User Dashboard</h1>
    <p>Your User ID is: <?php echo htmlspecialchars($user_id); ?></p>
    <p>Your Name is: <?php echo htmlspecialchars($user_name); ?></p>

    <!-- Logout button -->
    <form method="post" action="">
        <button type="submit" name="logout">Logout</button>
    </form>
</body>

</html>