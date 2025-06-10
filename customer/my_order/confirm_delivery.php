<?php
// session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// include '../../db_config.php';

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $data = json_decode(file_get_contents("php://input"), true);
//     $order_id = $data['order_id'];

//     // Authentication check
//     if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
//         echo json_encode(["success" => false, "message" => "Unauthorized"]);
//         exit();
//     }

    // Get supplier and payment info
    //$stmt = $conn->prepare("SELECT supplier_id, total_price FROM orders WHERE order_id = ? AND customer_id = ?");
    // $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    // $stmt->execute();
    // $stmt->bind_result($supplier_id, $total_price);
    // if (!$stmt->fetch()) {
    //     echo json_encode(["success" => false, "message" => "Order not found."]);
    //     exit();
   // }
    // $stmt->close();

    // $conn->begin_transaction();
    // try {
    //     // Mark order as delivered
    //     $updateOrder = $conn->prepare("UPDATE orders SET status = 'Delivered', delivery_confirmed_by_customer = 1 WHERE order_id = ? AND customer_id = ?");
    //     $updateOrder->bind_param("ii", $order_id, $_SESSION['user_id']);
    //     $updateOrder->execute();
    //     $updateOrder->close();

    //     // Add amount to supplier wallet
    //     $walletUpdate = $conn->prepare("
    //         INSERT INTO supplier_wallets (supplier_id, balance)
    //         VALUES (?, ?)
    //         ON DUPLICATE KEY UPDATE balance = balance + VALUES(balance), last_updated = NOW()
    //     ");
    //     $walletUpdate->bind_param("id", $supplier_id, $total_price);
    //     $walletUpdate->execute();
    //     $walletUpdate->close();

    //     $conn->commit();
    //     echo json_encode(["success" => true, "message" => "Delivery confirmed. Supplier wallet updated."]);
    // } catch (Exception $e) {
    //     $conn->rollback();
    //     echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
    // }

    // $conn->close();
//}






session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $order_id = intval($data['order_id']);

    // Authentication check
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
        echo json_encode(["success" => false, "message" => "Unauthorized"]);
        exit();
    }

    $customer_id = $_SESSION['user_id'];

    // Fetch order info
    $stmt = $conn->prepare("SELECT supplier_id, total_price FROM orders WHERE order_id = ? AND customer_id = ? AND delivery_confirmed_by_customer = 0");
    $stmt->bind_param("ii", $order_id, $customer_id);
    $stmt->execute();
    $stmt->bind_result($supplier_id, $total_price);

    if (!$stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Order not found or already confirmed."]);
        exit();
    }
    $stmt->close();

    $conn->begin_transaction();

    try {
        // 1. Mark as delivered
        $updateOrder = $conn->prepare("UPDATE orders SET status = 'Delivered', delivery_confirmed_by_customer = 1 WHERE order_id = ? AND customer_id = ?");
        $updateOrder->bind_param("ii", $order_id, $customer_id);
        $updateOrder->execute();
        $updateOrder->close();

        // 2. Ensure supplier wallet exists
        $checkWallet = $conn->prepare("SELECT balance FROM supplier_wallets WHERE supplier_id = ?");
        $checkWallet->bind_param("i", $supplier_id);
        $checkWallet->execute();
        $result = $checkWallet->get_result();

        if ($result->num_rows > 0) {
            // Wallet exists — update balance
            $updateWallet = $conn->prepare("UPDATE supplier_wallets SET balance = balance + ?, last_updated = NOW() WHERE supplier_id = ?");
            $updateWallet->bind_param("di", $total_price, $supplier_id);
            $updateWallet->execute();
            $updateWallet->close();
        } else {
            // Wallet does not exist — insert new
            $insertWallet = $conn->prepare("INSERT INTO supplier_wallets (supplier_id, balance, token_status, is_token_granted, last_updated) VALUES (?, ?, 'locked', 0, NOW())");
            $insertWallet->bind_param("id", $supplier_id, $total_price);
            $insertWallet->execute();
            $insertWallet->close();
        }
        $checkWallet->close();

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Delivery confirmed. Wallet updated."]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
    }

    $conn->close();
}
?>
