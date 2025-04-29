<div class="sidebar">
  <h2>Admin Panel</h2>
  <ul>
    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="../available_stock/available_stock.html" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_suppliers.php' ? 'active' : ''; ?>"><i class="fas fa-boxes"></i> available stock</a></li>
    <li><a href="../my_order/my_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_customers.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> My Orders</a></li>
    <li><a href="logout.php" onclick="return confirm('Are you sure you want to log out?')"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</div>
