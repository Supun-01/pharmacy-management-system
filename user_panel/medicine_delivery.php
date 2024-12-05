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

// Include database connection
include '../db_connection.php';

// Query to fetch orders for the user
$query = "SELECT * FROM `orders` WHERE `user_id` = $user_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Delivery</title>
    <link rel="stylesheet" href="../style/user_panel/medicine_delivery.css"> <!-- Link to external CSS -->
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Back to User Home Button -->
        <button class="back-btn" onclick="window.location.href='../home_page/user_home.php';">
            <i class="fas fa-home"></i> Back to User Home
        </button>

        <h1 class="page-title">Welcome <?php echo htmlspecialchars($user_name); ?>! Your User ID is <?php echo htmlspecialchars($user_id); ?>.</h1>

        <h2>Your Orders</h2>

        <!-- Orders Table -->
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Medicine ID</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['medicine_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td class="status <?php echo htmlspecialchars($order['status']); ?>">
                            <?php
                            $status = ucfirst(htmlspecialchars($order['status']));
                            echo $status;
                            // Adding a check for the status to show an appropriate icon
                            if ($status === 'Delivered') {
                                echo ' <i class="fas fa-check-circle"></i>';
                            } elseif ($status === 'Pending') {
                                echo ' <i class="fas fa-hourglass-half"></i>';
                            } elseif ($status === 'Cancelled') {
                                echo ' <i class="fas fa-times-circle"></i>';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>