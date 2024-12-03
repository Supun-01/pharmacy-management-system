<?php
// Get user data from query parameters
$user_id = $_GET['user_id'] ?? null;
$user_name = $_GET['user_name'] ?? null;

// Check if user is logged in and has valid parameters
if (!$user_id || !$user_name) {
    // Redirect to login page if user_id or user_name are not provided
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicines List</title>
</head>

<body>
    <button onclick="window.location.href='../home_page/user_home.php';">Back to User Home</button>

    <h1>medicines_list.php</h1>
    <!-- Display user information -->
    <h1>Welcome <?php echo htmlspecialchars($user_name); ?>! Your User ID is <?php echo htmlspecialchars($user_id); ?>.</h1>
</body>

</html>