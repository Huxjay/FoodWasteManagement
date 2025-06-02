<div class="sidebar">
  <h2>Admin Panel</h2>
  <ul>
    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="../users/manage_suppliers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_suppliers.php' ? 'active' : ''; ?>"><i class="fas fa-user-tie"></i> Manage Suppliers</a></li>
    <li><a href="../users/manage_customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_customers.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Manage Customers</a></li>
    <li><a href="../users/view_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_orders.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> View Orders</a></li>
    <li><a href="../users/report.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Reports</a></li>
    <li><a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

  </ul>
</div>
