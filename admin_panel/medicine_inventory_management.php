<?php
// Start session
session_start();

// Include the database connection
include_once('../db_connection.php');

// Check if user_id and user_name are passed
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$user_name = isset($_GET['user_name']) ? $_GET['user_name'] : null;

// Validate if the user is an admin
if ($user_id && $user_name) {
    // Query to check if the user_id corresponds to an admin
    $check_admin_query = "SELECT user_id, role FROM Users WHERE user_id = ? AND role = 'admin'";
    $stmt_check_admin = $conn->prepare($check_admin_query);
    $stmt_check_admin->bind_param("i", $user_id);
    $stmt_check_admin->execute();
    $result = $stmt_check_admin->get_result();

    // If no valid admin is found, redirect to login page
    if ($result->num_rows == 0) {
        // End the session and redirect
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    }
    $stmt_check_admin->close();
} else {
    // If the necessary parameters are missing, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Initialize variables for success and error messages
$success = $error = "";

// Handle delete action
if (isset($_GET['delete'])) {
    $medicine_id = $_GET['delete'];

    $delete_query = "DELETE FROM Medicines WHERE medicine_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $medicine_id);

    if ($stmt->execute()) {
        $success = "Medicine deleted successfully!";
    } else {
        $error = "Error deleting medicine: " . $stmt->error;
    }
    $stmt->close();
}

// Handle update action
if (isset($_POST['update'])) {
    $medicine_id = $_POST['medicine_id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $update_query = "UPDATE Medicines SET name = ?, category = ?, price = ?, stock = ? WHERE medicine_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssdis", $name, $category, $price, $stock, $medicine_id);

    if ($stmt->execute()) {
        $success = "Medicine updated successfully!";
    } else {
        $error = "Error updating medicine: " . $stmt->error;
    }
    $stmt->close();
}

// Handle add new medicine action
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $insert_query = "INSERT INTO Medicines (name, category, price, stock) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssdi", $name, $category, $price, $stock);

    if ($stmt->execute()) {
        $success = "Medicine added successfully!";
    } else {
        $error = "Error adding medicine: " . $stmt->error;
    }
    $stmt->close();
}

// Retrieve all medicines
$query = "SELECT * FROM Medicines";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Inventory Management</title>
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- External CSS -->
    <link rel="stylesheet" href="../style/admin_panel/medicine_inventory_management.css">
</head>

<body>
    <div class="container">
        <h1 class="page-title">Medicine Inventory Management</h1>

        <!-- Button to go to Admin Panel -->
        <a href="../home_page/admin_home.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
            <button class="btn admin-btn"><i class="fas fa-cogs"></i> Go to Admin Panel</button>
        </a>

        <!-- Success and Error Messages -->
        <?php if ($success) {
            echo "<p class='success'>$success</p>";
        } ?>
        <?php if ($error) {
            echo "<p class='error'>$error</p>";
        } ?>

        <!-- Add New Medicine Form -->
        <h2 class="section-title">Add New Medicine</h2>
        <form class="form" method="POST" action="medicine_inventory_management.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" class="input" required><br><br>

            <label for="category">Category:</label>
            <input type="text" name="category" class="input" required><br><br>

            <label for="price">Price:</label>
            <input type="text" name="price" class="input" required><br><br>

            <label for="stock">Stock:</label>
            <input type="text" name="stock" class="input" required><br><br>

            <button type="submit" name="add" class="btn add-btn"><i class="fas fa-plus-circle"></i> Add Medicine</button>
        </form>

        <!-- Medicines Table -->
        <h2 class="section-title">All Medicines</h2>
        <table class="medicine-table">
            <thead>
                <tr>
                    <th>Medicine ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Added At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['medicine_id'] . "</td>
                                <td>" . $row['name'] . "</td>
                                <td>" . $row['category'] . "</td>
                                <td>" . $row['price'] . "</td>
                                <td>" . $row['stock'] . "</td>
                                <td>" . $row['added_at'] . "</td>
                                <td>
                                    <a href='medicine_inventory_management.php?edit=" . $row['medicine_id'] . "&user_id=" . $user_id . "&user_name=" . urlencode($user_name) . "'>
                                        <i class='fas fa-edit'></i> Edit
                                    </a> | 
                                    <a href='medicine_inventory_management.php?delete=" . $row['medicine_id'] . "&user_id=" . $user_id . "&user_name=" . urlencode($user_name) . "'>
                                        <i class='fas fa-trash-alt'></i> Delete
                                    </a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No medicines found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Edit Medicine Form -->
        <?php
        if (isset($_GET['edit'])) {
            $medicine_id = $_GET['edit'];
            $query = "SELECT * FROM Medicines WHERE medicine_id = $medicine_id";
            $edit_result = $conn->query($query);
            $edit_row = $edit_result->fetch_assoc();
        ?>
            <h2 class="section-title">Edit Medicine</h2>
            <form class="form" method="POST" action="medicine_inventory_management.php?user_id=<?php echo $user_id; ?>&user_name=<?php echo urlencode($user_name); ?>">
                <input type="hidden" name="medicine_id" value="<?php echo $edit_row['medicine_id']; ?>">

                <label for="name">Name:</label>
                <input type="text" name="name" value="<?php echo $edit_row['name']; ?>" class="input" required><br><br>

                <label for="category">Category:</label>
                <input type="text" name="category" value="<?php echo $edit_row['category']; ?>" class="input" required><br><br>

                <label for="price">Price:</label>
                <input type="text" name="price" value="<?php echo $edit_row['price']; ?>" class="input" required><br><br>

                <label for="stock">Stock:</label>
                <input type="text" name="stock" value="<?php echo $edit_row['stock']; ?>" class="input" required><br><br>

                <button type="submit" name="update" class="btn update-btn"><i class="fas fa-save"></i> Update Medicine</button>
            </form>
        <?php
        }
        ?>
    </div>
</body>

</html>


<?php
// Close the database connection
$conn->close();
?>