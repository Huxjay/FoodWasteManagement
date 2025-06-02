

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
.sidebar {
  width: 220px;
  background-color: #2c3e50;
  color: white;
  height: 100vh;
  padding: 20px;
}

.sidebar h2 {
  margin-bottom: 20px;
  font-size: 24px;
}

.sidebar ul {
  list-style: none;
  padding: 0;
}

.sidebar ul li {
  margin: 15px 0;
}

.sidebar ul li a {
  color: white;
  text-decoration: none;
  display: block;
}

.sidebar ul li a:hover,
.sidebar ul li a.active {
  background-color: #34495e;
  padding: 8px;
  border-radius: 5px;
}</style>
</head>
<body>
  
</body>
</html>

<div class="sidebar">
  <h2>customer Panel</h2>
  <ul>
    <li><a href="../dashboard/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="../available_stock/available_stock.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_suppliers.php' ? 'active' : ''; ?>"><i class="fas fa-boxes"></i> available stock</a></li>
    <li><a href="../my_order/my_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_customers.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> My Orders</a></li>
        <li><a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

  </ul>
</div>
