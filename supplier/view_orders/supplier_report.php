<?php
session_start();
include_once("../../db_config.php");

// 🚫 Block access if not logged in as supplier
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    header("Location: ../../login/login.php");
    exit();
}

// Auto logout after 15 minutes
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

$supplier_id = $_SESSION['user_id'];
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$date_filter = "";
if ($from_date && $to_date) {
    $date_filter = "AND o.order_date BETWEEN '$from_date' AND '$to_date'";
}

// Summary
$summary_sql = "
    SELECT 
        COUNT(*) AS total_orders,
        SUM(o.quantity_kg) AS total_quantity,
        SUM(o.total_price) AS total_sales,
        SUM(CASE WHEN o.status = 'Pending' THEN 1 ELSE 0 END) AS pending_orders,
        SUM(CASE WHEN o.status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_orders
    FROM orders o
    JOIN foodstock f ON o.stock_id = f.stock_id
    WHERE f.supplier_id = $supplier_id $date_filter
";
$summary_result = $conn->query($summary_sql);
$summary = $summary_result->fetch_assoc();

// Orders
$order_sql = "
    SELECT 
        o.order_id,
        o.quantity_kg,
        o.total_price,
        o.order_date,
        o.status,
        c.name AS customer_name,
        f.food_type
    FROM orders o
    JOIN foodstock f ON o.stock_id = f.stock_id
    JOIN users c ON o.customer_id = c.id
    WHERE f.supplier_id = $supplier_id $date_filter
    ORDER BY o.order_date DESC
";
$orders_result = $conn->query($order_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Supplier Report</title>
    <style>
        body { font-family: Arial; background:#f5f6fa; padding:20px; }
        h2 { text-align: center; }
        .summary { display: flex; justify-content: space-around; margin-bottom: 30px; }
        .summary div { background:#fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; width: 20%; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        tr:nth-child(even) { background: #f0f0f0; }
        form, .export-buttons { text-align: center; margin: 20px 0; }
        .export-buttons a button { margin: 0 10px; padding: 10px 15px; background: #27ae60; color: white; border: none; cursor: pointer; border-radius: 5px; }
        .export-buttons a button:hover { background: #219150; }
    </style>
</head>
<body>

<h2>Supplier Activity Report</h2>

<form method="GET">
    <label>From: <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>"></label>
    <label>To: <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>"></label>
    <button type="submit">Filter</button>
</form>

<div class="export-buttons">
    <a href="export_supplier_pdf.php?from_date=<?= urlencode($from_date) ?>&to_date=<?= urlencode($to_date) ?>" target="_blank">
        <button>Download PDF</button>
    </a>
    <a href="export_supplier_excel.php?from_date=<?= urlencode($from_date) ?>&to_date=<?= urlencode($to_date) ?>">
        <button>Download Excel</button>
    </a>
</div>

<div class="summary">
    <div><h3>Total Orders</h3><p><?= $summary['total_orders'] ?? 0 ?></p></div>
    <div><h3>Total Quantity (kg)</h3><p><?= number_format($summary['total_quantity'] ?? 0, 2) ?></p></div>
    <div><h3>Total Sales (₱)</h3><p><?= number_format($summary['total_sales'] ?? 0, 2) ?></p></div>
    <div><h3>Pending / Confirmed</h3><p><?= $summary['pending_orders'] ?? 0 ?> / <?= $summary['confirmed_orders'] ?? 0 ?></p></div>
</div>

<h3 style="text-align:center;">Orders List</h3>
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
        <?php if ($orders_result->num_rows > 0): ?>
            <?php while($order = $orders_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['order_id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['food_type']) ?></td>
                    <td><?= number_format($order['quantity_kg'], 2) ?></td>
                    <td><?= number_format($order['total_price'], 2) ?></td>
                    <td><?= $order['order_date'] ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No orders found for selected date range.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>