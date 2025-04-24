<?php
require 'db_config.php';

$order_id = $_POST['order_id'];

$sql = "UPDATE orders SET status = 'Confirmed' WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
  echo "Order confirmed!";
} else {
  echo "Failed to update order.";
}
?>
