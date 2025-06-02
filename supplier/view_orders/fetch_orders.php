<?php
session_start();
header('Content-Type: application/json');
include_once("../../db_config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// SQL to fetch orders joined with customers
$supplier_id = $_SESSION['user_id'];

$sql = "SELECT o.order_id, u.name AS customer_name, fs.food_type, o.quantity_kg, o.total_price, o.order_date, o.status, o.payment_status
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        JOIN foodstock fs ON o.stock_id = fs.stock_id
        WHERE fs.supplier_id = ?
        ORDER BY o.order_date DESC";

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