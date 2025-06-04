<?php
session_start();
include_once("../../db_config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$customer_id = $_SESSION['user_id'];

$query = "SELECT 
            o.order_id,
            f.food_type,
            o.quantity_kg,
            o.total_price,
            o.order_date,
            o.status,
            o.delivery_confirmed_by_customer
          FROM orders o
          INNER JOIN foodstock f ON o.stock_id = f.stock_id
          WHERE o.customer_id = ?
          ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = [
        "order_id" => $row["order_id"],
        "food_type" => $row["food_type"],
        "quantity_kg" => $row["quantity_kg"],
        "total_price" => $row["total_price"],
        "order_date" => $row["order_date"],
        "status" => $row["status"],
        "delivery_confirmed_by_customer" => $row["delivery_confirmed_by_customer"]
    ];
}

header('Content-Type: application/json');
echo json_encode($orders);
?>
