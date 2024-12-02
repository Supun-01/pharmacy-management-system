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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <h1>Admin Panel</h1>

    <div>
        <!-- Lab Test Management -->
        <a href="lab_test_management.php">
            <button>
                <i class="fas fa-flask"></i> Lab Test Management
            </button>
        </a><br><br>

        <!-- Medicine Inventory Management -->
        <a href="medicine_inventory_management.php">
            <button>
                <i class="fas fa-pills"></i> Medicine Inventory
            </button>
        </a><br><br>

        <!-- Prescription Verification -->
        <a href="prescription_verification.php">
            <button>
                <i class="fas fa-prescription"></i> Prescription Verification
            </button>
        </a><br><br>

        <!-- Reporting and Analytics -->
        <a href="reporting_and_analytics.php">
            <button>
                <i class="fas fa-chart-line"></i> Reporting & Analytics
            </button>
        </a><br><br>

        <!-- User Management -->
        <a href="user_management.php">
            <button>
                <i class="fas fa-users"></i> User Management
            </button>
        </a><br><br>
    </div>

</body>

</html>