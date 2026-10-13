<?php
// listar_pedidos.php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/inc.php';

$id_cliente = $_GET['id_cliente'] ?? 1;

try {
    $pdo = db();
    // Como tu tabla venta no tiene 'estado', le mandamos un estado por defecto "Completado" para la app
    $stmt = $pdo->prepare("SELECT id_venta AS id_pedido, fecha, total, 'Completado' AS estado FROM venta WHERE id_cliente = ? ORDER BY id_venta DESC");
    $stmt->execute([$id_cliente]);
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ventas as &$v) {
        $v['id_pedido'] = (int)$v['id_pedido'];
        $v['total'] = (float)$v['total'];
    }

    echo json_encode([
        "ObjListaPedido" => $ventas
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "ObjListaPedido" => [],
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}