<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get filter values from URL
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$where = "";
if ($from_date && $to_date) {
    $from = $mysqli->real_escape_string($from_date);
    $to = $mysqli->real_escape_string($to_date);
    $where = "WHERE o.order_date BETWEEN '$from' AND '$to'";
}

// SQL to get orders
$sql = "
SELECT o.order_id, u.name AS customer_name, f.food_type, o.quantity_kg, o.total_price, o.order_date, o.status
FROM orders o
JOIN users u ON o.customer_id = u.id
JOIN foodstock f ON o.stock_id = f.stock_id
$where
ORDER BY o.order_date DESC
";

$result = $mysqli->query($sql);

// Set headers to force download as Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=order_report.xls");

// Output column headers
echo "Order ID\tCustomer\tFood Type\tQuantity (kg)\tTotal Price\tOrder Date\tStatus\n";

// Output rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo $row['order_id'] . "\t" .
             $row['customer_name'] . "\t" .
             $row['food_type'] . "\t" .
             number_format($row['quantity_kg'], 2) . "\t" .
             number_format($row['total_price'], 2) . "\t" .
             $row['order_date'] . "\t" .
             ucfirst($row['status']) . "\n";
    }
} else {
    echo "No data available\n";
}

$mysqli->close();
?>