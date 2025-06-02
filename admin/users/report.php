<?php 
session_start();
include_once("../../db_config.php");

// 🚨 Block caching (prevents access via back button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// 🚨 Enforce login and correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
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


// Summary Data
$summary_sql = "
  SELECT 
    COUNT(*) AS total_orders,
    SUM(quantity_kg) AS total_quantity,
    SUM(total_price) AS total_sales,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_orders,
    SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_orders
  FROM orders
";
$summary_result = $conn->query($summary_sql);
$summary = $summary_result->fetch_assoc();

// Orders List
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$where = "";

if ($from_date && $to_date) {
    $where = "WHERE o.order_date BETWEEN '$from_date' AND '$to_date'";
}

// Now use $where in the SQL
$orders_sql = "
  SELECT 
    o.order_id, 
    c.name AS customer_name, 
    f.food_type, 
    o.quantity_kg, 
    o.total_price, 
    o.order_date, 
    o.status
  FROM orders o
  JOIN customer c ON o.customer_id = c.customer_id
  JOIN foodstock f ON o.stock_id = f.stock_id
  $where
  ORDER BY o.order_date DESC
";
$orders_result = $conn->query($orders_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports</title>
  <style>
    body { font-family: Arial, sans-serif; background:#eef2f3; margin:20px; }
    h2 { text-align:center; }
    .summary { display: flex; justify-content: space-around; margin-bottom:30px; }
    .summary div { background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); width: 20%; text-align: center; }
    .summary div h3 { margin-bottom:10px; color:#333; }
    table { width:90%; margin:auto; border-collapse:collapse; background:#fff; }
    th, td { padding:12px; text-align:center; border:1px solid #ccc; }
    th { background:#2980b9; color:white; }
    tr:nth-child(even){ background:#f9f9f9; }
  </style>
</head>
<body>

<h2>Admin Reports</h2>

<form method="GET" style="text-align:center; margin-bottom:30px;">
    <label>From: <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>"></label>
    <label>To: <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>"></label>
    <button type="submit">Filter</button>
</form>

<div style="text-align:center; margin-bottom:30px;">
    <a href="export_pdf.php?from_date=<?= $_GET['from_date'] ?? '' ?>&to_date=<?= $_GET['to_date'] ?? '' ?>" target="_blank">
        <button type="button">Download PDF</button>
    </a>
    <a href="export_excel.php?from_date=<?= $_GET['from_date'] ?? '' ?>&to_date=<?= $_GET['to_date'] ?? '' ?>">
        <button type="button">Download Excel</button>
    </a>
</div>


<div class="summary">
  <div>
    <h3>Total Orders</h3>
    <p><?= $summary['total_orders'] ?? 0 ?></p>
  </div>
  <div>
    <h3>Total Quantity (kg)</h3>
    <p><?= number_format($summary['total_quantity'] ?? 0, 2) ?></p>
  </div>
  <div>
    <h3>Total Sales (₱)</h3>
    <p><?= number_format($summary['total_sales'] ?? 0, 2) ?></p>
  </div>
  <div>
    <h3>Pending / Confirmed</h3>
    <p><?= $summary['pending_orders'] ?? 0 ?> / <?= $summary['confirmed_orders'] ?? 0 ?></p>
  </div>
</div>

<h3 style="text-align:center;">All Orders</h3>
<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Customer</th>
      <th>Food Type</th>
      <th>Quantity (kg)</th>
      <th>Total Price</th>
      <th>Order Date</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($orders_result->num_rows): ?>
      <?php while($order = $orders_result->fetch_assoc()): ?>
        <tr>
          <td><?= $order['order_id'] ?></td>
          <td><?= htmlspecialchars($order['customer_name']) ?></td>
          <td><?= htmlspecialchars($order['food_type']) ?></td>
          <td><?= number_format($order['quantity_kg'],2) ?></td>
          <td><?= number_format($order['total_price'],2) ?></td>
          <td><?= $order['order_date'] ?></td>
          <td><?= ucfirst($order['status']) ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="7">No orders found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<h3 style="text-align:center;">Top 5 Customers</h3>
<table>
  <thead>
    <tr>
      <th>Customer Name</th>
      <th>Total Orders</th>
    </tr>
  </thead>
  <tbody>
<?php
$top_customers_sql = "
  SELECT c.name, COUNT(o.order_id) AS order_count
  FROM orders o
  JOIN customer c ON o.customer_id = c.customer_id
  GROUP BY o.customer_id
  ORDER BY order_count DESC
  LIMIT 5
";
$top_customers_result = $conn->query($top_customers_sql);

if ($top_customers_result->num_rows):
    while($row = $top_customers_result->fetch_assoc()):
?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['order_count'] ?></td>
    </tr>
<?php endwhile; else: ?>
    <tr><td colspan="2">No data found.</td></tr>
<?php endif; ?>
  </tbody>
</table>

<h3 style="text-align:center;">Top 5 Selling Food Types</h3>
<table>
  <thead>
    <tr>
      <th>Food Type</th>
      <th>Quantity Sold (kg)</th>
    </tr>
  </thead>
  <tbody>
<?php
$top_food_sql = "
  SELECT f.food_type, SUM(o.quantity_kg) AS total_sold
  FROM orders o
  JOIN foodstock f ON o.stock_id = f.stock_id
  GROUP BY o.stock_id
  ORDER BY total_sold DESC
  LIMIT 5
";
$top_food_result = $conn->query($top_food_sql);

if ($top_food_result->num_rows):
    while($row = $top_food_result->fetch_assoc()):
?>
    <tr>
      <td><?= htmlspecialchars($row['food_type']) ?></td>
      <td><?= number_format($row['total_sold'],2) ?></td>
    </tr>
<?php endwhile; else: ?>
    <tr><td colspan="2">No data found.</td></tr>
<?php endif; ?>
  </tbody>
</table>


</body>
</html>
