<?php
session_start();
include_once("../../db_config.php");

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// Enforce login and role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'supplier') {
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
    <title>View Orders - Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 40px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        .btn-confirm {
            background-color: #28a745;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-confirm:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
        .status-pending { color: orange; }
        .status-confirmed { color: green; }
        .status-delivered { color: blue; }
    </style>
</head>
<body>
    <h1>Customer Orders</h1>
    <table id="ordersTable">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Food Type</th>
                <th>Quantity (kg)</th>
                <th>Total Price</th>
                <th>Date</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
    fetch("fetch_orders.php")
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#ordersTable tbody");
            tbody.innerHTML = "";

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9">No orders found.</td></tr>`;
                return;
            }

            data.forEach(order => {
                const row = document.createElement("tr");

                let statusClass = "";
                if (order.status === 'Pending Supplier Confirmation') statusClass = "status-pending";
                else if (order.status === 'Order Confirmed') statusClass = "status-confirmed";
                else if (order.status === 'Delivered') statusClass = "status-delivered";

                let action = "NA";
                if (order.status === "Pending Supplier Confirmation" && order.payment_status === "Paid - On Hold") {
                    action = `<button class="btn-confirm" onclick="confirmOrder(${order.order_id})">Confirm Order</button>`;
                }

                row.innerHTML = `
                    <td>${order.order_id}</td>
                    <td>${order.customer_name}</td>
                    <td>${order.food_type}</td>
                    <td>${order.quantity_kg}</td>
                    <td>${order.total_price}</td>
                    <td>${order.order_date}</td>
                    <td class="${statusClass}">${order.status}</td>
                    <td>${order.payment_status}</td>
                    <td>${action}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(err => {
            console.error("Error loading orders", err);
            document.querySelector("#ordersTable tbody").innerHTML = `<tr><td colspan="9">Error loading orders.</td></tr>`;
        });

    function confirmOrder(orderId) {
        if (!confirm("Are you sure you want to confirm this order?")) return;

        fetch("confirm_order.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Order confirmed successfully.");
                location.reload();
            } else {
                alert("Failed to confirm order.");
            }
        });
    }
    </script>
</body>
</html>
