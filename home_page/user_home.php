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
    <link rel="stylesheet" href="../style/user_home.css">
</head>

<body>
    <!-- Navbar with links to different pages -->
    <nav class="navbar">
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
            <!-- Logout Button in Navbar -->
            <li class="logout-item">
                <form method="post" action="" class="logout-form">
                    <button type="submit" name="logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h1>Welcome! <?php echo htmlspecialchars($user_name); ?>.</h1>
            <p>Text refers to the actual words written in a book, newspaper, blog post, or any other written work. Pictures, charts, and other images are not text. When you read something, you are looking<?php echo htmlspecialchars($user_id); ?></p>
            <p> at text and using your language skills to get meaning out of it. Something that doesn't contain any text is textless.</p>
        </div>
        <div class="hero-image">
            <img src="../assets/01.jpg" alt="Hero Image" />
        </div>
    </div>
</body>

</html>