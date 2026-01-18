<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$conn = new mysqli('localhost', 'root', '', 'agfms');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch logged-in user
$user_result = $conn->query("SELECT * FROM users WHERE user_id='$user_id'");
$user = $user_result->fetch_assoc();

// Fetch orders & reviews for farmers
$orders = [];
$reviews = [];

if ($role == 'farmer') {
    $order_sql = "SELECT orders.*, marketplace.product_name 
                  FROM orders 
                  JOIN marketplace ON orders.product_id = marketplace.product_id 
                  WHERE marketplace.farmer_id = '$user_id'";
    $order_result = $conn->query($order_sql);
    while ($row = $order_result->fetch_assoc()) $orders[] = $row;

    $review_sql = "SELECT reviews.*, marketplace.product_name 
                   FROM reviews 
                   JOIN marketplace ON reviews.product_id = marketplace.product_id 
                   WHERE marketplace.farmer_id = '$user_id'";
    $review_result = $conn->query($review_sql);
    while ($row = $review_result->fetch_assoc()) $reviews[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Dashboard – Agro Marketplace</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #eef5ee;
    }

    /* NAVIGATION */
    nav {
        background: #2e7d32;
        padding: 15px 40px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    nav .logo {
        display: flex;
        align-items: center;
        font-size: 26px;
        font-weight: 700;
    }

    nav .logo img {
        width: 50px;
        margin-right: 10px;
    }

    nav ul {
        list-style: none;
        display: flex;
        gap: 30px;
    }

    nav ul li a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        font-size: 17px;
        transition: 0.2s;
    }

    nav ul li a:hover {
        color: #b8ffcd;
    }

    /* CONTAINER */
    .container {
        max-width: 1100px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        animation: fadeIn 0.5s ease;
    }

    h2 {
        color: #2e7d32;
        margin-bottom: 15px;
    }

    .user-box {
        display: flex;
        align-items: center;
        gap: 20px;
        background: #dbf3df;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 35px;
    }

    .user-box img {
        width: 80px;
    }

    /* TABLES */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: #ffffff;
        border-radius: 10px;
        overflow: hidden;
    }

    th {
        background: #e8f7ea;
        padding: 12px;
        font-weight: 600;
        text-align: center;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        text-align: center;
    }

    tr:hover {
        background: #f7fdf7;
    }

    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: #2e7d32;
        margin-top: 40px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
</head>

<body>

<!-- NAVBAR -->
<nav>
    <div class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/7665/7665441.png">
        Agro Market
    </div>

    <ul>
        <li><a href="home.php">HOME</a></li>
        <?php if ($role == 'farmer') { ?>
            <li><a href="manage_farm.php">MANAGE FARM</a></li>
        <?php } ?>
        <li><a href="marketplace.php">MARKETPLACE</a></li>
        <li><a href="cart.php">MY CART</a></li>
        <li><a href="main.php?logout=true">LOG OUT</a></li>
    </ul>
</nav>

<!-- CONTENT -->
<div class="container">

    <h2>Welcome, <?= htmlspecialchars($user['username']); ?> 👋</h2>

    <div class="user-box">
        <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png">
        <div>
            <p><strong>Role:</strong> <?= ucfirst($user['role']); ?></p>
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
        </div>
    </div>

    <?php if ($role == 'farmer') { ?>

        <!-- ORDERS SECTION -->
        <div class="section-title">📦 Your Orders</div>

        <?php if (count($orders) > 0) { ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer ID</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Date</th>
                    <th>Address</th>
                    <th>Phone</th>
                </tr>

                <?php foreach ($orders as $o) { ?>
                    <tr>
                        <td><?= $o['order_id']; ?></td>
                        <td><?= $o['buyer_id']; ?></td>
                        <td><?= htmlspecialchars($o['product_name']); ?></td>
                        <td><?= $o['quantity']; ?></td>
                        <td><?= $o['order_date']; ?></td>
                        <td><?= htmlspecialchars($o['delivery_address']); ?></td>
                        <td><?= htmlspecialchars($o['phone_number']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { echo "<p>No orders found.</p>"; } ?>

        <!-- REVIEWS SECTION -->
        <div class="section-title">⭐ Product Reviews</div>

        <?php if (count($reviews) > 0) { ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>

                <?php foreach ($reviews as $r) { ?>
                    <tr>
                        <td><?= htmlspecialchars($r['product_name']); ?></td>
                        <td><?= $r['rating']; ?>/5</td>
                        <td><?= htmlspecialchars($r['comment']); ?></td>
                        <td><?= $r['review_date']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { echo "<p>No reviews yet.</p>"; } ?>

    <?php } ?>

</div>

</body>
</html>
