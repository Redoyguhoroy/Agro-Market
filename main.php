<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Log out
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agro Farm Management For Local Farmer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Agro Farm Management For Local Farmer</h1>
        <nav>
            <ul>
                <li><a href="home.php"><img src="icons/home.png" alt="Home" class="icon"> Home</a></li>
                <?php if ($role == 'farmer') { ?>
                    <li><a href="manage_farm.php"><img src="icons/manage_farm.png" alt="Manage Farm" class="icon"> Manage Farm</a></li>
                <?php } ?>
                <li><a href="marketplace.php"><img src="icons/marketplace.png" alt="Marketplace" class="icon"> Marketplace</a></li>
                <li><a href="cart.php"><img src="icons/cart.png" alt="My Cart" class="icon"> My Cart</a></li>
                <li><a href="main.php?logout=true"><img src="icons/exit.png" alt="Log Out" class="icon"> Log Out</a></li>
            </ul>
        </nav>
    </header>
   


        </div>
        </div>
    </div>
                </main>
</body>
</html>
