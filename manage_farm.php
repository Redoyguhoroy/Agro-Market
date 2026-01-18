<?php
session_start();

// Simulate a logged-in farmer for testing
// Remove this after implementing a real login system
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'farmer';
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: index.php");
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "agfms");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ===== ADD FARM DATA =====
if (isset($_POST['add_farm_data'])) {
    $sql = "INSERT INTO farm_management (farmer_id, farm_name, location, irrigation, farming_details)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $_SESSION['user_id'], $_POST['farm_name'], $_POST['location'], $_POST['irrigation'], $_POST['farming_details']);
    $stmt->execute();
    $farm_success = "Farm data added successfully!";
}

// ===== ADD PRODUCT =====
if (isset($_POST['add_product'])) {
    $farmer_id = $_SESSION['user_id'];
    $image = $_FILES['product_image']['name'];

    if (!is_dir("uploads")) mkdir("uploads");

    move_uploaded_file($_FILES['product_image']['tmp_name'], "uploads/" . $image);

    $sql = "INSERT INTO marketplace (farmer_id, product_name, product_description, price, stock, product_image)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssis", $farmer_id, $_POST['product_name'], $_POST['product_description'], $_POST['price'], $_POST['stock'], $image);
    $stmt->execute();
    $product_success = "Product added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Farm – Agro Marketplace</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
body { margin:0; font-family:'Poppins',sans-serif; background:#eef5ee; }
nav { background:#2e7d32; padding:15px 40px; color:white; display:flex; justify-content:space-between; align-items:center; box-shadow:0 4px 12px rgba(0,0,0,0.15); }
nav .logo { font-size:26px; font-weight:700; display:flex; align-items:center; }
nav .logo img { width:50px; margin-right:10px; }
nav ul { display:flex; list-style:none; gap:30px; }
nav ul li a { color:white; text-decoration:none; font-weight:500; transition:.2s; font-size:17px; }
nav ul li a:hover { color:#c6ffd0; }
.container { max-width:1100px; margin:40px auto; background:white; padding:30px; border-radius:15px; box-shadow:0px 8px 18px rgba(0,0,0,0.12); }
h2 { color:#2e7d32; margin-bottom:10px; }
.tabs { display:flex; gap:20px; margin-top:25px; }
.tab-button { padding:12px 22px; border:none; background:#dff5df; border-radius:10px; cursor:pointer; font-weight:600; font-size:15px; transition:0.3s; }
.tab-button:hover,.tab-button.active { background:#2e7d32;color:white; }
.flex-box { display:flex; flex-wrap:wrap; gap:20px; margin-top:20px; }
.form-card { background:#f7fdf7; padding:20px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1); flex:1; min-width:300px; }
label { font-weight:600; margin-top:10px; display:block; }
input, textarea { width:100%; padding:12px; border-radius:10px; border:1px solid #ccc; margin-top:8px; }
button { margin-top:15px; padding:12px; width:100%; background:#2e7d32; border:none; border-radius:10px; color:white; font-size:16px; font-weight:600; cursor:pointer; transition:.25s; }
button:hover { background:#256428; transform:scale(1.03); }
table { width:100%; border-collapse:collapse; margin-top:25px; background:#ffffff; border-radius:12px; overflow:hidden; }
th { background:#e7f7e9; padding:12px; font-weight:600; text-align:center; }
td { padding:10px; text-align:center; border-bottom:1px solid #eee; }
tr:hover { background:#f2fff3; }
.success-msg { color:green; margin-top:10px; font-weight:600; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/7665/7665441.png"> Agro Market
    </div>
    <ul>
        <li><a href="home.php">HOME</a></li>
        <li><a href="manage_farm.php">MANAGE FARM</a></li>
        <li><a href="marketplace.php">MARKETPLACE</a></li>
        <li><a href="cart.php">MY CART</a></li>
        <li><a href="main.php?logout=true">LOG OUT</a></li>
    </ul>
</nav>

<div class="container">
<h2>Manage Farm</h2>
<div class="tabs">
    <button class="tab-button active" onclick="openTab('enterData')">Enter Data</button>
    <button class="tab-button" onclick="openTab('seeDetails')">See Details</button>
</div>

<!-- ENTER DATA -->
<div id="enterData" class="tab-content">
<div class="flex-box">
    <!-- Farm Information -->
    <div class="form-card">
        <h3>Farm Information</h3>
        <?php if(isset($farm_success)) echo "<p class='success-msg'>$farm_success</p>"; ?>
        <form method="post">
            <label>Farm Name:</label>
            <input type="text" name="farm_name" required>
            <label>Location:</label>
            <input type="text" name="location" required>
            <label>Irrigation :</label>
            <textarea name="irrigation" required></textarea>
            <label>Farming Details:</label>
            <textarea name="farming_details" required></textarea>
            <button type="submit" name="add_farm_data">Add Farm Data</button>
        </form>
    </div>

    <!-- Add Product -->
    <div class="form-card">
        <h3>Add Product to Marketplace</h3>
        <?php if(isset($product_success)) echo "<p class='success-msg'>$product_success</p>"; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="product_name" required>
            <label>Description:</label>
            <textarea name="product_description" required></textarea>
            <label>Price:</label>
            <input type="number" name="price" required>
            <label>Stock:</label>
            <input type="number" name="stock" required>
            <label>Product Image:</label>
            <input type="file" name="product_image" required>
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>
</div>
</div>

<!-- SEE DETAILS -->
<div id="seeDetails" class="tab-content" style="display:none;">
    <h3>Your Farm Details</h3>
    <?php
    $id = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM farm_management WHERE farmer_id='$id'");
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Farm Name</th><th>Location</th><th>Irrigation</th><th>Details</th></tr>";
        while($row = $result->fetch_assoc()){
            echo "<tr>
                    <td>{$row['farm_name']}</td>
                    <td>{$row['location']}</td>
                    <td>{$row['irrigation']}</td>
                    <td>{$row['farming_details']}</td>
                  </tr>";
        }
        echo "</table>";
    } else { echo "<p>No farm data found.</p>"; }
    ?>
</div>

</div>

<script>
function openTab(tab){
    document.querySelectorAll('.tab-content').forEach(t => t.style.display='none');
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    document.getElementById(tab).style.display='block';
    event.currentTarget.classList.add('active');
}
</script>
</body>
</html>
