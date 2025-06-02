<?php  
session_start();
include_once("../../db_config.php");

// 🚫 Prevent back-button after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// 🔐 Ensure customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../../login/login.php");
    exit();
}

// ⏳ Auto logout after 15 mins
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// ✅ Assign and validate customer
$customer_id = $_SESSION['user_id'] ?? null;
if (!$customer_id) {
    echo "❌ Session missing user_id.";
    exit();
}

// ✅ Collect POST data
$stock_id = intval($_POST['stock_id']);
$quantity = floatval($_POST['quantity']);
$total_price = floatval($_POST['total_price']);
$order_date = date("Y-m-d");

// ✅ Set initial status and delivery confirmation
$status = "Pending Supplier Confirmation";
$payment_method = trim($_POST['payment_method']);
$payment_status = "Pending";
$delivery_confirmed_by_customer = 0;

// 🚨 Validate input
if ($quantity <= 0) {
    echo "❌ Invalid quantity.";
    exit();
}

// 📦 Connect to DB
$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");
if ($mysqli->connect_error) {
    die("❌ Connection failed: " . $mysqli->connect_error);
}

// 🧾 Check available stock
$checkStock = $mysqli->prepare("SELECT quantity_kg, status FROM foodstock WHERE stock_id = ?");
$checkStock->bind_param("i", $stock_id);
$checkStock->execute();
$result = $checkStock->get_result();

if ($result->num_rows > 0) {
    $stock = $result->fetch_assoc();

    if ($stock['status'] !== 'Active') {
        echo "⚠ Stock is no longer available.";
        exit();
    }

    if ($quantity > $stock['quantity_kg']) {
        echo "⚠ Not enough stock available.";
        exit();
    }

    // 🧾 Begin transaction
    $mysqli->begin_transaction();

    try {
        // 📥 Insert order
        $insert = $mysqli->prepare("
            INSERT INTO orders (
                customer_id, 
                stock_id, 
                quantity_kg, 
                total_price, 
                order_date, 
                status, 
                payment_method, 
                payment_status, 
                delivery_confirmed_by_customer
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->bind_param(
            "iiddssssi",
            $customer_id,
            $stock_id,
            $quantity,
            $total_price,
            $order_date,
            $status,
            $payment_method,
            $payment_status,
            $delivery_confirmed_by_customer
        );
        $insert->execute();

        // 📉 Update stock quantity
        $newQty = $stock['quantity_kg'] - $quantity;
        $updateStock = $mysqli->prepare("UPDATE foodstock SET quantity_kg = ? WHERE stock_id = ?");
        $updateStock->bind_param("di", $newQty, $stock_id);
        $updateStock->execute();

        $mysqli->commit();

        // ✅ Redirect to mock payment
        $order_id = $insert->insert_id;
        echo "redirect: mock_payment.php?order_id=$order_id";
        exit();

    } catch (Exception $e) {
        $mysqli->rollback();
        echo "❌ Error placing order: " . $e->getMessage();
    }

} else {
    echo "❌ Stock not found.";
}

$mysqli->close();
?>