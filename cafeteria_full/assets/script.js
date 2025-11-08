let cart = [];

// Cargar productos según mesa
function updateProducts() {
    const tableId = document.getElementById('tableSelect').value;
    const productList = document.getElementById('productList');
    if (!tableId) {
        productList.innerHTML = '<p>Selecciona una mesa para ver productos</p>';
        return;
    }

    
    const demoProducts = [
        {id: 1, name: 'Café Americano', price: 150},
        {id: 2, name: 'Cortado', price: 180},
        {id: 3, name: 'Medialuna', price: 120}
    ];

    productList.innerHTML = demoProducts.map(p => 
        `<div class="product" onclick="addToCart(${p.id}, '${p.name}', ${p.price})">${p.name} - $${p.price}</div>`
    ).join('');
}

function addToCart(id, name, price) {
    const tableId = document.getElementById('tableSelect').value;
    if (!tableId) { alert('Selecciona una mesa primero'); return; }
    const existing = cart.find(i=>i.id===id);
    if (existing) existing.qty++;
    else cart.push({id, name, price, qty:1, table: parseInt(tableId)});
    renderCart();
}

function renderCart() {
    const list = document.getElementById('cartList');
    if (cart.length === 0) { list.innerHTML = '(Carrito vacío)'; document.getElementById('cartTotal').innerText = 'Total: $0.00'; return; }
    list.innerHTML = cart.map(item => `<div>${item.qty} × ${item.name} — $${(item.price*item.qty).toFixed(2)}</div>`).join('');
    document.getElementById('cartTotal').innerText = 'Total: $' + getCartTotal().toFixed(2);
}

function getCartTotal() {
    return cart.reduce((s,i)=> s + i.price * i.qty, 0);
}

document.getElementById('paymentForm').addEventListener('change', function(e){
    const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
    const amountReceived = document.getElementById('amountReceived');
    if (method === 'cash') amountReceived.style.display = 'block'; else amountReceived.style.display = 'none';
});

document.getElementById('paymentForm').addEventListener('submit', function(e){
    e.preventDefault();
    if (cart.length === 0) { alert('El carrito está vacío'); return; }
    const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
    if (!method) { alert('Selecciona un método de pago'); return; }
    const table_number = cart[0].table;
    const total = getCartTotal();
    if (method === 'cash') {
        const received = parseFloat(document.getElementById('amountReceived').value || 0);
        const change = (received - total).toFixed(2);
        if (received < total) { alert('Monto recibido menor al total'); return; }
        
    }

    // Determinar estado según método
    const status = (method === 'qr' || method === 'modo') ? 'pagado' : 'pendiente';

    // Enviar al backend
    fetch('process_payment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            table_number: table_number,
            method: method,
            amount: total,
            status: status
        })
    }).then(res=>res.json())
      .then(data=>{
          const msg = document.getElementById('message');
          if (data.success) {
              msg.innerText = '✅ ' + data.message;
              // Si es QR: mostrar QR para que el cliente pague (simulación)
              if (method === 'qr') {
                  document.getElementById('qrCode').style.display = 'block';
                  QRCode.toCanvas(document.getElementById('qrCode'), `Pago mesa ${table_number} - $${total.toFixed(2)}`, function (error) {
                      if (error) console.error(error);
                  });
              } else {
                  document.getElementById('qrCode').style.display = 'none';
              }
              // Vaciar carrito
              cart = [];
              renderCart();
          } else {
              msg.innerText = '❌ ' + data.message;
          }
      }).catch(err => {
          console.error(err);
          alert('Error al comunicarse con el servidor');
      });
});