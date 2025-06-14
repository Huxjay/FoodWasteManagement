<?php 
session_start();
include_once("../../db_config.php");

// Block caching (prevents access via back button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// Enforce login and correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
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
  <title>Supplier Dashboard</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
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
      <h1>Welcome, Supplier</h1>
      <p>Use the dashboard to manage the system efficiently.</p>

      <div class="dashboard-cards">
        <div class="card">
          <i class="fas fa-box-open"></i>
          <h3>Post Stock</h3>
          <a href="../foodstock/poststock.php">View</a>
        </div>

        <div class="card">
          <i class="fas fa-receipt"></i>
          <h3>View Orders</h3>
          <a href="../view_orders/view_orders.php">View</a>
        </div>

        <div class="card">
          <i class="fas fa-receipt"></i>
          <h3>wallet</h3>
          <a href="../wallet/supplier_wallet.php">View</a>
        </div>


        <div class="card">
          <i class="fas fa-receipt"></i>
          <h3>report</h3>
          <a href="../view_orders/supplier_report.php">View</a>
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
