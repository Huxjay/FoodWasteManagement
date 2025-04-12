<?php 
session_start();
include "db_config.php";

$email = $_POST['email'];
$password = $_POST['password'];
$role = strtolower($_POST['role']);

// Map role to table and redirect path
$tables = [
    "admin" => ["table" => "admn", "redirect" => "../Admin/admin.html"],
    "customer" => ["table" => "customer", "redirect" => "../Customer/customer.html"],
    "supplier" => ["table" => "supplier", "redirect" => "../Supplier/supplier.html"]
];

if (array_key_exists($role, $tables)) {
    $table = $tables[$role]["table"];
    $redirect = $tables[$role]["redirect"];

    $query = "SELECT * FROM $table WHERE email='$email' AND password='$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        header("Location: $redirect");
        exit();
    }
}

// If nothing matched
echo "<script>alert('Incorrect email or password!'); window.location.href='login.html';</script>";
$conn->close();
?>
