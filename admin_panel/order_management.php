<?php
// Include the database connection file
include '../db_connection.php';

// Start the session to check for logged-in users
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit;
}

// Get user ID from the session
$user_id = $_SESSION['user_id'];

// Check if the logged-in user is an admin
$query = "SELECT role FROM `users` WHERE `user_id` = $user_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);
if ($user['role'] !== 'admin') {
    // Redirect to login page if the user is not an admin
    header("Location: ../login.php");
    exit;
}

// Query to fetch orders along with medicine names and medicine IDs, grouped by user_id
$query = "
    SELECT 
        o.user_id,
        o.order_id,
        o.medicine_id,
        m.name AS medicine_name,
        o.quantity,
        o.status,
        o.order_date
    FROM `orders` o
    JOIN `medicines` m ON o.medicine_id = m.medicine_id
    ORDER BY o.user_id, o.order_id
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Process results to group orders by user_id
$orders_by_user = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders_by_user[$row['user_id']][] = $row;
}

// Handle status update based on button action
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update query based on the new status
    $update_query = "UPDATE `orders` SET status = '$new_status' WHERE order_id = $order_id";
    if (mysqli_query($conn, $update_query)) {
        // Redirect to refresh the page and show updated status
        header("Location: order_management.php");
        exit; // Ensure that no further code is executed after redirection
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Management</title>
</head>

<body>
    <h1>Order Management</h1>
    <!-- Button to go to Admin Panel -->
    <a href="../home_page/admin_home.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user['role']); ?>">
        <button>Go to Admin Panel</button>
    </a>
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Order ID</th>
                <th>Medicine ID</th>
                <th>Medicine Name</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders_by_user as $user_id => $orders): ?>
                <?php foreach ($orders as $index => $order): ?>
                    <tr>
                        <!-- Only show the user_id for the first row of this user's orders -->
                        <?php if ($index === 0): ?>
                            <td rowspan="<?php echo count($orders); ?>">
                                <?php echo htmlspecialchars($user_id); ?>
                            </td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['medicine_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['medicine_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td>
                            <!-- Action buttons to change the order status -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" name="update_status">Pending</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" name="update_status">Reject</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" name="update_status">Confirmed</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>