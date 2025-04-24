document.addEventListener("DOMContentLoaded", () => {
    fetch("fetch_orders.php")
      .then(res => res.json())
      .then(data => {
        const tbody = document.querySelector("#ordersTable tbody");
        tbody.innerHTML = "";
  
        data.forEach(order => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${order.order_id}</td>
            <td>${order.customer_id}</td>
            <td>${order.food_type}</td>
            <td>${order.quantity_kg}</td>
            <td>${order.price}</td>
            <td>${order.status}</td>
            <td>
              ${order.status === 'Pending' 
                ? `<button onclick="confirmOrder(${order.order_id})">Confirm</button>` 
                : 'Confirmed'}
            </td>
          `;
          tbody.appendChild(row);
        });
      });
  });
  
  function confirmOrder(orderId) {
    fetch("update_order_status.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: `order_id=${orderId}`
    })
    .then(res => res.text())
    .then(response => {
      alert(response);
      location.reload();
    });
  }
  