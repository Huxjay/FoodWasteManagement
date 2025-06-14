<?php
require_once(__DIR__ . '/tcpdf/tcpdf.php'); // 

// Connect to DB
$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Get filters
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

$where = "";
if ($from_date && $to_date) {
    $where = "WHERE o.order_date BETWEEN '$from_date' AND '$to_date'";
}

// Fetch data
$sql = "
    SELECT o.order_id, c.name AS customer_name, f.food_type, o.quantity_kg, o.total_price, o.order_date, o.status
    FROM orders o
    JOIN users c ON o.customer_id = c.id
    JOIN foodstock f ON o.stock_id = f.stock_id
    $where
    ORDER BY o.order_date DESC
";

$result = $mysqli->query($sql);

// Initialize TCPDF
$pdf = new TCPDF();
$pdf->SetCreator('Admin');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Order Report');
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Order Report', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);

// Table headers
$html = '
<table border="1" cellpadding="4">
  <thead>
    <tr style="background-color:#f0f0f0;">
      <th><b>ID</b></th>
      <th><b>Customer</b></th>
      <th><b>Food</b></th>
      <th><b>Qty (kg)</b></th>
      <th><b>Price</b></th>
      <th><b>Date</b></th>
      <th><b>Status</b></th>
    </tr>
  </thead>
  <tbody>
';

// Table content
while ($row = $result->fetch_assoc()) {
    $html .= '
    <tr>
      <td>' . $row['order_id'] . '</td>
      <td>' . htmlspecialchars($row['customer_name']) . '</td>
      <td>' . htmlspecialchars($row['food_type']) . '</td>
      <td>' . number_format($row['quantity_kg'], 2) . '</td>
      <td>' . number_format($row['total_price'], 2) . '</td>
      <td>' . $row['order_date'] . '</td>
      <td>' . ucfirst($row['status']) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, false, false, '');

// Output
$pdf->Output('order_report.pdf', 'D');
$mysqli->close();
?>