<?php
session_start();
require 'db_config.php';

$supplier_id = $_SESSION['supplier_id']; // ensure supplier is logged in

$sql = "SELECT o.order_id, o.customer_id, o.status, fs.food_type, fs.quantity_kg, fs.price
        FROM orders o
        JOIN foodstock fs ON o.stock_id = fs.stock_id
        WHERE fs.supplier_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
  $orders[] = $row;
}

echo json_encode($orders);
?>
