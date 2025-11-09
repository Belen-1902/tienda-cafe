<?php
require 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id <= 0){ 
    header('Location: index.php'); 
    exit; 
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if(!$product){ 
    header('Location: index.php'); 
    exit; 
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($product['name']); ?> | Cafetería Aroma y Sabor</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="site-header">
    <h1>Cafetería Aroma y Sabor</h1>
    <nav><a href="index.php">Volver</a> | <a href="admin/login.php">Admin</a></nav>
  </header>

  <main class="container">
    <div class="detail-card">
      <div class="detail-image">
        <?php 
        // Usamos 'image' en lugar de 'image_path'
        if(!empty($product['image']) && file_exists(__DIR__ . '/' . $product['image'])): ?>
          <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
          <div class="no-image">Sin imagen</div>
        <?php endif; ?>
      </div>

      <div class="detail-info">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <p class="brand"><?php echo htmlspecialchars($product['brand']); ?></p>
        <p class="price">$ <?php echo number_format($product['price'],2,',','.'); ?></p>
        <p class="stock">Stock: <?php echo intval($product['stock']); ?></p>

        <h3>Detalle</h3>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
      </div>
    </div>
  </main>
</body>
</html>
