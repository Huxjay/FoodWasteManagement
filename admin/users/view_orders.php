<?php
// admin/orders/view_orders.php
session_start();
require __DIR__ . '/../../db_config.php';

// Security: only admin may view
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.html");
    exit();
}

// Fetch all orders with customer & supplier info
$sql = "
  SELECT 
    o.order_id,
    c.name AS customer_name,
    s.name AS supplier_name,
    f.food_type,
    o.quantity_kg,
    o.total_price,
    o.order_date,
    o.status
  FROM orders o
  JOIN customer c   ON o.customer_id = c.customer_id
  JOIN foodstock f  ON o.stock_id    = f.stock_id
  JOIN supplier s   ON f.supplier_id = s.supplier_id
  ORDER BY o.order_date DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Orders</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:20px; }
    h2 { text-align:center; margin-bottom:20px; }
    table { width:90%; margin:auto; border-collapse:collapse; background:#fff; }
    th, td { padding:12px; border:1px solid #ccc; text-align:center; }
    th { background:#2c3e50; color:#fff; }
    tr:nth-child(even){ background:#f9f9f9; }
    .status-Pending   { color:#e67e22; font-weight:bold; }
    .status-Confirmed { color:#27ae60; font-weight:bold; }
    .status-Cancelled { color:#c0392b; font-weight:bold; }
  </style>
</head>
<body>
  <h2>All Orders</h2>
  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Supplier</th>
        <th>Food Type</th>
        <th>Quantity (kg)</th>
        <th>Total Price</th>
        <th>Order Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows): ?>
        <?php while($o = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $o['order_id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= htmlspecialchars($o['supplier_name']) ?></td>
            <td><?= htmlspecialchars($o['food_type']) ?></td>
            <td><?= number_format($o['quantity_kg'],2) ?></td>
            <td><?= number_format($o['total_price'],2) ?></td>
            <td><?= $o['order_date'] ?></td>
            <td class="status-<?= $o['status'] ?>">
              <?= ucfirst($o['status']) ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8">No orders found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
