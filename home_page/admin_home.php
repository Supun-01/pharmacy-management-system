<?php
// Start the session
session_start();

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to the login page if not authorized
    header("Location: ../login.php");
    exit;
}

// Retrieve user data from the session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Handle logout request
if (isset($_POST['logout'])) {
    // Destroy the session and redirect to the login page
    session_destroy();
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="../style/admin_home.css">

    <!-- background image -->
    <style>
        body {
            background-image: url("../assets/admin.jpg");
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <p>Welcome, <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
        <p>Your Admin ID is: <strong><?php echo htmlspecialchars($user_id); ?></strong></p>

        <div class="admin-buttons">
            <!-- Lab Test Management -->
            <a href="../admin_panel/lab_test_management.php?user_id=<?php echo urlencode($user_id); ?>&user_name=<?php echo urlencode($user_name); ?>">
                <button>
                    <i class="fas fa-flask"></i> Lab Test Management
                </button>
            </a>

            <!-- Medicine Inventory Management -->
            <a href="../admin_panel/medicine_inventory_management.php?user_id=<?php echo urlencode($user_id); ?>&user_name=<?php echo urlencode($user_name); ?>">
                <button>
                    <i class="fas fa-pills"></i> Medicine Inventory
                </button>
            </a>

            <!-- Orders Management -->
            <a href="../admin_panel/order_management.php?user_id=<?php echo urlencode($user_id); ?>&user_name=<?php echo urlencode($user_name); ?>">
                <button>
                    <i class="fas fa-shopping-cart"></i> Manage Orders
                </button>
            </a>

            <!-- Prescription Verification -->
            <a href="../admin_panel/prescription_verification.php?user_id=<?php echo urlencode($user_id); ?>&user_name=<?php echo urlencode($user_name); ?>">
                <button>
                    <i class="fas fa-prescription"></i> Prescription Verification
                </button>
            </a>

            <!-- Reporting and Analytics -->
            <!-- <a href="../admin_panel/reporting_and_analytics.php?user_id=<?php echo urlencode($user_id); ?>&user_name=<?php echo urlencode($user_name); ?>">
                <button>
                    <i class="fas fa-chart-line"></i> Reporting & Analytics
                </button>
            </a> -->

            <!-- User Management -->
            <a href="../admin_panel/user_management.php?user_id=<?php echo urlencode($user_id); ?>&user_name=<?php echo urlencode($user_name); ?>">
                <button>
                    <i class="fas fa-users"></i> User Management
                </button>
            </a>
        </div>

        <!-- Logout button -->
        <form method="post" action="" class="logout-form">
            <button type="submit" name="logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</body>

</html>