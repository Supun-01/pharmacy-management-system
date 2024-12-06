<?php
// Include the database connection file
include 'db_connection.php';

// Initialize variables for storing errors and success messages
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input from form
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'user'; // Default role

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $stmt = $conn->prepare("INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success = "User registered successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="style/register.css">

    <!-- background image -->
    <style>
        body {
            background-image: url("assets/02.jpg");
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-user-plus"></i> Register</h1>

        <?php
        if (!empty($error)) {
            echo "<p class='error'><i class='fas fa-exclamation-circle'></i> $error</p>";
        }
        if (!empty($success)) {
            echo "<p class='success'><i class='fas fa-check-circle'></i> $success</p>";
        }
        ?>

        <form method="post" action="register.php">
            <label for="name">
                <i class="fas fa-user"></i> Name:
            </label>
            <input type="text" name="name" id="name" required>

            <label for="email">
                <i class="fas fa-envelope"></i> Email:
            </label>
            <input type="email" name="email" id="email" required>

            <label for="password">
                <i class="fas fa-lock"></i> Password:
            </label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">
                <i class="fas fa-lock"></i> Confirm Password:
            </label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit"><i class="fas fa-user-plus"></i> Register</button>
        </form>

        <p>Already have an account? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login here</a></p>
    </div>
</body>

</html>