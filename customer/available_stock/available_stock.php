<?php 
session_start();
include_once("../../db_config.php");

// 🚨 Block caching (prevents access via back button after logout)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

// 🚨 Enforce login and correct role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Stock</title>
  <link rel="stylesheet" href="available_stock.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .container {
      display: flex;
      min-height: 100vh;
    }
    .main-content {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
    }
    h1 {
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      text-align: center;
      border: 1px solid #ccc;
    }
    button {
      padding: 6px 12px;
      background-color: #28a745;
      color: white;
      border: none;
      cursor: pointer;
    }
    select, input[type='number'] {
      padding: 4px;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php include '../sidebar.php'; ?>
    <div class="main-content">
      <h1>Available Food Stock</h1>
      <table id="stockTable">
        <thead>
          <tr>
            <th>Stock ID</th>
            <th>Food Type</th>
            <th>Quantity (kg)</th>
            <th>Price (per kg)</th>
            <th>Order Quantity (kg)</th>
            <th>Total Price</th>
            <th>Payment Method</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Rows will be loaded here -->
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function fetchStock() {
      fetch('fetch_stock.php')
        .then(response => response.json())
        .then(data => {
          const stockTable = document.getElementById('stockTable').getElementsByTagName('tbody')[0];
          stockTable.innerHTML = '';

          data.forEach(stock => {
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${stock.stock_id}</td>
              <td>${stock.food_type}</td>
              <td>${stock.quantity_kg}</td>
              <td>${stock.price}</td>
              <td>
                <input type="number" id="qty-${stock.stock_id}" min="0.01" max="${stock.quantity_kg}" step="0.01" placeholder="Qty (kg)">
              </td>
              <td id="total-${stock.stock_id}">—</td>
              <td>
                <select id="payment-${stock.stock_id}">
                  <option value="">Select Payment</option>
                  <option value="Cash">Cash</option>
                  <option value="Mobile Money">Mobile Money</option>
                  <option value="Bank Transfer">Bank Transfer</option>
                </select>
              </td>
              <td>
                <button onclick="placeOrder(${stock.stock_id}, ${stock.price}, ${stock.quantity_kg})" class="order-btn">Order</button>
              </td>
            `;

            const locationRow = document.createElement('tr');
            locationRow.innerHTML = `
              <td colspan="8" style="text-align: left; font-style: italic; color: #555;">
                📦 Supplier: ${stock.supplier_name} | 📍 Location: ${stock.supplier_location}
              </td>
            `;

            stockTable.appendChild(row);
            stockTable.appendChild(locationRow);
          });
        });
    }

    function placeOrder(stockId, pricePerKg, availableQty) {
      const qtyInput = document.getElementById(`qty-${stockId}`);
      const paymentSelect = document.getElementById(`payment-${stockId}`);
      const totalCell = document.getElementById(`total-${stockId}`);
      const quantity = parseFloat(qtyInput.value);
      const paymentMethod = paymentSelect.value;

      if (!paymentMethod) {
        alert("Please select a payment method.");
        return;
      }

      if (isNaN(quantity) || quantity <= 0 || quantity > availableQty) {
        alert('Please enter a valid quantity.');
        return;
      }

      const totalPrice = (quantity * pricePerKg).toFixed(2);
      totalCell.textContent = `Tsh ${totalPrice}`;

      const formData = new FormData();
      formData.append('stock_id', stockId);
      formData.append('quantity', quantity);
      formData.append('total_price', totalPrice);
      formData.append('payment_method', paymentMethod);

      fetch("place_order.php", {
    method: "POST",
    body: formData
})
.then(response => response.text())
.then(data => {
    if (data.startsWith("redirect:")) {
        const url = data.replace("redirect:", "").trim();
        window.location.href = url;  // ✅ Perform the redirect
    } else {
        alert(data);  // Optional: for any error messages
    }
});

    }

    fetchStock(); // Initial load
  </script>
</body>
</html>
