
<?php
session_start();
ob_clean(); // clean any output buffer

require_once(__DIR__ . '/tcpdf/tcpdf.php'); 
// Adjust path to tcpdf.php if needed

$conn = new mysqli("localhost", "root", "", "foodwastemanagement");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Ensure supplier is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
    die("Unauthorized access.");
}

$supplier_id = $_SESSION['user_id'];
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$where = "WHERE f.supplier_id = $supplier_id";
if ($from_date && $to_date) {
    $where .= " AND o.order_date BETWEEN '$from_date' AND '$to_date'";
}

$sql = "
SELECT o.order_id, u.name AS customer_name, f.food_type, o.quantity_kg, o.total_price, o.order_date, o.status
FROM orders o
JOIN foodstock f ON o.stock_id = f.stock_id
JOIN users u ON o.customer_id = u.id
$where
ORDER BY o.order_date DESC
";

$result = $conn->query($sql);

// ✅ Start PDF generation
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Food Waste Management');
$pdf->SetTitle('Supplier Order Report');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$html = '<h2>Supplier Order Report</h2>';
$html .= '<table border="1" cellpadding="4">
    <thead>
        <tr>
            <th><b>Order ID</b></th>
            <th><b>Customer</b></th>
            <th><b>Food Type</b></th>
            <th><b>Quantity (kg)</b></th>
            <th><b>Total Price</b></th>
            <th><b>Order Date</b></th>
            <th><b>Status</b></th>
        </tr>
    </thead><tbody>';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>' . $row['order_id'] . '</td>
            <td>' . $row['customer_name'] . '</td>
            <td>' . $row['food_type'] . '</td>
            <td>' . $row['quantity_kg'] . '</td>
            <td>' . number_format($row['total_price'], 2) . '</td>
            <td>' . $row['order_date'] . '</td>
            <td>' . ucfirst($row['status']) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="7">No orders found</td></tr>';
}
$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('supplier_order_report.pdf', 'I');
exit;
?>