<?php
session_start();
include_once("../../db_config.php");

$supplier_id = $_SESSION['user_id'];

$result = $conn->query("SELECT balance FROM supplier_wallets WHERE supplier_id = $supplier_id");
$row = $result->fetch_assoc();
echo "Your Wallet Balance: TZS " . number_format($row['balance'], 2);
?>