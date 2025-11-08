<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require '../db.php';

// Cargar productos ordenados por ID descendente
$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();

// Rutas correctas
$uploadDir = __DIR__ . '/../uploads/';   // ruta física (para verificar existencia)
$uploadUrl = '../uploads/';              // ruta pública (para mostrar en navegador)
$placeholder = '../assets/no-image.png'; // imagen por defecto
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Admin - Dashboard</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #fff8f6;
    margin: 0;
    padding: 0;
}
.site-header.admin {
    background-color: #ffb6a3;
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.site-header.admin h1 {
    margin: 0;
    font-size: 1.6rem;
}
.site-header.admin nav a {
    color: white;
    text-decoration: none;
    margin-left: 10px;
    font-weight: bold;
}
.container {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s;
}
.card:hover { transform: translateY(-5px); }
.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.card-content {
    padding: 12px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.card-content h3 {
    margin: 0 0 6px;
    font-size: 1.1rem;
    color: #333;
}
.card-content p {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
    flex-grow: 1;
}
.card-content .price {
    font-weight: bold;
    color: #e67c73;
    margin: 8px 0;
}
.card-actions {
    display: flex;
    justify-content: space-between;
}
.card-actions a {
    text-decoration: none;
    color: white;
    background-color: #ff7f66;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 0.85rem;
    transition: background 0.2s;
}
.card-actions a:hover { background-color: #ff4f33; }
.no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    color: #999;
    font-size: 14px;
    height: 180px;
}
.empty-message {
    grid-column: 1 / -1;
    text-align: center;
    color: #777;
    font-size: 1.2rem;
    padding: 40px 0;
}
</style>
</head>
<body>
<header class="site-header admin">
    <h1>Panel Admin</h1>
    <nav>
        <a href="product_form.php">Nuevo producto</a> |
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="container">
<?php if (empty($products)): ?>
    <div class="empty-message">No hay productos cargados aún.</div>
<?php else: ?>
    <?php foreach ($products as $p): ?>
        <?php
            // Limpiar el nombre del archivo
            $imageFile = !empty($p['image']) ? basename($p['image']) : null;
            $serverPath = $imageFile ? $uploadDir . $imageFile : null;
            $imgSrc = ($imageFile && file_exists($serverPath))
                        ? $uploadUrl . $imageFile . '?t=' . time()
                        : $placeholder;
        ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Imagen del producto">
            <div class="card-content">
                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                <p><?php echo htmlspecialchars(mb_strimwidth($p['description'], 0, 60, '...')); ?></p>
                <div class="price">$ <?php echo number_format($p['price'], 2, ',', '.'); ?></div>
                <div class="card-actions">
                    <a href="product_form.php?id=<?php echo $p['id']; ?>">Editar</a>
                    <a href="delete.php?id=<?php echo $p['id']; ?>" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</main>
</body>
</html>
