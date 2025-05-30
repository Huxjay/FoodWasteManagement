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
      <h1>Welcome, Customer</h1>
      <p>Use the dashboard to manage the system efficiently.</p>

      <div class="dashboard-cards">
        <div class="card">
          <i class="fas fas fa-boxes"></i>
          <h3>Available Stock</h3>
          <a href="../available_stock/available_stock.html">View</a>
        </div>

        <div class="card">
          <i class="fas fa-shopping-cart"></i>
          <h3>My orders</h3>
          <a href="../my_order/my_orders.php">View</a>
        </div>

        
      </div>

      <footer>
        © 2025 Food Waste Management System | Contact Admin: admin@example.com
      </footer>
    </div>
  </div>


</body>
</html>

