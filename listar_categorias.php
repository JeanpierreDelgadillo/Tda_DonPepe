<?php
// listar_categorias.php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/inc.php';

try {
    $pdo = db();
    $st = $pdo->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre");
    $categorias = $st->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categorias as &$c) {
        $c['id_categoria'] = (int)$c['id_categoria'];
    }

    echo json_encode([
        "ObjListaCategoria" => $categorias
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "ObjListaCategoria" => [],
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}