<?php
// Include the database connection file
include '../db_connection.php';

// Fetch all medicines from the database
$sql = "SELECT * FROM medicines";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Store the fetched data in an array
    $medicines = [];
    while ($row = $result->fetch_assoc()) {
        $medicines[] = $row;
    }
} else {
    $medicines = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicines</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to CSS file -->
    <style>
        /* Add some basic styles for the cards */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 250px;
            padding: 20px;
            text-align: center;
        }

        .card img {
            max-width: 100%;
            border-radius: 10px;
        }

        .card h3 {
            margin: 10px 0;
            color: #333;
        }

        .card p {
            color: #666;
            font-size: 14px;
        }

        .card .price {
            color: #27ae60;
            font-weight: bold;
            font-size: 18px;
        }

        .card .btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }

        .card .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php foreach ($medicines as $medicine): ?>
            <div class="card">
                <?php if ($medicine['image_location']): ?>
                    <img src="<?php echo htmlspecialchars($medicine['image_location']); ?>" alt="<?php echo htmlspecialchars($medicine['name']); ?>">
                <?php else: ?>
                    <img src="default_image.jpg" alt="Default Image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($medicine['name']); ?></h3>
                <p class="price">Price: $<?php echo number_format($medicine['price'], 2); ?></p>
                <a href="#" class="btn">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>