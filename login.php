<?php
// Include the database connection file
include 'db_connection.php';

// Initialize variables for storing errors
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Username and password are required!";
    } else {
        // Prepare a SQL statement to retrieve the user based on username
        $stmt = $conn->prepare("SELECT user_id, name, password, role FROM Users WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Fetch the user data
            $stmt->bind_result($user_id, $name, $hashed_password, $role);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Start the session
                session_start();

                // Regenerate session ID to enhance security
                session_regenerate_id();

                // Store user data in session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['role'] = $role;

                // Redirect based on role
                if ($role === 'admin') {
                    header("Location: home_page/admin_home.php");
                } else {
                    header("Location: home_page/user_home.php");
                }
                exit;
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="style/login.css">

    <!-- background image -->
    <style>
        body {
            background-image: url("assets/02.jpg");
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-sign-in-alt"></i> Login</h1>

        <?php
        if (!empty($error)) {
            echo "<p class='error'>" . htmlspecialchars($error) . "</p>";
        }
        ?>

        <form method="post" action="login.php">
            <label for="username">
                <i class="fas fa-user"></i> Username:
            </label>
            <input type="text" name="username" id="username" required>

            <label for="password">
                <i class="fas fa-lock"></i> Password:
            </label>
            <input type="password" name="password" id="password" required>

            <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>

        <p>Don't have an account? <a href="register.php"><i class="fas fa-user-plus"></i> Register here</a></p>
    </div>
</body>

</html>