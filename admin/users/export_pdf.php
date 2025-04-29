<?php
require_once('../../library/fpdf/fpdf.php');
// adjust path if needed

$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get date filter if any
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$where = "";
if ($from_date && $to_date) {
    $where = "WHERE o.order_date BETWEEN '$from_date' AND '$to_date'";
}

// Fetch orders
$sql = "
SELECT o.order_id, c.name AS customer_name, f.food_type, o.quantity_kg, o.total_price, o.order_date, o.status
FROM orders o
JOIN customer c ON o.customer_id = c.customer_id
JOIN foodstock f ON o.stock_id = f.stock_id
$where
ORDER BY o.order_date DESC
";
$result = $mysqli->query($sql);

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Order Report', 0, 1, 'C');
$pdf->Ln(10);

// Table headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 10, 'ID', 1);
$pdf->Cell(40, 10, 'Customer', 1);
$pdf->Cell(35, 10, 'Food', 1);
$pdf->Cell(20, 10, 'Qty(kg)', 1);
$pdf->Cell(25, 10, 'Price', 1);
$pdf->Cell(25, 10, 'Date', 1);
$pdf->Cell(30, 10, 'Status', 1);
$pdf->Ln();

// Table content
$pdf->SetFont('Arial', '', 10);
while($row = $result->fetch_assoc()) {
    $pdf->Cell(15, 10, $row['order_id'], 1);
    $pdf->Cell(40, 10, $row['customer_name'], 1);
    $pdf->Cell(35, 10, $row['food_type'], 1);
    $pdf->Cell(20, 10, $row['quantity_kg'], 1);
    $pdf->Cell(25, 10, number_format($row['total_price'],2), 1);
    $pdf->Cell(25, 10, $row['order_date'], 1);
    $pdf->Cell(30, 10, $row['status'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'order_report.pdf');

$mysqli->close();
?>
