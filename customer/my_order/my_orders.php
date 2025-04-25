<?php
// my_orders.php
session_start();
if (!isset($_SESSION['customer_id'])) {
  header('Location: ../login/login.html');
  exit();
}
$customerId = $_SESSION['customer_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Orders</title>
  <link rel="stylesheet" href="my_orders.css" />
</head>
<body>
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
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    const customerId = <?= json_encode($customerId) ?>;

    document.addEventListener("DOMContentLoaded", () => {
      fetch(`fetch_orders.php?customer_id=${customerId}`)
        .then(r => r.json())
        .then(data => {
          const tbody = document.querySelector("#ordersTable tbody");
          tbody.innerHTML = "";
          data.forEach(o => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${o.order_id}</td>
              <td>${o.food_type}</td>
              <td>${o.quantity}</td>
              <td>${parseFloat(o.total_price).toFixed(2)}</td>
              <td>${o.order_date}</td>
              <td>${o.status}</td>
            `;
            tbody.appendChild(tr);
          });
        });
    });
  </script>
</body>
</html>
