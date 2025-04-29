<?php
$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$where = "";
if ($from_date && $to_date) {
    $where = "WHERE o.order_date BETWEEN '$from_date' AND '$to_date'";
}

// Fetch orders
$sql = "
SELECT o.order_id, c.name AS customer_name, f.food_type, o.quantity_kg, o.total_price, o.order_date, o.status
FROM orders o
JOIN customer c ON o.customer_id = c.customer_id
JOIN foodstock f ON o.stock_id = f.stock_id
$where
ORDER BY o.order_date DESC
";
$result = $mysqli->query($sql);

// Set header
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=order_report.xls");

echo "Order ID\tCustomer\tFood Type\tQuantity (kg)\tTotal Price\tOrder Date\tStatus\n";

while($row = $result->fetch_assoc()) {
    echo $row['order_id'] . "\t" . 
         $row['customer_name'] . "\t" . 
         $row['food_type'] . "\t" . 
         $row['quantity_kg'] . "\t" . 
         number_format($row['total_price'],2) . "\t" . 
         $row['order_date'] . "\t" . 
         $row['status'] . "\n";
}

$mysqli->close();
?>
