

<?php
session_start();
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Check role and login
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
        echo "Unauthorized. Please log in.";
        exit();
    }

    $supplier_id = $_SESSION['user_id']; // ✅ This is what you saved in login
    $food_type = $_POST['food_type'];
    $quantity_kg = $_POST['quantity_kg'];
    $price = $_POST['price'];
    
    // Optional: Automatically use session location_id if available
    if (isset($_SESSION['location_id'])) {
        $location_id = $_SESSION['location_id'];
    } else {
        $location_id = $_POST['location_id']; // fallback to form input
    }

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
