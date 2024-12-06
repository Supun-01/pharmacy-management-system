<?php
// Include the database connection file
include_once("../db_connection.php");

// Start session
session_start();

// Get user ID and username from the URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : null;

// Redirect to login page if user_id or user_name is missing
if (!$user_id || !$user_name) {
    header("Location: ../login.php");
    exit;
}

// Fetch user information from the database
$sql = "SELECT * FROM `users` WHERE `user_id` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Check if the user role is 'user'
    if ($user['role'] !== 'user') {
        // Redirect to login page if the user is not a regular user
        header("Location: ../login.php");
        exit;
    }
} else {
    // Redirect to login page if user not found
    header("Location: ../login.php");
    exit;
}

// Handle form submission for booking a lab test
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_type = isset($_POST['test_type']) ? trim($_POST['test_type']) : null;
    $appointment_date = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : null;
    $appointment_time = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : null;

    // Validate inputs
    if ($test_type && $appointment_date && $appointment_time) {
        // Check if the selected time slot is already booked
        $check_sql = "SELECT * FROM `lab_tests` WHERE `appointment_date` = ? AND `appointment_time` = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $appointment_date, $appointment_time);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<p style='color: red;'>The selected time slot is already booked. Please choose another time.</p>";
        } else {
            // Insert the new lab test booking
            $insert_sql = "INSERT INTO `lab_tests` (`user_id`, `test_type`, `appointment_date`, `appointment_time`) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("isss", $user_id, $test_type, $appointment_date, $appointment_time);
            if ($insert_stmt->execute()) {
                echo "<p style='color: green;'>Lab test booked successfully!</p>";
            } else {
                echo "<p style='color: red;'>Error booking the lab test. Please try again.</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>All fields are required!</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Test Booking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/user_panel/lab_test_booking.css"> <!-- Link to external CSS file -->

    <style>
        body {
            background-image: url("../assets/lab_test_booking.jpg");
            background-repeat: no-repeat;
            /* Prevents the image from repeating */
            background-size: cover;
            /* Ensures the image covers the entire background */
            background-position: center;
            /* Centers the image */
            background-attachment: fixed;
            /* Keeps the image fixed as the user scrolls */
            margin: 0;
            /* Removes default body margin */
            height: 100vh;
            /* Ensures body takes the full height of the viewport */
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Back to User Home Button -->
        <button class="back-btn" onclick="window.location.href='../home_page/user_home.php';">
            <i class="fas fa-home"></i> Back to User Home
        </button>

        <h1 class="page-title">Welcome to Lab Test Booking</h1>
        <p class="user-info">User Name: <?php echo htmlspecialchars($user_name); ?></p>

        <!-- Lab Test Booking Form -->
        <form class="booking-form" method="post">
            <label for="test_type">Test Type:</label>
            <select name="test_type" id="test_type" required>
                <option value="">-- Select a Test Type --</option>
                <option value="Blood Test">Blood Test</option>
                <option value="X-Ray">X-Ray</option>
                <option value="MRI">MRI</option>
                <option value="CT Scan">CT Scan</option>
                <option value="Ultrasound">Ultrasound</option>
            </select>

            <label for="appointment_date">Appointment Date:</label>
            <input type="date" name="appointment_date" id="appointment_date" required>

            <label for="appointment_time">Appointment Time:</label>
            <select name="appointment_time" id="appointment_time" required>
                <?php
                for ($hour = 9; $hour <= 21; $hour++) {
                    for ($minute = 0; $minute < 60; $minute += 30) {
                        $time = sprintf("%02d:%02d:00", $hour, $minute);
                        $display_time = date("h:i A", strtotime($time));
                        echo "<option value=\"$time\">$display_time</option>";
                    }
                }
                ?>
            </select>

            <button type="submit" class="submit-btn">Book Lab Test</button>
        </form>

        <h2>Your Lab Test Bookings</h2>
        <table class="booking-table">
            <thead>
                <tr>
                    <th>Test ID</th>
                    <th>Test Type</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Booked At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch all lab test bookings for the current user
                $fetch_tests_sql = "SELECT `test_id`, `test_type`, `appointment_date`, `appointment_time`, `booked_at`, `status` FROM `lab_tests` WHERE `user_id` = ? ORDER BY `appointment_date` ASC, `appointment_time` ASC";
                $fetch_tests_stmt = $conn->prepare($fetch_tests_sql);
                $fetch_tests_stmt->bind_param("i", $user_id);
                $fetch_tests_stmt->execute();
                $tests_result = $fetch_tests_stmt->get_result();

                if ($tests_result->num_rows > 0) {
                    while ($row = $tests_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['test_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['test_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['appointment_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['booked_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No lab test bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>