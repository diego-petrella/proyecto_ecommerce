<?php

session_start();
require '../models/funciones.php'; 

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id_categoria = $input['id'] ?? 0;

if ($id_categoria == 0) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error: No se recibió un ID de categoría válido.']);
    exit;
}


$conteo_productos = contarProductosPorCategoria($id_categoria);

if ($conteo_productos > 0) {
    echo json_encode([
        'estado' => 'error',
        'mensaje' => "No se puede desactivar: Esta categoría tiene {$conteo_productos} productos activos asociados."
    ]);
    exit;
}


$exito = desactivarCategoria($id_categoria); 


if ($exito) {
    echo json_encode([
        'estado' => 'ok',
        'mensaje' => 'Categoría desactivada con éxito.'
    ]);
} else {
    echo json_encode([
        'estado' => 'error',
        'mensaje' => 'Error al desactivar la categoría en la base de datos.'
    ]);
}
exit;
?>