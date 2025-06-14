<?php
session_start();

// ✅ Ensure supplier is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    die("Unauthorized access.");
}

$conn = new mysqli("localhost", "root", "", "foodwastemanagement");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$supplier_id = $_SESSION['user_id'];
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$where = "WHERE f.supplier_id = $supplier_id";
if ($from_date && $to_date) {
    $where .= " AND o.order_date BETWEEN '$from_date' AND '$to_date'";
}

$sql = "
SELECT o.order_id, u.name AS customer_name, f.food_type, o.quantity_kg, o.total_price, o.order_date, o.status
FROM orders o
JOIN foodstock f ON o.stock_id = f.stock_id
JOIN users u ON o.customer_id = u.id
$where
ORDER BY o.order_date DESC
";

$result = $conn->query($sql);

// ✅ Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=supplier_order_report.xls");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ Output Excel table
echo "<table border='1'>";
echo "<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Food Type</th>
    <th>Quantity (kg)</th>
    <th>Total Price</th>
    <th>Order Date</th>
    <th>Status</th>
</tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['order_id']}</td>
            <td>{$row['customer_name']}</td>
            <td>{$row['food_type']}</td>
            <td>{$row['quantity_kg']}</td>
            <td>" . number_format($row['total_price'], 2) . "</td>
            <td>{$row['order_date']}</td>
            <td>" . ucfirst($row['status']) . "</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No orders found</td></tr>";
}

echo "</table>";
exit;
?>