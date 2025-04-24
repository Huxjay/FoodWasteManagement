document.addEventListener("DOMContentLoaded", () => {
    fetch("fetch_orders.php?customer_id=1") 
      .then(response => response.json())
      .then(data => {
        const tbody = document.querySelector("#ordersTable tbody");
        tbody.innerHTML = "";
  
        data.forEach(order => {
          const row = document.createElement("tr");
  
          row.innerHTML = `
            <td>${order.order_id}</td>
            <td>${order.food_type}</td>
            <td>${order.quantity_kg}</td>
            <td>${order.price}</td>
            <td>${order.order_date}</td>
            <td>${order.status}</td>
          `;
  
          tbody.appendChild(row);
        });
      });
  });
  