<?php 
session_start();
include_once("../../db_config.php");

// 🚨 Block caching (prevents access via back button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// 🚨 Enforce login and correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../../login/login.php");
    exit();
}

// ⏳ Auto logout after 15 minutes of inactivity
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}


$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get optional filters from GET parameters
$foodTypeFilter = isset($_GET['food_type']) ? trim($_GET['food_type']) : '';
$supplierFilter = isset($_GET['supplier_name']) ? trim($_GET['supplier_name']) : '';

$sql = "
    SELECT 
        fs.stock_id, 
        fs.food_type, 
        fs.quantity_kg, 
        fs.price, 
        fs.created_at,
        u.name AS supplier_name, 
        l.address AS supplier_location 
    FROM foodstock fs
    JOIN users u ON fs.supplier_id = u.id
    JOIN locations l ON fs.location_id = l.location_id
    WHERE fs.status = 'Active' 
      AND fs.quantity_kg > 0
      AND u.status = 'Active'
";

// Add filter conditions dynamically
$params = [];
$types = '';

if (!empty($foodTypeFilter)) {
    $sql .= " AND fs.food_type = ?";
    $params[] = $foodTypeFilter;
    $types .= 's';
}

if (!empty($supplierFilter)) {
    $sql .= " AND u.name LIKE ?";
    $params[] = '%' . $supplierFilter . '%';
    $types .= 's';
}

$stmt = $mysqli->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$stocks = [];

while ($row = $result->fetch_assoc()) {
    $stocks[] = $row;
}

header('Content-Type: application/json');
echo json_encode($stocks);

$stmt->close();
$mysqli->close();
?>
