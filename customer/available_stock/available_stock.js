document.addEventListener("DOMContentLoaded", () => {
  fetch("/FoodWasteManagement/customer/available_stock/fetch_stock.php")


    .then(response => response.json())
    .then(data => {
      const tableBody = document.querySelector("#stockTable tbody");
      tableBody.innerHTML = "";

      data.forEach(stock => {
        const row = document.createElement("tr");

        row.innerHTML = `
          <td>${stock.stock_id}</td>
          <td>${stock.food_type}</td>
          <td>${stock.quantity_kg}</td>
          <td>${stock.price}</td>
          <td><button onclick="placeOrder(${stock.stock_id})">Order</button></td>
        `;

        tableBody.appendChild(row);
      });
    });
});

function placeOrder(stockId) {
  fetch("place_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `stock_id=${stockId}&customer_id=1`
  })
  .then(response => response.text())
  .then(result => {
    alert(result);
  });
}
