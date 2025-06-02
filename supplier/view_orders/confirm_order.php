<?php
session_start();
include_once("../../db_config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data['order_id'];

$stmt = $conn->prepare("UPDATE orders SET status = 'Confirmed' WHERE order_id = ?");
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>
