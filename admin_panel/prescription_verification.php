<?php
// Get user data from query parameters
$user_id = $_GET['user_id'] ?? null;
$user_name = $_GET['user_name'] ?? null;

// Check if user_id or user_name is not provided, and redirect to login page if necessary
if (!$user_id || !$user_name) {
    header("Location: ../login.php");
    exit;
}

// Include the database connection file
include '../db_connection.php';

// Query to get the user role from the database
$query = "SELECT role FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);

// Check if the query was prepared successfully
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $role);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Check if the role is 'admin', if not redirect to login page
    if ($role !== 'admin') {
        header("Location: ../login.php");
        exit;
    }
} else {
    echo "Error querying the database.";
    exit;
}

// Handle the change status action
if (isset($_POST['change_status'])) {
    $prescription_id = $_POST['prescription_id'];
    $new_status = $_POST['new_status'];

    // Query to update the status of the prescription
    $update_query = "UPDATE prescriptions SET status = ? WHERE prescription_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $new_status, $prescription_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Status updated successfully!</p>";
        } else {
            echo "<p>Error updating the status.</p>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Query to fetch all prescriptions
$query = "SELECT prescription_id, user_id, file_path, status, uploaded_at FROM prescriptions ORDER BY user_id, uploaded_at";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    echo "Error retrieving prescriptions: " . mysqli_error($conn);
    exit;
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Verification</title>
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- Display the user information -->
    <h1>Welcome <?php echo htmlspecialchars($user_name); ?>! Your User ID is <?php echo htmlspecialchars($user_id); ?>.</h1>

    <!-- Display the prescription data -->
    <h2>All Prescriptions</h2>
    <!-- Button to go to Admin Panel with Font Awesome Icon -->
    <a href="../home_page/admin_home.php">
        <button><i class="fas fa-cogs"></i> Go to Admin Panel</button>
    </a>
    <table border="1">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Prescription ID</th>
                <th>File Path</th>
                <th>Status</th>
                <th>Uploaded At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $previous_user_id = null;
            $user_prescription_count = 0;

            // Loop through the result set and display each prescription
            while ($row = mysqli_fetch_assoc($result)) {
                // Check if the user_id is different from the previous one
                if ($previous_user_id != $row['user_id']) {
                    // Get the count of prescriptions for this user
                    $user_prescription_count = 1;
                    $user_id = htmlspecialchars($row['user_id']);

                    // Display the user_id with rowspan
                    echo "<tr>";
                    echo "<td rowspan='$user_prescription_count'>" . $user_id . "</td>";
                } else {
                    // If user_id is the same as the previous, increment the rowspan and leave the user_id cell empty
                    $user_prescription_count++;
                    echo "<tr><td></td>";
                }

                echo "<td>" . htmlspecialchars($row['prescription_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['file_path']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['uploaded_at']) . "</td>";
                echo "<td>";
                // Create a form with a dropdown and button to change the status
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="prescription_id" value="' . htmlspecialchars($row['prescription_id']) . '">';
                echo '<select name="new_status">';
                echo '<option value="pending" ' . ($row['status'] == 'pending' ? 'selected' : '') . '>Pending</option>';
                echo '<option value="delivered" ' . ($row['status'] == 'delivered' ? 'selected' : '') . '>Delivered</option>';
                echo '<option value="cancelled" ' . ($row['status'] == 'cancelled' ? 'selected' : '') . '>Cancelled</option>';
                echo '</select>';
                echo '<button type="submit" name="change_status"><i class="fas fa-sync-alt"></i> Change Status</button>';
                echo '</form>';
                echo "</td>";
                echo "</tr>";

                // Update the previous_user_id
                $previous_user_id = $row['user_id'];
            }
            ?>
        </tbody>
    </table>
</body>

</html>