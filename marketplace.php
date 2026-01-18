<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'agfms');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1;

    $sql = "INSERT INTO cart (buyer_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
    $conn->query($sql);
}

$sql = "SELECT * FROM marketplace";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Marketplace - Agro Marketplace</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #f2f7f5;
        }

        header {
            background: #2e7d32;
            padding: 15px;
            color: white;
            text-align: center;
            box-shadow: 0px 3px 8px rgba(0,0,0,0.2);
            animation: fadeDown 0.6s ease;
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        nav ul {
            display: flex;
            justify-content: center;
            gap: 25px;
            padding: 0;
            list-style: none;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        nav a:hover {
            opacity: 0.8;
        }

        main {
            width: 90%;
            margin: auto;
            padding: 30px 0;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1b5e20;
        }

        /* PRODUCT GRID */
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .product-item {
            background: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.35s, box-shadow 0.35s;
            position: relative;
            overflow: hidden;
        }

        .product-item:hover {
            transform: translateY(-10px);
            box-shadow: 0px 10px 18px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.4s ease;
        }

        .product-item:hover .product-image {
            transform: scale(1.08);
        }

        h3 {
            margin: 10px 0 5px;
        }

        p {
            margin: 5px 0;
            color: #444;
        }

        button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 8px;
            width: 80%;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #1b5e20;
            transform: scale(1.05);
        }

        button:active {
            transform: scale(0.95);
        }

        /* Floating Glow Animation */
        .product-item::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -150%;
            left: -150%;
            background: radial-gradient(circle, rgba(46,125,50,0.08), transparent 70%);
            transform: rotate(25deg);
            transition: opacity 0.4s;
            opacity: 0;
        }

        .product-item:hover::after {
            opacity: 1;
        }
    </style>
</head>

<body>
    <header>
        <h1>Agro Marketplace For Local Farmers</h1>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <?php if ($role == 'farmer') { ?>
                <li><a href="manage_farm.php">Manage Farm</a></li>
                <?php } ?>
                <li><a href="marketplace.php">Marketplace</a></li>
                <li><a href="cart.php">My Cart</a></li>
                <li><a href="main.php?logout=true">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Available Products</h2>

        <div class="product-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-item'>";
                    echo "<img src='uploads/{$row['product_image']}' class='product-image' alt='Product'>";
                    echo "<h3>{$row['product_name']}</h3>";
                    echo "<p><strong>৳{$row['price']}</strong></p>";
                    echo "<p>Stock: {$row['stock']}</p>";

                    echo "<form method='post'>";
                    echo "<input type='hidden' name='product_id' value='{$row['product_id']}'>";
                    echo "<button type='submit' name='add_to_cart'>Add to Cart</button>";
                    echo "</form>";

                    echo "</div>";
                }
            } else {
                echo "<p>No products available.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>

