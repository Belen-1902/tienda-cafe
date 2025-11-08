<?php
include 'db.php';

// Obtener productos desde la base de datos (usando PDO)
$stmt = $pdo->query("SELECT id, name, price, image_path FROM products");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Cafetería Granitos</title>
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
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 15px;
        }
        .product-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: center;
            cursor: pointer;
            transition: transform .15s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .product-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        .product-info {
            padding: 10px;
        }
        .product-info h4 {
            font-size: 0.95rem;
            margin: 5px 0;
        }
        .product-info p {
            color: #e67c73;
            margin: 0;
        }
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
        <!-- Productos -->
        <div>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <?php
                        $imageFile = !empty($p['image_path']) ? $p['image_path'] : 'assets/img/no-image.jpg';
                        if (!file_exists(__DIR__ . '/' . $imageFile)) {
                            $imageFile = 'assets/img/no-image.jpg';
                        }
                    ?>
                    <div class="product-card" 
                        onclick="addToCart(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['name']); ?>', <?php echo $p['price']; ?>)">
                        <img src="<?php echo htmlspecialchars($imageFile); ?>" 
                             alt="<?php echo htmlspecialchars($p['name']); ?>" 
                             class="product-img">
                        <div class="product-info">
                            <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                            <p>$<?php echo number_format($p['price'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Carrito y Pago -->
        <div class="sidebar">
            <h3> Carrito</h3>
            <div id="cartList">(Carrito vacío)</div>
            <div id="cartTotal">Total: $0.00</div>

            <form id="paymentForm">
                <h3> Método de Pago</h3>
                <label><input type="radio" name="paymentMethod" value="modo"> Modo</label>
                <label><input type="radio" name="paymentMethod" value="qr"> QR</label>
                <label><input type="radio" name="paymentMethod" value="cash"> Efectivo</label>
                <label><input type="radio" name="paymentMethod" value="transfer"> Transferencia</label>
                <input type="number" id="amountReceived" placeholder="Monto Recibido (efectivo)" step="0.01" min="0" style="display:none;">
                <button type="submit"> Pagar</button>
            </form>
        </div>
    </main>

    <script>
    let cart = [];

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

    // Mostrar campo de monto solo si el método es efectivo
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
            alert("Datos incompletos: seleccioná mesa, método de pago y agregá productos.");
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
        alert(data.message || data.error || 'Pago procesado correctamente');
    });
    </script>
</body>
</html>
