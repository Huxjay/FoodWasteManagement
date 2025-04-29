<!-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Supplier Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <h2>Supplier Panel</h2>
      <ul>
        <li><a href="dashboard.html" class="active">Dashboard</a></li>
        <li><a href="../foodstock/post_stock.html">Post Stock</a></li>
        <li><a href="../view_orders/view_orders.html">View Orders</a></li>
        <li><a href="../dashboard/logout.php" onclick="return confirm('Are you sure you want to log out?');">
          <button>Logout</button>
        </a></li>
      </ul>
    </aside>

    <main class="main-content">
      <h1>Welcome Supplier</h1>
      <p>This is your dashboard. Use the menu to manage stock and view orders.</p>
    </main>
  </div>

  <script src="dashboard.js"></script>
</body>
</html> -->


<?php
include_once("db_config.php");
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
</head>
<body>
  <div class="container">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
      <h1>Welcome, Spplier</h1>
      <p>Use the dashboard to manage the system efficiently.</p>

      <div class="dashboard-cards">
        <div class="card">
        <i class="fas fa-box-open"></i>
        <h3>Post Stock</h3>
          <a href="../foodstock/post_stock.html">View</a>
        </div>

        <div class="card">
        <i class="fas fa-receipt"></i>
        <h3>View Orders</h3>
          <a href="../view_orders/view_orders.html">View</a>
        </div>

        
      </div>

      <script src="dashboard.js"></script>

      <footer>
        © 2025 Food Waste Management System | Contact Admin: admin@example.com
      </footer>
    </div>
  </div>


</body>
</html>

