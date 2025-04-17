<?php
$mysqli = new mysqli("localhost", "root", "", "foodwastemanagement");

if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

$sql = "SELECT stock_id, food_type, quantity_kg, price FROM foodstock";
$result = $mysqli->query($sql);

$stocks = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $stocks[] = $row;
  }
}

header('Content-Type: application/json');
echo json_encode($stocks);

$mysqli->close();
?>
