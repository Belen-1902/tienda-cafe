<?php
// admin/payments.php - lista pagos (requiere sesión activa)
session_start();
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header('Location: login.php');
    exit;
}
include '../db.php';
$res = mysqli_query($conn, "SELECT * FROM payments ORDER BY date DESC");
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Admin - Pagos</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Pagos registrados</h1>
<table class="payments">
<thead><tr><th>ID</th><th>Mesa</th><th>Método</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr></thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($res)): ?>
<tr class="state-<?php echo $row['status']; ?>">
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['table_number']; ?></td>
<td><?php echo $row['method']; ?></td>
<td>$<?php echo number_format($row['amount'],2); ?></td>
<td><?php echo $row['status']; ?></td>
<td><?php echo $row['date']; ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<p><a href="dashboard.php">Volver al dashboard</a></p>
</body>
</html>
