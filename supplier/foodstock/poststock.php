<?php 
session_start();
include_once("../../db_config.php");

// 🚨 Block caching (prevents access via back button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// 🚨 Enforce login and correct role
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
  <title>Post Stock</title>
  <link rel="stylesheet" href="post_stock.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
  
<div class="container"></div>
    <?php include '../sidebar.php'; ?>

  <div class="main-content">
    <div class="card">
      <h1><i class="fas fa-box-open"></i> Post New Stock</h1>
      <form id="stockForm">
        <input type="text" id="food_type" placeholder="Food Name" required />
        <input type="number" id="quantity_kg" step="0.01" placeholder="Quantity (kg)" required />
        <input type="number" id="price" step="0.01" placeholder="Price per kg" required />
        <!-- Location field optional if set in session -->
        <button type="submit">Post Stock</button>
      </form>
      <div id="message"></div>
    </div>
  </div>
</div>
  <script src="post_stock.js"></script>
</body>
</html>
