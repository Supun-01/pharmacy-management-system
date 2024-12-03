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
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Link to external CSS (optional) -->
    <!-- <link rel="stylesheet" href="style.css"> -->
</head>

<body>
    <h1>Welcome to the User Dashboard</h1>
    <p>Your User ID is: <?php echo htmlspecialchars($user_id); ?></p>
    <p>Your Name is: <?php echo htmlspecialchars($user_name); ?></p>

    <!-- Navbar with links to different pages -->
    <nav>
        <ul>
            <li>
                <a href="../user_panel/prescription_upload.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
                    <i class="fas fa-upload"></i> Upload Prescription
                </a>
            </li>
            <li>
                <a href="../user_panel/medicines_list.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
                    <i class="fas fa-pills"></i> Medicines Store
                </a>
            </li>
            <li>
                <a href="../user_panel/lab_test_booking.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
                    <i class="fas fa-flask"></i> Book Lab Test
                </a>
            </li>
            <li>
                <a href="../user_panel/medicine_delivery.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
                    <i class="fas fa-truck"></i> Track Delivery Status
                </a>
            </li>
        </ul>
    </nav>


    <!-- Logout button with icon -->
    <form method="post" action="">
        <button type="submit" name="logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </form>
</body>

</html>