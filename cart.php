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
$error = '';
$success = '';

/* -------------------- HANDLE ORDER -------------------- */
if (isset($_POST['confirm_order'])) {
    $delivery_address = $_POST['delivery_address'];
    $phone_number = $_POST['phone_number'];
    $payment_method = $_POST['payment_method'];
    $trx_id = $_POST['trx_id'];

    if (!preg_match("/^[0-9]{11}$/", $phone_number)) {
        $error = "Invalid phone number. Enter 11 digits.";
    } elseif (empty($trx_id)) {
        $error = "Transaction ID is required.";
    } elseif (isset($_POST['product_ids'])) {

        foreach ($_POST['product_ids'] as $product_id) {
            $quantity = intval($_POST['quantity'][$product_id]);

            $sql = "INSERT INTO orders (buyer_id, product_id, quantity, delivery_address, phone_number, payment_method, trx_id)
                    VALUES ('$user_id','$product_id','$quantity','$delivery_address','$phone_number','$payment_method','$trx_id')";
            
            if ($conn->query($sql)) {
                $conn->query("DELETE FROM cart WHERE buyer_id='$user_id' AND product_id='$product_id'");
            }
        }
        $success = "Order placed successfully!";
    } else {
        $error = "Your cart is empty.";
    }
}

/* -------------------- FETCH CART ITEMS -------------------- */
$sql = "SELECT cart.product_id, cart.quantity, marketplace.product_name,
        marketplace.price, marketplace.product_image
        FROM cart
        JOIN marketplace ON cart.product_id = marketplace.product_id
        WHERE cart.buyer_id='$user_id'";

$result = $conn->query($sql);

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

$total_price = number_format($total_price, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Cart - Agro Farm</title>

<style>
body {
    background: #f4f6f8;
    font-family: "Segoe UI", sans-serif;
    margin: 0;
}

header {
    background: #1b8f3b;
    padding: 18px;
    text-align: center;
    color: white;
    font-size: 26px;
    font-weight: bold;
    letter-spacing: 1px;
}

nav {
    background: #157a30;
    padding: 10px 0;
}

nav ul {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
}

nav ul li {
    margin: 0 20px;
}

nav ul li a {
    color: white;
    font-size: 17px;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}

nav ul li a:hover {
    opacity: 0.7;
}

.tabs {
    display: flex;
    justify-content: center;
    margin: 25px 0;
}
.tab-button {
    background: #1b8f3b;
    color: white;
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 8px;
    margin: 0 10px;
    cursor: pointer;
    transition: 0.3s;
}
.tab-button.active {
    background: #146328;
}
.tab-button:hover {
    background: #146328;
}

.tab-content { display: none; }
.tab-content.active { display: block; }

.cart-items {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
}
.cart-item {
    width: 260px;
    background: white;
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    text-align: center;
}
.cart-item img {
    width: 100%;
    height: 160px;
    border-radius: 10px;
    object-fit: cover;
}
.cart-item h3 {
    margin: 10px 0 5px;
}

.cart-total {
    font-size: 24px;
    font-weight: bold;
    color: #d02323;
    text-align: center;
    border: 3px solid #d02323;
    padding: 16px 40px;
    background: white;
    border-radius: 12px;
    margin: 30px auto;
    width: fit-content;
}

#checkout-form {
    width: 55%;
    background: white;
    padding: 30px;
    border-radius: 12px;
    margin: 20px auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
#checkout-form input,
#checkout-form textarea {
    width: 100%;
    padding: 15px;
    margin-top: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

/* Payment Options with Logos */
.payment-option {
    display: flex;
    align-items: center;
    gap: 10px; /* space between radio, image, text */
    margin: 10px 0;
    background: #f8f8f8;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid #ddd;
    cursor: pointer;
}

.payment-option:hover {
    background: #eee;
}

.payment-option img {
    height: 30px;  /* logo size */
    display: inline-block;
}

button[type="submit"] {
    background: #1b8f3b;
    padding: 15px;
    font-size: 18px;
    width: 100%;
    border-radius: 10px;
    border: none;
    color: white;
    cursor: pointer;
    margin-top: 18px;
}
button[type="submit"]:hover {
    background: #146328;
}
</style>
</head>

<body>

<header>Agro Farm Marketplace</header>

<nav>
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="marketplace.php">Marketplace</a></li>
        <li><a href="cart.php">My Cart</a></li>
        <li><a href="main.php?logout=true">Logout</a></li>
    </ul>
</nav>

<h2 style="text-align:center;">My Cart</h2>

<?php
if ($error) echo "<p style='color:red;text-align:center;'>$error</p>";
if ($success) echo "<p style='color:green;text-align:center;'>$success</p>";
?>

<div class="tabs">
    <button class="tab-button active" onclick="openTab('cart-tab')">🛒 Cart</button>
    <button class="tab-button" onclick="openTab('checkout-tab')">💳 Checkout</button>
</div>

<!-- CART TAB -->
<div id="cart-tab" class="tab-content active">
    <?php if ($cart_items): ?>
        <div class="cart-items">
            <?php foreach($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="uploads/<?php echo $item['product_image']; ?>">
                    <h3><?php echo $item['product_name']; ?></h3>
                    <p>Price: ৳<?php echo $item['price']; ?></p>
                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                    <p><b>Subtotal: ৳<?php echo $item['price'] * $item['quantity']; ?></b></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="cart-total">Total: ৳<?php echo $total_price; ?></div>
    <?php else: ?>
        <p style="text-align:center;">Your cart is empty.</p>
    <?php endif; ?>
</div>

<!-- CHECKOUT TAB -->
<div id="checkout-tab" class="tab-content">
<?php if ($cart_items): ?>
<form method="post" id="checkout-form">

    <h3>Delivery Information</h3>
    <textarea name="delivery_address" required placeholder="Enter your delivery address"></textarea>
    <input type="text" name="phone_number" required placeholder="Phone number (11 digits)" pattern="[0-9]{11}">

    <h3>Payment Method</h3>

    <label class="payment-option">
        <input type="radio" name="payment_method" value="bkash" onclick="showPaymentNumber(this.value)">
      
        <b>bKash</b>
    </label>

    <label class="payment-option">
        <input type="radio" name="payment_method" value="nagad" onclick="showPaymentNumber(this.value)">
       
        <b>Nagad</b>
    </label>

    <label class="payment-option">
        <input type="radio" name="payment_method" value="rocket" onclick="showPaymentNumber(this.value)">
        
        <b>Rocket</b>
    </label>

    <div id="payment-number" style="text-align:center;color:#c21e1e;font-size:18px;margin-top:10px;"></div>

    <input type="text" name="trx_id" required placeholder="Transaction ID">

    <?php foreach($cart_items as $item): ?>
        <input type="hidden" name="product_ids[]" value="<?php echo $item['product_id']; ?>">
        <input type="hidden" name="quantity[<?php echo $item['product_id']; ?>]" value="<?php echo $item['quantity']; ?>">
    <?php endforeach; ?>

    <button type="submit" name="confirm_order">Confirm Order</button>

</form>
<?php else: ?>
<p style="text-align:center;">Add items to your cart first.</p>
<?php endif; ?>
</div>

<script>
function openTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    event.currentTarget.classList.add('active');
}

function showPaymentNumber(method){
    let text = "";
    if(method === "bkash") text = "Send to bKash: 01745985077";
    if(method === "nagad") text = "Send to Nagad: 01313731493";
    if(method === "rocket") text = "Send to Rocket: 01745985077";

    document.getElementById("payment-number").innerHTML = text;
}
</script>

</body>
</html>








