<?php
session_start();
include_once("../../db_config.php");

// 🚨 Check login and role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../../login/login.php");
    exit();
}

// ⏳ Auto logout after 15 minutes
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// ✅ Validate order_id
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    echo "❌ Invalid or missing Order ID.";
    exit();
}

$order_id = intval($_GET['order_id']);
$customer_id = $_SESSION['user_id'];

// ✅ Connect DB
$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// ✅ Fetch order
$query = $mysqli->prepare("
    SELECT o.order_id, o.total_price, o.payment_status, o.payment_method, f.food_type, o.order_date
    FROM orders o
    JOIN foodstock f ON o.stock_id = f.stock_id
    WHERE o.order_id = ? AND o.customer_id = ?
");
$query->bind_param("ii", $order_id, $customer_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "❌ Order not found or does not belong to you.";
    exit();
}

$order = $result->fetch_assoc();

// ✅ Handle payment simulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $update = $mysqli->prepare("UPDATE orders SET payment_status = 'Paid - On Hold' WHERE order_id = ?");
    $update->bind_param("i", $order_id);
    
    if ($update->execute()) {
        header("Location: payment_success.php?order_id=$order_id");
        exit();
    } else {
        echo "❌ Failed to update payment status.<br>";
        echo "MySQL Error: " . $mysqli->error;
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mock Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 30px;
        }
        .container {
            background: white;
            padding: 25px;
            max-width: 500px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .summary {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Mock Payment</h2>

    <div class="summary">
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
        <p><strong>Food Type:</strong> <?= htmlspecialchars($order['food_type']) ?></p>
        <p><strong>Total Price:</strong> TZS <?= number_format($order['total_price'], 2) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        <p><strong>Payment Status:</strong> <?= $order['payment_status'] === 'Paid' ? '<span style="color:green;">Paid</span>' : '<span style="color:red;">Unpaid</span>' ?></p>
    </div>

    <?php if ($order['payment_status'] === 'Paid'): ?>
        <p style="color: green;">✅ Payment has already been completed.</p>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="confirm_payment" value="1">
            <button type="submit" class="btn">Simulate Payment</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>