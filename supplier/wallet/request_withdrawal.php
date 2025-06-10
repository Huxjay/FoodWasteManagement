<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../db_config.php'; // Adjust path if needed

// 🚨 Verify request method and user role
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$supplier_id = $_SESSION['user_id'];

// 🔍 Check if supplier_wallet exists
$exists_stmt = $conn->prepare("SELECT balance,token_status FROM supplier_wallets WHERE supplier_id = ?");
$exists_stmt->bind_param("i", $supplier_id);
$exists_stmt->execute();
$exists_stmt->store_result();

if ($exists_stmt->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Wallet not found for this supplier."]);
    exit();
}

$exists_stmt->bind_result($balance, $token_status);
$exists_stmt->fetch();
$exists_stmt->close();

// ✅ Validate token status and balance
if ($token_status !== 'unlocked') {
    echo json_encode(["success" => false, "message" => "Withdrawal denied. Your token is locked."]);
    exit();
}

if ($balance <= 0) {
    echo json_encode(["success" => false, "message" => "Withdrawal denied. Your balance is zero."]);
    exit();
}

// 💳 Record withdrawal
$withdrawal_stmt = $conn->prepare("
    INSERT INTO withdrawals (supplier_id, amount, status, requested_at)
    VALUES (?, ?, 'pending', NOW())
");
$withdrawal_stmt->bind_param("id", $supplier_id, $balance);

if (!$withdrawal_stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to record withdrawal.", "error" => $withdrawal_stmt->error]);
    exit();
}

$withdrawal_id = $withdrawal_stmt->insert_id;
$withdrawal_stmt->close();

// 🔄 Reset wallet balance and lock token
$update_stmt = $conn->prepare("
    UPDATE supplier_wallets
    SET balance = 0, token_status = 'locked', is_token_granted = 0
    WHERE supplier_id = ?
");
$update_stmt->bind_param("i", $supplier_id);

if ($update_stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Withdrawal requested successfully. Your token has been locked.",
        "withdrawal_id" => $withdrawal_id
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong while processing your withdrawal.",
        "error" => $update_stmt->error
    ]);
}

$update_stmt->close();
$conn->close();
?>