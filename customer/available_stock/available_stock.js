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
            <button onclick="placeOrder(${stock.stock_id}, ${stock.price}, ${stock.quantity_kg})" class="order-btn">Order</button>
          </td>
        `;
        stockTable.appendChild(row);
      });
    });
}

function placeOrder(stockId, pricePerKg, availableQty) {
  const qtyInput = document.getElementById(`qty-${stockId}`);
  const totalCell = document.getElementById(`total-${stockId}`);
  const quantity = parseFloat(qtyInput.value);

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

  fetch('place_order.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(result => {
    alert(result);
    fetchStock(); // Refresh the list after order
  });
}

fetchStock();
