<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
</head>
<body>
    <h2>✅ Payment Successful!</h2>
    <p>Thank you. Your payment for Order #<?= htmlspecialchars($order_id) ?> has been confirmed.</p>
    <a href="../dashboard/dashboard.php">Back to Dashboard</a>
</body>
</html>
