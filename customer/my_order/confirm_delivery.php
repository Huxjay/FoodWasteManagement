<?php
session_start();
include '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $order_id = $data['order_id'];

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
        echo json_encode(["success" => false, "message" => "Unauthorized"]);
        exit();
    }

    $sql = "UPDATE orders 
            SET status = 'Delivered', delivery_confirmed_by_customer = 1 
            WHERE order_id = ? AND customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Delivery confirmed."]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed."]);
    }

    $stmt->close();
    $conn->close();
}
?>
