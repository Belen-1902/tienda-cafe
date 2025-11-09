<?php
$menu_url = "http://localhost/cafeteria_full/index.php";
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>QR Menú</title>
<style>body{font-family:Arial;text-align:center;padding:40px;background:#fff7f5}img{border:6px solid #ffb3a7;border-radius:12px}</style>
</head><body><h1>Escaneá para ver el menú </h1>
<img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo urlencode($menu_url); ?>" alt="QR">
<p><a href="<?php echo htmlspecialchars($menu_url); ?>" target="_blank"><?php echo htmlspecialchars($menu_url); ?></a></p>
</body></html>