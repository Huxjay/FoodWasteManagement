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
      <h1>Welcome, Admin</h1>
      <p>Use the dashboard to manage the system efficiently.</p>

      <div class="dashboard-cards">
        <div class="card">
          <i class="fas fa-user-tie"></i>
          <h3>Manage Suppliers</h3>
          <a href="../users/manage_suppliers.php">View</a>
        </div>

        <div class="card">
          <i class="fas fa-users"></i>
          <h3>Manage Customers</h3>
          <a href="../users/manage_customers.php">View</a>
        </div>

        <div class="card">
          <i class="fas fa-shopping-cart"></i>
          <h3>View Orders</h3>
          <a href="../users/view_orders.php">View</a>
        </div>

        <div class="card">
          <i class="fas fa-chart-line"></i>
          <h3>Reports</h3>
          <a href="../users/report.php">View</a>
        </div>
      </div>

      <footer>
        © 2025 Food Waste Management System | Contact Admin: admin@example.com
      </footer>
    </div>
  </div>
</body>
</html>

