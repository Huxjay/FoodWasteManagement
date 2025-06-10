<div class="sidebar">
  <h2>Supplier Pannel</h2>
  <ul>
    <li><a href="../dashboard/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="../foodstock/poststock.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_suppliers.php' ? 'active' : ''; ?>"><i class="fas fa-box-open"></i> Post Stock</a></li>
    <li><a href="../wallet/supplier_wallet.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'supplier_wallet.php' ? 'active' : ''; ?>"><i class="fas fa-box-open"></i>wallet</a></li>
    <li><a href="../view_orders/view_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_customers.php' ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> View Orders</a></li>
    <li><a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

</div>
