<?php
require 'db_config.php';

$customer_id = $_GET['customer_id'];

$sql = "SELECT o.order_id, f.food_type, f.quantity_kg, f.price, o.order_date, o.status
        FROM orders o
        JOIN foodstock f ON o.stock_id = f.stock_id
        WHERE o.customer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);

$stmt->close();
$conn->close();
?>
