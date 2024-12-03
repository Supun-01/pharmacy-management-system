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

// Handle prescription file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['prescription_file'])) {
    $file = $_FILES['prescription_file'];

    // Check if file was uploaded without error
    if ($file['error'] == 0) {
        // Define the upload directory
        $upload_dir = '../uploads/';

        // Generate a unique file name to avoid overwriting
        $file_name = uniqid('prescription_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the server
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Insert the file path into the database
            include '../db_connection.php';
            $query = "INSERT INTO `prescriptions` (`user_id`, `file_path`, `status`) 
                      VALUES ($user_id, '$file_path', 'pending')";

            if (mysqli_query($conn, $query)) {
                echo "Prescription uploaded successfully!";
            } else {
                echo "Error uploading prescription: " . mysqli_error($conn);
            }
        } else {
            echo "Error moving the uploaded file.";
        }
    } else {
        echo "Error uploading the file: " . $file['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Upload</title>
</head>

<body>
    <button onclick="window.location.href='../home_page/user_home.php';">Back to User Home</button>

    <h1>Welcome <?php echo htmlspecialchars($user_name); ?>! Your User ID is <?php echo htmlspecialchars($user_id); ?>.</h1>

    <h2>Upload Prescription</h2>
    <!-- Prescription upload form -->
    <form action="prescription_upload.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>" method="POST" enctype="multipart/form-data">
        <label for="prescription_file">Select Prescription File:</label>
        <input type="file" name="prescription_file" id="prescription_file" required>
        <br><br>
        <button type="submit">Upload Prescription</button>
    </form>
</body>

</html>