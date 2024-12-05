<?php
// Include the database connection file
require_once("../db_connection.php");

// Start session
session_start();

// Get user ID and name from the URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : null;

// Redirect to login if user_id or user_name is missing
if (!$user_id || !$user_name) {
    header("Location: ../login.php");
    exit();
}

// Query to check the user's role
$sql = "SELECT role FROM users WHERE user_id = ? AND name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $user_name);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists and is an admin
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if ($user['role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}

// Process form submission to add new users or admins
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        // Add new user logic
        $new_name = $_POST['name'];
        $new_email = $_POST['email'];
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];  // Get the confirm password

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            $message = "Passwords do not match!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Hash password

            $new_role = $_POST['role'];

            // Insert the new user into the database
            $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $new_name, $new_email, $hashed_password, $new_role);

            if ($stmt->execute()) {
                $message = "User added successfully!";
            } else {
                $message = "Error adding user: " . $conn->error;
            }
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user logic
        $delete_user_id = $_POST['delete_user_id'];

        // SQL to delete the user
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_user_id);

        if ($stmt->execute()) {
            $message = "User deleted successfully!";
        } else {
            $message = "Error deleting user: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="../style/admin_panel/user_management.css">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="page-title">User Management</h1>

        <!-- Admin Panel Button -->
        <a href="../home_page/admin_home.php">
            <button class="btn admin-btn"><i class="fas fa-cogs"></i> Go to Admin Panel</button>
        </a>

        <!-- Display success or error messages -->
        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Form to Add New Users -->
        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select><br>

            <button type="submit" name="add_user" class="add-user-btn">Add User</button>
        </form>

        <!-- List of existing users -->
        <h2>Existing Users</h2>

        <!-- Display Admins -->
        <h3>Admins</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display admins from the database
                $sql = "SELECT user_id, name, email, role, created_at FROM users WHERE role = 'admin'";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['user_id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['role']}</td>";
                    echo "<td>{$row['created_at']}</td>";
                    echo "<td>
                            <form action='' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>
                                <input type='hidden' name='delete_user_id' value='{$row['user_id']}'>
                                <button type='submit' name='delete_user'><i class='fas fa-trash'></i> Delete</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Display Users -->
        <h3>Users</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display users from the database
                $sql = "SELECT user_id, name, email, role, created_at FROM users WHERE role = 'user'";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['user_id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['role']}</td>";
                    echo "<td>{$row['created_at']}</td>";
                    echo "<td>
                            <form action='' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>
                                <input type='hidden' name='delete_user_id' value='{$row['user_id']}'>
                                <button type='submit' name='delete_user'><i class='fas fa-trash'></i> Delete</button>
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