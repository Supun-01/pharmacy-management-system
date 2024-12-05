<?php
// Include database connection file
include_once("../db_connection.php");

// Get user_id from the URL
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : '';

// If user_id is not set, redirect to login page
if ($user_id === 0) {
    header("Location: ../login.php");
    exit;
}

// Query to check user role
$query = "SELECT role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// If user role is not admin, redirect to login page
if ($role !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// If user is admin, continue with the lab test management
// Query to fetch all lab tests
$query = "SELECT lt.test_id, lt.user_id, lt.test_type, lt.appointment_date, lt.appointment_time, lt.status, lt.booked_at, u.name
          FROM lab_tests lt
          INNER JOIN users u ON lt.user_id = u.user_id
          ORDER BY lt.user_id, lt.appointment_date, lt.appointment_time";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$tests = [];
while ($row = $result->fetch_assoc()) {
    $tests[] = $row;
}
$stmt->close();

// Handle the form submission to update the test status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $test_id = (int)$_POST['test_id'];
    $new_status = $_POST['status'];

    // Update the status in the database
    $update_query = "UPDATE lab_tests SET status = ? WHERE test_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_status, $test_id);
    if ($update_stmt->execute()) {
        // Successfully updated
        header("Location: lab_test_management.php?user_id=" . $user_id . "&user_name=" . $user_name);
        exit;
    } else {
        // Error updating
        echo "Error updating the test status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Test Management</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../style/admin_panel/lab_test_management.css">
</head>

<body>
    <div class="container">
        <h1 class="page-title">Lab Test Management</h1>

        <a href="../home_page/admin_home.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
            <button class="btn admin-btn"><i class="fas fa-cogs"></i> Go to Admin Panel</button>
        </a>

        <!-- Table to show all lab tests -->
        <table class="lab-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Test ID</th>
                    <th>Test Type</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                    <th>Booked At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $previous_user_id = null;
                $user_rowspan = 0;

                foreach ($tests as $test) {
                    if ($test['user_id'] !== $previous_user_id) {
                        if ($previous_user_id !== null) {
                            echo "</tr>";
                        }
                        $user_rowspan = count(array_filter($tests, function ($t) use ($test) {
                            return $t['user_id'] == $test['user_id'];
                        }));
                        echo "<tr>";
                        echo "<td rowspan='{$user_rowspan}'>" . htmlspecialchars($test['user_id']) . "</td>";
                        echo "<td rowspan='{$user_rowspan}'>" . htmlspecialchars($test['name']) . "</td>";
                        $previous_user_id = $test['user_id'];
                    }

                    echo "<td>" . htmlspecialchars($test['test_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($test['test_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($test['appointment_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($test['appointment_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($test['status']) . "</td>";
                    echo "<td>" . htmlspecialchars($test['booked_at']) . "</td>";

                    echo "<td>
                        <form method='POST' action='' class='status-form'>
                            <input type='hidden' name='test_id' value='" . htmlspecialchars($test['test_id']) . "'>
                            <select name='status' class='status-select'>
                                <option value='scheduled' " . ($test['status'] == 'scheduled' ? 'selected' : '') . ">Scheduled</option>
                                <option value='completed' " . ($test['status'] == 'completed' ? 'selected' : '') . ">Completed</option>
                            </select>
                            <button type='submit' name='update_status' class='btn update-btn'>
                                <i class='fas fa-sync-alt'></i> Update Status
                            </button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>