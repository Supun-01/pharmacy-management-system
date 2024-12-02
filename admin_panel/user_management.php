<?php
// Include the database connection
include '../db_connection.php';

// Initialize variables for storing success or error messages
$success = "";
$error = "";

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id_to_delete = $_GET['delete'];

    // Prepare and execute deletion query for both users and admins
    $delete_query_user = "DELETE FROM Users WHERE user_id = ?";
    $delete_query_admin = "DELETE FROM Users WHERE user_id = ? AND role = 'admin'";

    $stmt_user = $conn->prepare($delete_query_user);
    $stmt_admin = $conn->prepare($delete_query_admin);

    $stmt_user->bind_param("i", $user_id_to_delete);
    $stmt_admin->bind_param("i", $user_id_to_delete);

    // Execute and check for deletion
    if ($stmt_user->execute() || $stmt_admin->execute()) {
        $success = "User deleted successfully!";
    } else {
        $error = "Failed to delete user.";
    }

    $stmt_user->close();
    $stmt_admin->close();
}

// Handle user addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Check if passwords match
    if ($password != $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the password for storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user or admin into the Users table
        $insert_query = "INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success = "New user added successfully!";
        } else {
            $error = "Failed to add user.";
        }

        $stmt->close();
    }
}

// Retrieve all users from the database (both admins and regular users)
$sql_users = "SELECT user_id, name, email, role, created_at FROM Users WHERE role = 'user'";
$sql_admins = "SELECT user_id, name, email, role, created_at FROM Users WHERE role = 'admin'";

$result_users = $conn->query($sql_users);
$result_admins = $conn->query($sql_admins);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>

<body>
    <h1>User Management</h1>

    <!-- Button to go to Admin Panel -->
    <a href="../home_page/admin_home.php">
        <button>Go to Admin Panel</button>
    </a>

    <!-- Display success or error messages -->
    <?php
    if ($success) {
        echo "<p style='color: green;'>$success</p>";
    }
    if ($error) {
        echo "<p style='color: red;'>$error</p>";
    }
    ?>

    <!-- Add User Form -->
    <h2>Add New User</h2>
    <form method="POST" action="user_management.php">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" required><br><br>

        <label for="role">Role:</label>
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br><br>

        <button type="submit">Add User</button>
    </form>

    <!-- Display Users -->
    <h2>All Users</h2>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result_users->num_rows > 0) {
            // Output data of each user
            while ($row = $result_users->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['user_id'] . "</td>
                        <td>" . $row['name'] . "</td>
                        <td>" . $row['email'] . "</td>
                        <td>" . $row['role'] . "</td>
                        <td>" . $row['created_at'] . "</td>
                        <td>
                            <a href='user_management.php?delete=" . $row['user_id'] . "'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No users found</td></tr>";
        }
        ?>
    </table>

    <!-- Display Admins -->
    <h2>All Admins</h2>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result_admins->num_rows > 0) {
            // Output data of each admin
            while ($row = $result_admins->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['user_id'] . "</td>
                        <td>" . $row['name'] . "</td>
                        <td>" . $row['email'] . "</td>
                        <td>" . $row['role'] . "</td>
                        <td>" . $row['created_at'] . "</td>
                        <td>
                            <a href='user_management.php?delete=" . $row['user_id'] . "'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No admins found</td></tr>";
        }
        ?>
    </table>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>