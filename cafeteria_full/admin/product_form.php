<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require '../db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = number_format((float)($_POST['price'] ?? 0), 2, '.', '');
    $stock = intval($_POST['stock'] ?? 0);

    // Manejo de imagen
    $image = $product['image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $filename = uniqid('p_') . '.' . preg_replace('/[^a-z0-9]/', '', $ext);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = 'uploads/' . $filename;
        }
    }

    if ($id) {
        $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?');
        $stmt->execute([$name, $description, $price, $stock, $image, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, stock, image) VALUES (?,?,?,?,?)');
        $stmt->execute([$name, $description, $price, $stock, $image]);
    }

    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?php echo $product ? 'Editar' : 'Nuevo'; ?> producto</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    body { background-color: #fff8f6; font-family: Arial, sans-serif; }
    .container { max-width: 600px; margin: 40px auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .product-form label { display: block; margin-bottom: 12px; font-weight: bold; color: #444; }
    .product-form input, .product-form textarea { width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; margin-top: 4px; }
    .product-form button { background-color: #ff7f66; border: none; padding: 10px 16px; border-radius: 6px; color: white; font-size: 1rem; cursor: pointer; }
    .product-form button:hover { background-color: #ff4f33; }
    .btn-cancel { text-decoration: none; color: #555; margin-left: 10px; }
  </style>
</head>
<body>
  <main class="container">
    <form method="post" enctype="multipart/form-data" class="product-form">
      <h2><?php echo $product ? 'Editar' : 'Nuevo'; ?> producto</h2>

      <label>Nombre
        <input name="name" required value="<?php echo $product ? htmlspecialchars($product['name']) : ''; ?>">
      </label>

      <label>Precio
        <input name="price" type="number" step="0.01" required value="<?php echo $product ? htmlspecialchars($product['price']) : '0.00'; ?>">
      </label>

      <label>Stock
        <input name="stock" type="number" required value="<?php echo $product ? intval($product['stock']) : 0; ?>">
      </label>

      <label>Descripción
        <textarea name="description"><?php echo $product ? htmlspecialchars($product['description']) : ''; ?></textarea>
      </label>

      <label>Imagen
        <?php if ($product && !empty($product['image']) && file_exists(__DIR__ . '/../' . $product['image'])): ?>
          <img src="../<?php echo htmlspecialchars($product['image']); ?>" style="max-width:140px;display:block;margin-bottom:8px;">
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
      </label>

      <div style="margin-top:10px">
        <button type="submit">Guardar</button>
        <a href="dashboard.php" class="btn-cancel">Cancelar</a>
      </div>
    </form>
  </main>
</body>
</html>
