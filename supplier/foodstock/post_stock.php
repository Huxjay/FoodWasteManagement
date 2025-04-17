<?php
session_start();
require 'db_config.php'; // Adjust path based on your structure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['supplier_id'])) {
        echo "Unauthorized. Please log in.";
        exit();
    }

    $supplier_id = $_SESSION['supplier_id'];
    $food_type = $_POST['food_type'];
    $quantity_kg = $_POST['quantity_kg'];
    $price = $_POST['price'];
    $location_id = $_POST['location_id'];

    $stmt = $conn->prepare("INSERT INTO foodstock (supplier_id, food_type, quantity_kg, price, location_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdd", $supplier_id, $food_type, $quantity_kg, $price, $location_id);

    if ($stmt->execute()) {
        echo "Stock posted successfully!";
    } else {
        echo "Failed to post stock.";
    }

    $stmt->close();
    $conn->close();
}
?>
