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

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['prescription_file'])) {
    // Get the uploaded file data
    $file = $_FILES['prescription_file'];

    // Validate file type and size
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    /* 
    if (!in_array($file['type'], $allowed_types)) {
        echo "Invalid file type. Only images, PDFs, and Word documents are allowed.";
        exit;
    }
    */

    // Check for file upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        // Commented out error message for file upload issue
        // echo "Error uploading the file. Please try again.";
        exit;
    }

    // Prepare file path and data for database insertion
    $file_path = 'uploads/' . basename($file['name']);
    $file_data = file_get_contents($file['tmp_name']);

    // Store the file path and data in the database
    include '../db_connection.php'; // Include your DB connection file
    $query = "INSERT INTO prescriptions (user_id, file_path, file_data, status) VALUES (?, ?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'iss', $user_id, $file_path, $file_data);
        if (mysqli_stmt_execute($stmt)) {
            // Move the uploaded file to the uploads directory
            move_uploaded_file($file['tmp_name'], '../uploads/' . basename($file['name']));
            echo "Prescription uploaded successfully.";
        } else {
            // Commented out error message for saving prescription
            // echo "Error saving prescription: " . mysqli_error($conn);
        }
    } else {
        // Commented out error message for query preparation
        // echo "Error preparing query: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Upload</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <button onclick="window.location.href='../home_page/user_home.php';">
        <i class="fas fa-home"></i> Back to User Home
    </button>

    <h1><i class="fas fa-file-upload"></i> Prescription Upload</h1>
    <h2>Welcome <?php echo htmlspecialchars($user_name); ?>! Your User ID is <?php echo htmlspecialchars($user_id); ?>.</h2>

    <!-- Prescription upload form -->
    <form action="prescription_upload.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>" method="POST" enctype="multipart/form-data">
        <label for="prescription_file">
            <i class="fas fa-file-medical"></i> Upload Prescription File (JPEG, PNG, GIF, PDF, Word):
        </label>
        <input type="file" name="prescription_file" id="prescription_file" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" required><br><br>
        <button type="submit">
            <i class="fas fa-cloud-upload-alt"></i> Upload Prescription
        </button>
    </form>
</body>

</html>