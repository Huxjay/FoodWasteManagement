<?php


session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login/login.php");
    exit();
}



$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");

if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

$stock_id = $_POST['stock_id'];
$customer_id = $_POST['customer_id'];
$order_date = date("Y-m-d");
$status = "Pending";

$sql = "INSERT INTO orders (customer_id, stock_id, order_date, status)
        VALUES (?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiss", $customer_id, $stock_id, $order_date, $status);

if ($stmt->execute()) {
  echo "Order placed successfully!";
} else {
  echo "Error placing order.";
}

$stmt->close();
$mysqli->close();
?>
