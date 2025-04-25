<?php 
session_start();
include "../db_config.php";

$email = $_POST['email'];
$password = $_POST['password'];
$role = strtolower($_POST['role']);

$tables = [
    "admin" => ["table" => "admn", "redirect" => "../Admin/dashboard/dashboard.html"],
    "customer" => ["table" => "customer", "redirect" => "../customer/dashboard/dashboard.php"],
    "supplier" => ["table" => "supplier", "redirect" => "../Supplier/dashboard/dashboard.html"]
];

if (array_key_exists($role, $tables)) {
    $table = $tables[$role]["table"];
    $redirect = $tables[$role]["redirect"];

    // Include status check only for supplier and customer
    $statusCheck = ($role === 'admin') ? "" : " AND status='Active'";

    $query = "SELECT * FROM $table WHERE email=? AND password=? $statusCheck";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($role === 'supplier') {
            $_SESSION['supplier_id'] = $user['supplier_id']; 
        } elseif ($role === 'customer') {
            $_SESSION['customer_id'] = $user['customer_id'];
        } elseif ($role === 'admin') {
            $_SESSION['admin_id'] = $user['admin_id'];
        }

        header("Location: $redirect");
        exit();
    }
}

// Login failed
echo "<script>alert('Incorrect email, password, or account is not active!'); window.location.href='login.html';</script>";
$conn->close();
?>
