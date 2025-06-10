<?php
session_start();
include '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$supplier_id = $data['supplier_id'] ?? 0;

if (!$supplier_id) {
    echo json_encode(["success" => false, "message" => "Invalid supplier ID."]);
    exit();
}

// Unlock token
$stmt = $conn->prepare("UPDATE supplier_wallets SET token_status = 'unlocked', is_token_granted = 1 WHERE supplier_id = ?");
$stmt->bind_param("i", $supplier_id);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Token granted successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to grant token."]);
}
$stmt->close();
$conn->close();