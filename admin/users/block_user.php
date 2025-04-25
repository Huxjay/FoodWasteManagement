<?php
include_once("db_config.php");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.html");
    exit();
}

if (isset($_GET['type']) && isset($_GET['id']) && isset($_GET['action'])) {
    $userType = $_GET['type']; // 'customer' or 'supplier'
    $userId = intval($_GET['id']);
    $action = $_GET['action']; // 'block' or 'unblock'

    $status = ($action === 'block') ? 'Blocked' : 'Active';

    if ($userType === 'customer') {
        $sql = "UPDATE customer SET status = ? WHERE customer_id = ?";
    } elseif ($userType === 'supplier') {
        $sql = "UPDATE supplier SET status = ? WHERE supplier_id = ?";
    } else {
        die("Invalid user type.");
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $userId);

    if ($stmt->execute()) {
        header("Location: manage_" . $userType . "s.php");
        exit();
    } else {
        echo "Failed to update user status.";
    }

    $stmt->close();
} else {
    echo "Invalid parameters.";
}

$conn->close();
?>
