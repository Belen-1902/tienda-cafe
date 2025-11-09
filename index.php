<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cafetería - Menú QR</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #fff8f6;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }
    header {
      background: #ff7f66;
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    main {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      padding: 20px;
      flex: 1;
    }
    /* Barra de búsqueda */
    .search-bar {
      margin-bottom: 15px;
      display: flex;
      justify-content: center;
    }
    .search-bar input {
      width: 80%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    /* Contenedor de tarjetas */
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 20px;
    }
    /* Tarjeta de producto */
    .product-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      text-align: center;
      cursor: pointer;
      transition: transform .2s, box-shadow .2s;
    }
    .product-card:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 12px rgba(0,0,0,0.15);
    }
    .product-img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      background: #f5f5f5;
    }
    .product-info {
      padding: 12px;
    }
    .product-info h4 {
      font-size: 1rem;
      margin: 5px 0;
      color: #333;
    }
    .product-info p {
      color: #e67c73;
      margin: 0;
      font-weight: bold;
    }
    /* Carrito */
    .sidebar {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      padding: 20px;
      display: flex;
      flex-direction: column;
      height: fit-content;
    }
    #cartList {
      max-height: 250px;
      overflow-y: auto;
      border: 1px solid #eee;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 6px;
      background: #fffaf9;
    }
    #cartList ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    #cartList li {
      padding: 5px 0;
      border-bottom: 1px solid #f0f0f0;
      font-size: 0.9rem;
    }
    #cartTotal {
      font-weight: bold;
      text-align: right;
      margin: 10px 0;
      font-size: 1.1rem;
    }
    #paymentForm {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    #paymentForm label {
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    #paymentForm button {
      background: #ff7f66;
      border: none;
      color: white;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 1rem;
      margin-top: 10px;
    }
    #paymentForm button:hover {
      background: #ff4f33;
    }
    select {
      margin-bottom: 15px;
      padding: 5px;
      border-radius: 6px;
      border: 1px solid #ddd;
    }
  </style>
</head>
<body>
  <header>
    <h1>Cafetería</h1>
    <div>
      <label>Mesa:</label>
      <select id="tableSelect">
        <option value="">Seleccionar Mesa</option>
        <?php for ($i = 1; $i <= 20; $i++): ?>
          <option value="<?php echo $i ?>">Mesa <?php echo $i ?></option>
        <?php endfor; ?>
      </select>
    </div>
  </header>

  <main>
    <div>
      <!-- Barra de búsqueda -->
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Buscar producto...">
      </div>

      <!-- Productos -->
      <div id="productGrid" class="product-grid">
        <p>Cargando productos...</p>
      </div>
    </div>

    <!-- Carrito y pago -->
    <div class="sidebar">
      <h3>Carrito</h3>
      <div id="cartList">(Carrito vacío)</div>
      <div id="cartTotal">Total: $0.00</div>

      <form id="paymentForm">
        <h3>Método de Pago</h3>
        <label><input type="radio" name="paymentMethod" value="modo"> Modo</label>
        <label><input type="radio" name="paymentMethod" value="qr"> QR</label>
        <label><input type="radio" name="paymentMethod" value="cash"> Efectivo</label>
        <label><input type="radio" name="paymentMethod" value="transfer"> Transferencia</label>
        <input type="number" id="amountReceived" placeholder="Monto Recibido (efectivo)" step="0.01" min="0" style="display:none;">
        <button type="submit">Pagar</button>
      </form>
    </div>
  </main>

  <script>
    let products = [];
    let cart = [];

    async function loadProducts() {
      const res = await fetch('get_products.php');
      const data = await res.json();

      const grid = document.getElementById('productGrid');
      grid.innerHTML = '';

      if (!data.success || !data.products.length) {
        grid.innerHTML = '<p>No hay productos disponibles.</p>';
        return;
      }

      products = data.products;
      renderProducts(products);
    }

    function renderProducts(list) {
      const grid = document.getElementById('productGrid');
      grid.innerHTML = '';

      list.forEach(p => {
        let img = p.image ? p.image.trim() : '';
        if (!img.startsWith('uploads/') && !img.startsWith('assets/')) {
          img = 'uploads/' + img;
        }
        const imageSrc = img || 'assets/img/no-image.jpg';

        const card = document.createElement('div');
        card.className = 'product-card';
        card.onclick = () => addToCart(p.id, p.name, parseFloat(p.price));
        card.innerHTML = `
          <img src="${imageSrc}" alt="${p.name}" class="product-img" onerror="this.src='assets/img/no-image.jpg'">
          <div class="product-info">
            <h4>${p.name}</h4>
            <p>$${parseFloat(p.price).toFixed(2)}</p>
          </div>
        `;
        grid.appendChild(card);
      });
    }

    // Filtro en tiempo real
    document.getElementById('searchInput').addEventListener('input', e => {
      const term = e.target.value.toLowerCase();
      const filtered = products.filter(p => p.name.toLowerCase().includes(term));
      renderProducts(filtered);
    });

    function addToCart(id, name, price) {
      const existing = cart.find(item => item.id === id);
      if (existing) {
        existing.qty++;
      } else {
        cart.push({ id, name, price, qty: 1 });
      }
      renderCart();
    }

    function renderCart() {
      const cartList = document.getElementById('cartList');
      const cartTotal = document.getElementById('cartTotal');
      if (cart.length === 0) {
        cartList.innerHTML = '(Carrito vacío)';
        cartTotal.innerText = 'Total: $0.00';
        return;
      }
      let html = '<ul>';
      let total = 0;
      for (const item of cart) {
        total += item.price * item.qty;
        html += `<li>${item.name} x ${item.qty} - $${(item.price * item.qty).toFixed(2)}</li>`;
      }
      html += '</ul>';
      cartList.innerHTML = html;
      cartTotal.innerText = `Total: $${total.toFixed(2)}`;
    }

    // Mostrar campo monto si el método es efectivo
    document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
      radio.addEventListener('change', function() {
        document.getElementById('amountReceived').style.display = this.value === 'cash' ? 'block' : 'none';
      });
    });

    // Procesar pago
    document.getElementById('paymentForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const tableNumber = document.getElementById('tableSelect').value;
      const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
      const totalText = document.getElementById('cartTotal').innerText.replace('Total: $', '');
      const amount = parseFloat(totalText);

      if (!tableNumber || !method || amount <= 0) {
        alert("Datos incompletos: seleccioná mesa, método y agregá productos.");
        return;
      }

      const response = await fetch('process_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          table_number: tableNumber,
          method: method,
          amount: amount,
          status: 'pagado'
        })
      });

      const data = await response.json();
      alert(data.message || data.error || 'Pago registrado correctamente');
    });

    loadProducts();
  </script>
</body>
</html>
