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
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #444;
            margin-top: 30px;
        }

        /* Add User Form */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        form input,
        form select {
            width: 100%;
            max-width: 400px;
            padding: 12px;
            font-size: 1em;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s;
        }

        form input:focus,
        form select:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-button {
            width: 100%;
            max-width: 400px;
            background-color: #007bff;
            color: white;
            font-size: 1.1em;
            padding: 15px 20px;
            border-radius: 8px;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .form-button:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            text-align: left;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f8f9fa;
            font-size: 1.1em;
        }

        table td {
            font-size: 1em;
        }

        table td a {
            color: #d9534f;
            text-decoration: none;
            font-size: 1.2em;
            transition: color 0.3s;
        }

        table td a:hover {
            color: #c9302c;
        }

        /* Button Container */
        .button-container a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1.1em;
            text-decoration: none;
            margin-bottom: 20px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button-container a:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 2em;
            }

            h2 {
                font-size: 1.6em;
            }

            table th,
            table td {
                font-size: 0.9em;
            }

            form {
                width: 100%;
            }

            .form-button {
                font-size: 1em;
                padding: 12px;
            }

            .button-container a {
                font-size: 1em;
                padding: 12px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>User Management</h1>

        <!-- Button to go to Admin Panel -->
        <div class="button-container">
            <a href="../home_page/admin_home.php">
                <i class="fas fa-cogs"></i> Go to Admin Panel
            </a>
        </div>

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
            <input type="text" name="name" placeholder="Enter Name" required>

            <input type="email" name="email" placeholder="Enter Email" required>

            <input type="password" name="password" placeholder="Enter Password" required>

            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <select name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit" class="form-button">Add User</button>
        </form>

        <!-- Display Users -->
        <h2>All Users</h2>
        <table>
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
                                <a href='user_management.php?delete=" . $row['user_id'] . "'><i class='fas fa-trash-alt'></i> Delete</a>
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
        <table>
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
                                <a href='user_management.php?delete=" . $row['user_id'] . "'><i class='fas fa-trash-alt'></i> Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No admins found</td></tr>";
            }
            ?>
        </table>
    </div>
</body>

</html>


<?php
// Close the database connection
$conn->close();
?>