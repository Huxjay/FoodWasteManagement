document.getElementById('stockForm').addEventListener('submit', function(e) {
    e.preventDefault();
  
    const formData = new FormData();
    formData.append('food_type', document.getElementById('food_type').value);
    formData.append('quantity_kg', document.getElementById('quantity_kg').value);
    formData.append('price', document.getElementById('price').value);
    formData.append('location_id', document.getElementById('location_id').value);
  
    fetch('post_stock.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      document.getElementById('message').innerText = data;
      document.getElementById('stockForm').reset();
    })
    .catch(error => {
      document.getElementById('message').innerText = "Error posting stock.";
    });
  });
  