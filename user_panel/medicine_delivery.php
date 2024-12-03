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
    <link rel="stylesheet" href="../style/medicine_delivery.css"> <!-- Link to external CSS -->
</head>

<body>
    <button onclick="window.location.href='../home_page/user_home.php';">Back to User Home</button>

    <h1>Welcome <?php echo htmlspecialchars($user_name); ?>! Your User ID is <?php echo htmlspecialchars($user_id); ?>.</h1>

    <h2>Your Orders</h2>
    <table border="1">
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
                    <td class="<?php echo htmlspecialchars($order['status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                    </td>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>