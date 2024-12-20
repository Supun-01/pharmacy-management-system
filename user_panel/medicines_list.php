<?php
// Include the database connection file
include_once '../db_connection.php';

// Get user data from query parameters
$user_id = $_GET['user_id'] ?? null;
$user_name = $_GET['user_name'] ?? null;

// Check if user is logged in and has valid parameters
if (!$user_id || !$user_name) {
    // Redirect to login page if user_id or user_name are not provided
    header("Location: ../login.php");
    exit;
}

// Fetch all medicines from the database
$sql = "SELECT * FROM medicines";
$result = $conn->query($sql);

// Check for any errors in the query
if (!$result) {
    die("Error fetching medicines: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicines List</title>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="../style/card.css"> Link to external CSS file -->
    <link rel="stylesheet" href="../style/user_panel/medicines_list.css">

    <style>
        body {
            background-image: url("../assets/medicines_list.jpg");
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
    <!-- Back to User Home Button -->
    <button class="back-btn" onclick="window.location.href='../home_page/user_home.php';">
        <i class="fas fa-home"></i> Back to User Home
    </button>

    <h1>
        <i class="fas fa-user"></i> Welcome <?php echo htmlspecialchars($user_name); ?>.
    </h1>

    <div class="medicine-cards-container">
        <?php while ($medicine = $result->fetch_assoc()): ?>
            <div class="medicine-card">
                <img src="<?php echo htmlspecialchars($medicine['image_location']); ?>" alt="<?php echo htmlspecialchars($medicine['name']); ?>" class="medicine-image">
                <h2><i class="fas fa-pills"></i> <?php echo htmlspecialchars($medicine['name']); ?></h2>
                <p><i class="fas fa-tags"></i> Category: <?php echo htmlspecialchars($medicine['category']); ?></p>
                <p><i class="fas fa-dollar-sign"></i> Price: $<?php echo number_format($medicine['price'], 2); ?></p>
                <p><i class="fas fa-warehouse"></i> Stock: <?php echo $medicine['stock']; ?> available</p>
                <div>
                    <label for="quantity_<?php echo $medicine['medicine_id']; ?>">
                        <i class="fas fa-sort-numeric-up"></i> Quantity:
                    </label>
                    <input type="number" id="quantity_<?php echo $medicine['medicine_id']; ?>" name="quantity" min="1" max="<?php echo $medicine['stock']; ?>" value="1">
                </div>
                <button class="add-to-cart" onclick="addToCart(<?php echo $medicine['medicine_id']; ?>, '<?php echo htmlspecialchars($medicine['name']); ?>', <?php echo $medicine['price']; ?>, '<?php echo $medicine['stock']; ?>')">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Cart Navigation Bar -->
    <div class="cart-nav">
        <h3><i class="fas fa-shopping-cart"></i> Cart</h3>
        <ul id="cart-items"></ul>
        <button onclick="confirmOrder()">
            <i class="fas fa-check-circle"></i> Confirm Order
        </button>
    </div>

    <script>
        let cart = [];

        function addToCart(medicine_id, name, price, stock) {
            const quantityInput = document.getElementById('quantity_' + medicine_id);
            const quantity = parseInt(quantityInput.value);

            if (quantity < 1 || quantity > stock) {
                alert('Please select a valid quantity.');
                return;
            }

            const item = {
                medicine_id,
                name,
                price,
                quantity
            };

            // Add item to cart
            cart.push(item);

            // Update cart display
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItemsList = document.getElementById('cart-items');
            cartItemsList.innerHTML = ''; // Clear previous cart items

            cart.forEach(item => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<i class="fas fa-capsules"></i> ${item.name} - $${item.price} x ${item.quantity}`;
                cartItemsList.appendChild(listItem);
            });
        }

        function confirmOrder() {
            if (cart.length === 0) {
                alert("Your cart is empty.");
                return;
            }

            // Disable the "Confirm Order" button to prevent multiple clicks
            document.querySelector('.cart-nav button').disabled = true;

            // Create order entries for each item in the cart
            let successCount = 0;
            let failureCount = 0;

            cart.forEach(item => {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "confirm_order.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        successCount++;
                    } else {
                        failureCount++;
                    }

                    // Check if all orders have been processed
                    if (successCount + failureCount === cart.length) {
                        if (successCount === cart.length) {
                            alert("Order confirmed successfully!");
                        } else {
                            alert("There was an error confirming your order.");
                        }

                        // Reset the cart
                        cart = [];
                        updateCartDisplay();

                        // Re-enable the "Confirm Order" button
                        document.querySelector('.cart-nav button').disabled = false;
                    }
                };
                xhr.send(`user_id=${<?php echo $user_id; ?>}&medicine_id=${item.medicine_id}&quantity=${item.quantity}`);
            });
        }
    </script>
</body>

</html>