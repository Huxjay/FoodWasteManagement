<?php
include_once("db_config.php");
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login/login.html");
    exit();
}

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];

    $table = '';
    $id_column = '';
    $redirect = '';

    if ($type == 'customer') {
        $table = 'customer';
        $id_column = 'customer_id';
        $redirect = 'users/manage_customers.php';
    } elseif ($type == 'supplier') {
        $table = 'supplier';
        $id_column = 'supplier_id';
        $redirect = 'users/manage_suppliers.php';
    }

    if ($table != '') {
        $sql = "UPDATE $table SET status='Deleted' WHERE $id_column=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: ../$redirect");
            exit();
        } else {
            echo "Failed to delete user.";
        }

        $stmt->close();
    }
}

$conn->close();
?>
