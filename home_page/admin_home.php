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
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Admin Panel Container */
        .container {
            text-align: center;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #333;
        }

        /* Button Styling */
        .admin-button {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            font-size: 1.1em;
            padding: 15px 0;
            /* Same padding for all buttons */
            margin: 15px 0;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            width: 100%;
            /* Make buttons full-width */
            max-width: 300px;
            /* Limit max width */
            text-align: center;
            transition: all 0.3s ease;
        }

        .admin-button i {
            margin-right: 10px;
        }

        /* Hover Effects */
        .admin-button:hover {
            background-color: #0056b3;
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .admin-button:active {
            transform: translateY(2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 30px;
                width: 100%;
            }

            .admin-button {
                width: 100%;
                /* Full width for mobile devices */
                max-width: none;
                /* Remove max-width on smaller screens */
                font-size: 1em;
                padding: 12px 20px;
                margin: 10px 0;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Admin Panel</h1>

        <!-- Lab Test Management -->
        <a href="../admin_panel/lab_test_management.php" class="admin-button">
            <i class="fas fa-flask"></i> Lab Test Management
        </a>

        <!-- Medicine Inventory Management -->
        <a href="../admin_panel/medicine_inventory_management.php" class="admin-button">
            <i class="fas fa-pills"></i> Medicine Inventory
        </a>

        <!-- Prescription Verification -->
        <a href="../admin_panel/prescription_verification.php" class="admin-button">
            <i class="fas fa-prescription"></i> Prescription Verification
        </a>

        <!-- Reporting and Analytics -->
        <a href="../admin_panel/reporting_and_analytics.php" class="admin-button">
            <i class="fas fa-chart-line"></i> Reporting & Analytics
        </a>

        <!-- User Management -->
        <a href="../admin_panel/user_management.php" class="admin-button">
            <i class="fas fa-users"></i> User Management
        </a>

    </div>

</body>

</html>