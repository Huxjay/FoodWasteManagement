<?php 
session_start();
include_once("../../db_config.php");

// 🚨 Block caching (prevents access via back button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// 🚨 Enforce login and correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../../login/login.php");
    exit();
}

// ⏳ Auto logout after 15 minutes of inactivity
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Custumer Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f2f2f2;
}

.container {
  display: flex;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  padding: 40px;
  display: flex;
  flex-direction: column;
}

h1 {
  margin-top: 0;
}

.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-top: 40px;
}

.card {
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
  transition: transform 0.2s ease;
}

.card:hover {
  transform: translateY(-5px);
}

.card i {
  font-size: 40px;
  color: #2c3e50;
  margin-bottom: 10px;
}

.card h3 {
  margin: 10px 0;
}

.card a {
  display: inline-block;
  margin-top: 10px;
  text-decoration: none;
  color: #3498db;
  font-weight: bold;
}

footer {
  margin-top: auto;
  text-align: center;
  padding: 10px 0;
  color: #777;
  font-size: 14px;
}

  </style>

</head>
<script>
// Auto logout after 15 minutes of inactivity (900000 ms)
let timeout;

function resetTimer() {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    alert("You were logged out due to inactivity.");
    window.location.href = "../../logout.php";
  }, 900000); // 15 minutes = 900,000 ms
}

window.onload = resetTimer;
document.onmousemove = resetTimer;
document.onkeypress = resetTimer;
document.onscroll = resetTimer;
document.onclick = resetTimer;
</script>

<body>
  <div class="container">
    <?php include '../sidebar.php'; ?>

    <div class="main-content">
      <h1>Welcome, Customer</h1>
      <p>Use the dashboard to manage the system efficiently.</p>
      
      <div class="dashboard-cards">
        <div class="card">
          <i class="fas fas fa-boxes"></i>
          <h3>Available Stock</h3>
          <a href="../available_stock/available_stock.php">View</a>
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

