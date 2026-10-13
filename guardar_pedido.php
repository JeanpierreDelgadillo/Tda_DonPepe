<?php
// guardar_pedido.php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/inc.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(["rpta" => false, "mensaje" => "Datos inválidos"]);
    exit;
}

$id_cliente = $input['id_cliente'] ?? 1; 
$id_usuario = $input['id_usuario'] ?? 1; // Requerido por tu tabla venta (vendedor/admin)
$total = $input['total'] ?? 0;
$productos = $input['productos'] ?? []; // Array de [id_producto, cantidad, precio]

try {
    $pdo = db();
    $pdo->beginTransaction();

    // 1. Insertar en la tabla 'venta' (sin el campo estado que no existe en tu BD)
    $stmtVenta = $pdo->prepare("INSERT INTO venta (id_cliente, id_usuario, fecha, total) VALUES (?, ?, NOW(), ?)");
    $stmtVenta->execute([$id_cliente, $id_usuario, $total]);
    $id_venta = $pdo->lastInsertId();

    // 2. Insertar en la tabla 'detalle_venta' (usando 'precio_unit' y calculando 'subtotal')
    $stmtDetalle = $pdo->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unit, subtotal) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($productos as $p) {
        $cantidad = $p['cantidad'];
        $precio_unit = $p['precio'];
        $subtotal = $cantidad * $precio_unit;

        $stmtDetalle->execute([
            $id_venta,
            $p['id_producto'],
            $cantidad,
            $precio_unit,
            $subtotal
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "rpta" => true, 
        "mensaje" => "Pedido registrado exitosamente", 
        "id_pedido" => (int)$id_venta
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        "rpta" => false, 
        "mensaje" => "Error al registrar el pedido: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}