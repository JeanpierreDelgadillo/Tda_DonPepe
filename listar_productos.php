<?php
// listar_productos.php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/inc.php'; // O usa 'base.php' según lo que use tu servidor

try {
    $pdo = db();
    
    $sql = 'SELECT p.id_producto, p.id_categoria, c.nombre AS categoria, p.nombre, p.precio, p.stock, p.activo
            FROM producto p 
            JOIN categoria c ON c.id_categoria = p.id_categoria
            WHERE p.activo = 1';
            
    $st = $pdo->prepare($sql);
    $st->execute();
    $filas = $st->fetchAll(PDO::FETCH_ASSOC);

    // Convertir tipos de datos para que Android los reciba correctamente
    foreach ($filas as &$f) {
        $f['id_producto'] = (int)$f['id_producto'];
        $f['id_categoria'] = (int)$f['id_categoria'];
        $f['precio'] = (float)$f['precio'];
        $f['stock'] = (int)$f['stock'];
        $f['activo'] = (int)$f['activo'];
    }

    // Estructura adaptada para Retrofit (puedes usar "ObjListaProducto" o "productos" según tu clase Response en Android)
    echo json_encode([
        "ObjListaProducto" => $filas
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "ObjListaProducto" => [],
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}