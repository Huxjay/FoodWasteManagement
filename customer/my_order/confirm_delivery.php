<?php
session_start();
include '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $order_id = $data['order_id'];

    // Validate session
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
        echo json_encode(["success" => false, "message" => "Unauthorized"]);
        exit();
    }

    $sql = "UPDATE orders SET status = 'Delivered' WHERE order_id = ? AND customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Order marked as Delivered. Payment will now be released."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update order status."]);
    }

    $stmt->close();
    $conn->close();
}
?>