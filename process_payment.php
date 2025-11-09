<?php
include 'db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$table = $input['table_number'] ?? null;
$method = $input['method'] ?? null;
$amount = $input['amount'] ?? null;
$status = $input['status'] ?? 'pendiente';

if (!$table || !$method || !$amount) {
    echo json_encode(['error' => 'Datos incompletos (mesa, método o monto)']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO payments (table_number, method, amount, status, created_at)
                           VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$table, $method, $amount, $status]);
    echo json_encode(['message' => ' Pago registrado correctamente']);
} catch (Exception $e) {
    echo json_encode(['error' => ' Error al guardar en la base de datos: ' . $e->getMessage()]);
}
?>
