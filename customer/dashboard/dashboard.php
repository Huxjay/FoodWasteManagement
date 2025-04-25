<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login/login.html");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
</head>
<body>
  <div class="sidebar">
    <h2>Customer</h2>
    <ul>
      <li><a href="">Dashboard</a></li>
      <li><a href="../available_stock/available_stock.html">Available Stock</a></li>
      <li><a href="../my_order/my_orders.php">My Orders</a></li>
      <li><a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">
        <button>Logout</button>
      </a>
      </li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Welcome to Your Dashboard</h1>
    <p>View stock, manage orders, and more.</p>
  </div>
</body>
</html>
