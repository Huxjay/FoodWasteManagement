<?php 
session_start();
include_once("../../db_config.php");

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// Enforce login and role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: ../../login/login.php");
    exit();
}

// Auto logout after 15 minutes
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../../login/login.php?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            padding: 40px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        .btn-confirm {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-confirm:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .status-pending { color: orange; font-weight: bold; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-delivered { color: blue; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>My Orders</h1>

    <table id="ordersTable">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Food Type</th>
                <th>Quantity (kg)</th>
                <th>Total Price</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
fetch("fetch_orders.php")
    .then(response => response.json())
    .then(data => {
        const tbody = document.querySelector("#ordersTable tbody");
        tbody.innerHTML = "";

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7">No orders found.</td></tr>`;
            return;
        }

        data.forEach(order => {
            const row = document.createElement("tr");

            let statusClass = "";
            if (order.status === "Pending Supplier Confirmation") statusClass = "status-pending";
            else if (order.status === "Confirmed") statusClass = "status-confirmed";
            else if (order.status === "Delivered") statusClass = "status-delivered";

            let actionHTML = "N/A";
            if (order.status === "Confirmed" && order.delivery_confirmed_by_customer == 0) {
                actionHTML = `<button class="btn-confirm" onclick="confirmDelivery(${order.order_id})">Confirm Delivery</button>`;
            }

            row.innerHTML = `
                <td>${order.order_id}</td>
                <td>${order.food_type}</td>
                <td>${order.quantity_kg}</td>
                <td>${order.total_price}</td>
                <td>${order.order_date}</td>
                <td class="${statusClass}">${order.status}</td>
                <td>${actionHTML}</td>
            `;

            tbody.appendChild(row);
        });
    })
    .catch(err => {
        console.error("Failed to fetch orders", err);
        document.querySelector("#ordersTable tbody").innerHTML = `<tr><td colspan="7">Failed to load orders.</td></tr>`;
    });

function confirmDelivery(orderId) {
    if (!confirm("Are you sure this order was delivered?")) return;

    fetch("confirm_delivery.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ order_id: orderId })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    })
    .catch(err => {
        console.error("Error confirming delivery:", err);
        alert("Something went wrong while confirming delivery.");
    });
}
</script>

</body>
</html>
