<?php
session_start();
include '../../db_config.php';

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../login/login.php");
    exit();
}

// Fetch suppliers with non-zero balance and token locked
$sql = "SELECT sw.supplier_id, u.name, sw.balance, sw.token_status, sw.is_token_granted
        FROM supplier_wallets sw
        JOIN users u ON sw.supplier_id = u.id
        WHERE sw.balance > 0";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grant Payment Tokens</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background: #4CAF50; color: white; }
        button {
            padding: 8px 12px;
            background: #28a745; color: white;
            border: none; border-radius: 5px;
            cursor: pointer;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .container { max-width: 900px; margin: auto; }
    </style>
</head>
<body>
<div class="container">
    <h2>Suppliers Awaiting Token Grant</h2>
    <table>
        <thead>
            <tr>
                <th>Supplier Name</th>
                <th>Balance (TZS)</th>
                <th>Token Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= number_format($row['balance'], 2) ?></td>
                        <td><?= ucfirst($row['token_status']) ?></td>
                        <td>
                            <button onclick="grantToken(<?= $row['supplier_id'] ?>)" 
                                    <?= ($row['token_status'] === 'unlocked') ? 'disabled' : '' ?>>
                                Grant Token
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No suppliers pending withdrawal.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function grantToken(supplierId) {
    if (!confirm("Are you sure you want to grant this supplier a withdrawal token?")) return;

    fetch("unlock_token.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ supplier_id: supplierId })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Failed to grant token.");
    });
}
</script>
</body>
</html>