<?php 
session_start();
include_once("../../db_config.php");

// 🚫 Prevent caching (back button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// 🔐 Enforce login and role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
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
$_SESSION['LAST_ACTIVITY'] = time();

// 📦 Fetch wallet data
$supplier_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT balance, token_status FROM supplier_wallets WHERE supplier_id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$stmt->bind_result($balance, $token_status);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wallet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            padding: 40px;
        }
        .wallet-box {
            background: #fff;
            padding: 30px;
            max-width: 500px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .wallet-info {
            margin-top: 25px;
            font-size: 18px;
            color: #444;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 6px;
            color: white;
            font-size: 14px;
        }
        .badge.green { background-color: #28a745; }
        .badge.red { background-color: #dc3545; }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 25px;
            display: block;
            width: 100%;
            font-size: 16px;
            transition: 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="wallet-box">
    <h2><i class="fas fa-wallet"></i> My Wallet</h2>
    <div class="wallet-info">
        <p><strong><i class="fas fa-money-bill-wave"></i> Balance:</strong>
            <?= number_format($balance ?? 0, 2) ?> TZS
        </p>
        <p><strong><i class="fas fa-lock"></i> Token Status:</strong>
            <span class="badge <?= $token_status === 'unlocked' ? 'green' : 'red' ?>">
                <?= ucfirst($token_status ?? 'Locked') ?>
            </span>
        </p>
    </div>

    <button id="withdrawBtn" class="btn" onclick="withdrawFunds()" <?= ($token_status !== 'unlocked' || $balance <= 0) ? 'disabled' : '' ?>>
        <i class="fas fa-paper-plane"></i> Withdraw Funds
    </button>
</div>

<script>
function withdrawFunds() {
    if (!confirm("Are you sure you want to withdraw your balance?")) return;

    const btn = document.getElementById("withdrawBtn");
    btn.disabled = true;
    btn.innerText = "Processing...";

    fetch("request_withdrawal.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        } else {
            btn.disabled = false;
            btn.innerText = "Withdraw Funds";
        }
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Something went wrong while processing your withdrawal.");
        btn.disabled = false;
        btn.innerText = "Withdraw Funds";
    });
}
</script>

</body>
</html>