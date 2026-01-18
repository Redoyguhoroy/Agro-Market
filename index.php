
<?php 
session_start();

// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "agfms");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// USER REGISTRATION
if (isset($_POST["register"])) {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $role = $_POST["role"];


    // Check if username already exists
    $check_sql = "SELECT * FROM users WHERE username='$username'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('Username already exists! Please choose another.');</script>";
    } else {
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration Successful! Your username is: $username');</script>";
        } else {
            echo "<script>alert('Error: Could not register user.');</script>";
        }
    }
}

// USER LOGIN
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["role"] = $user["role"];
            
            // Clear POST data
            $_POST['username'] = '';
            $_POST['password'] = '';
            
            header("Location: main.php");
            exit;
        } else {
            echo "<script>alert('Invalid Password!');</script>";
            // Clear password field
            $_POST['password'] = '';
        }
    } else {
        echo "<script>alert('User Not Found!');</script>";
        // Clear username and password fields
        $_POST['username'] = '';
        $_POST['password'] = '';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agro Marketplace</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f0f7f0;
        }

        /* NAVBAR */
        nav {
            background: #2e7d32;
            padding: 15px 40px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
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
            width: 55px;
            margin-right: 10px;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 35px;
            font-size: 17px;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: 0.2s;
        }

        nav ul li a:hover {
            color: #c5ffd0;
        }

        /* HERO SECTION */
        .hero {
            width: 100%;
            background: url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6') center/cover;
            padding: 140px 50px;
            color: white;
            text-shadow: 1px 1px 10px #000;
        }

        .hero h1 {
            font-size: 52px;
            font-weight: 700;
            max-width: 650px;
        }

        .hero p {
            font-size: 22px;
            max-width: 500px;
            margin-top: 10px;
        }

        /* FORMS */
        .container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 60px auto;
            max-width: 1000px;
        }

        .box {
            background: white;
            width: 45%;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            animation: fadeIn 0.5s ease;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            font-size: 24px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 18px;
            background: #2e7d32;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.25s;
        }

        button:hover {
            background: #256428;
            transform: scale(1.03);
        }

        /* GRID SECTION STYLES */
        .section-title {
            font-size: 32px;
            font-weight: 700;
            text-align: center;
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .grid-container {
            max-width: 1100px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-top: 35px;
        }

        .grid-item {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
            transition: 0.3s;
        }

        .grid-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 28px rgba(0,0,0,0.18);
        }

        .grid-item h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #2e7d32;
        }

        .grid-item p {
            color: #444;
            font-size: 15px;
        }

        /* Footer */
        footer {
            margin-top: 70px;
            background: #2e7d32;
            color: white;
            text-align: center;
            padding: 22px;
            font-size: 16px;
            letter-spacing: .5px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px);} 
            to { opacity: 1; transform: translateY(0);} 
        }

        @media(max-width: 768px) {
            .container { flex-direction: column; width: 90%; }
            .box { width: 100%; }
            nav ul { display: none; }
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
            <li><a href="#home">HOME</a></li>
            <li><a href="#about">ABOUT US</a></li>
            <li><a href="#produce">PRODUCE</a></li>
            <li><a href="#Features">FEATURES</a></li>
        </ul>
    </nav>

<!-- HERO -->
<section class="hero" id="home">
    <h1>Connecting Farmers & Buyers in a Modern Digital Marketplace</h1>
    <p style="color:#1E3A8A;">Buy fresh, local, and organic produce directly from trusted farmers across Bangladesh.</p>
</section>


    


    <!-- FORMS -->
    <div class="container">
        
        <!-- REGISTER -->
        <div class="box">
            <h2>Create Account</h2>
            <form method="post">
                <input type="text" name="username" placeholder="Enter Username" required>
                <input type="password" name="password" placeholder="Create Password" required>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="farmer">Farmer - I want to sell my product</option>
                    <option value="buyer">Buyer - I want to buy fresh product</option>
                </select>
                <button type="submit" name="register">Register</button>
            </form>
        </div>

        <!-- LOGIN -->
        <div class="box">
            <h2>Login</h2>
            <form method="post">
                <input type="text" name="username" placeholder="Your Username" required>
                <input type="password" name="password" placeholder="Your Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>

    </div>


    <!-- ABOUT US SECTION -->
    <section id="about" style="padding:60px 40px; background:#ffffff;">
        <h2 style="color:#2e7d32; text-align:center; font-size:32px;">About Us</h2>
        <p style="max-width:800px; margin:auto; font-size:18px; text-align:center; margin-top:15px;">
            Agro Market is a digital marketplace that connects local farmers with buyers.
            Our mission is to support farmers, reduce middlemen, and provide fresh produce
            directly to consumers across Bangladesh. We ensure transparency, fair pricing,
            and fast delivery through our smart platform.
        </p>
    </section>


<!-- PRODUCE SECTION -->
<section id="produce" style="padding:60px 40px; background:#f3f9f3;">
    <h2 style="color:#2e7d32; text-align:center; font-size:32px;">Popular Produce</h2>

    <div style="max-width:1000px; margin:auto; display:flex; gap:25px; justify-content:center; flex-wrap:wrap; margin-top:25px;">

        <!-- Fresh Vegetables -->
        <div style="background:white; width:260px; padding:20px; border-radius:15px;
                    box-shadow:0 4px 10px rgba(0,0,0,0.1); text-align:center;">
            <img src="https://media.istockphoto.com/id/1781830558/photo/fresh-vegetable-in-village-market.jpg?s=1024x1024&w=is&k=20&c=IrzgCtT3j5gec8g-PVjmym7xyegDGqX2XwgHwww92gs="
                 style="width:100%; border-radius:12px;" alt="Fresh Vegetables">
            <h3>Fresh Vegetables</h3>
            <p>Locally grown vegetables delivered to your doorstep.</p>
        </div>

        <!-- Seasonal Fruits -->
        <div style="background:white; width:260px; padding:20px; border-radius:15px;
                    box-shadow:0 4px 10px rgba(0,0,0,0.1); text-align:center;">
            <img src="https://dscdn.daily-sun.com/english/uploads/news_photos/2023/06/22/DS-27-22-06-2023.jpg"
                 style="width:100%; border-radius:12px;" alt="Seasonal Fruits">
            <h3>Seasonal Fruits</h3>
            <p>Fresh mangoes, bananas, jackfruit, strawberries, and more.</p>
        </div>

        <!-- Organic Rice -->
        <div style="background:white; width:260px; padding:20px; border-radius:15px;
                    box-shadow:0 4px 10px rgba(0,0,0,0.1); text-align:center;">
            <img src="https://chaldn.com/_mpimage/banglamoti-rice-boiled-1-kg?src=https%3A%2F%2Feggyolk.chaldal.com%2Fapi%2FPicture%2FRaw%3FpictureId%3D72051&q=best&v=1&m=400&webp=1"
                 style="width:100%; border-radius:12px;" alt="Organic Rice">
            <h3>Organic Rice</h3>
            <p>Pure and naturally grown rice varieties.</p>
        </div>

    </div>
</section>




    <!-- 🌾 AGRO MARKETPLACE FEATURES (GRID LAYOUT) -->
<section id="Features" style="padding:60px 40px; background:#ffffff;">


    <h2 class="section-title">Agro Marketplace Features</h2>

    <!-- 🔹 SECTION 1 -->
    <h2 style="color:#2e7d32; margin-top:40px; font-size:26px;">Farm-to-Consumer</h2>
    <div class="grid-container">
        <div class="grid-item">
            <h3>Fresh Vegetables</h3>
            <p>Directly sourced from local farmers to ensure freshness and quality.</p>
        </div>
        <div class="grid-item">
            <h3>Seasonal Fruits</h3>
            <p>Get seasonal fruits harvested at peak ripeness from trusted farmers.</p>
        </div>
        <div class="grid-item">
            <h3>Organic Rice</h3>
            <p>Pure, naturally grown rice varieties delivered to your home.</p>
        </div>
        <div class="grid-item">
            <h3>Dairy Products</h3>
            <p>Fresh milk, yogurt, and cheese directly from farmers’ farms.</p>
        </div>
    </div>

    <!-- 🔹 SECTION 2 -->
    <h2 style="color:#2e7d32; margin-top:40px; font-size:26px;">Secure Transactions</h2>
    <div class="grid-container">
        <div class="grid-item">
            <h3>Mobile Payments</h3>
            <p>Pay easily using bKash, Nagad, or Rocket with complete security.</p>
        </div>
        <div class="grid-item">
            <h3>Transparent Pricing</h3>
            <p>Fair pricing with no hidden charges ensures trust between farmers and buyers.</p>
        </div>
        <div class="grid-item">
            <h3>Order Tracking</h3>
            <p>Track your orders from the farm to your doorstep.</p>
        </div>
    </div>

    <!-- 🔹 SECTION 3 -->
    <h2 style="color:#2e7d32; margin-top:40px; font-size:26px;">Support Local Farmers</h2>
    <div class="grid-container">
        <div class="grid-item">
            <h3>Boost Farmer Income</h3>
            <p>Helping local farmers sell more by reducing middlemen.</p>
        </div>
        <div class="grid-item">
            <h3>Promote Sustainable Farming</h3>
            <p>Encourage organic and eco-friendly farming practices.</p>
        </div>
        <div class="grid-item">
            <h3>Community Growth</h3>
            <p>Strengthen the local economy and build a strong agricultural community.</p>
        </div>
    </div>

</section>



    <!-- FOOTER -->
    <footer>
        © 2025 Agro Farm Marketplace • Green University of Bangladesh
    </footer>

</body>
</html>
