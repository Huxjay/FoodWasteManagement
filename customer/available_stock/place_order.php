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
$customer_id = $_SESSION['customer_id'];
$quantity = $_POST['quantity'];
$total_price = $_POST['total_price'];
$order_date = date("Y-m-d");
$status = "Pending";

// Check if enough stock is available
$checkStock = $mysqli->prepare("SELECT quantity_kg FROM foodstock WHERE stock_id = ?");
$checkStock->bind_param("i", $stock_id);
$checkStock->execute();
$result = $checkStock->get_result();

if ($result->num_rows > 0) {
    $stock = $result->fetch_assoc();
    
    if ($quantity > $stock['quantity_kg']) {
        echo "Not enough stock available.";
        exit();
    }

    // Reduce stock quantity
    $newQty = $stock['quantity_kg'] - $quantity;
    $updateStock = $mysqli->prepare("UPDATE foodstock SET quantity_kg = ? WHERE stock_id = ?");
    $updateStock->bind_param("di", $newQty, $stock_id);
    $updateStock->execute();

    // Insert into orders table
    $insert = $mysqli->prepare("INSERT INTO orders (customer_id, stock_id, quantity_kg, total_price, order_date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iiddss", $customer_id, $stock_id, $quantity, $total_price, $order_date, $status);

    if ($insert->execute()) {
        echo "Order placed successfully!";
    } else {
        echo "Error placing order: " . $insert->error;
    }

    $insert->close();
} else {
    echo "Stock not found.";
}

$mysqli->close();
?>
