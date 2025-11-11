<?php
session_start();
require '../models/funciones.php'; 

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] != 1) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Acceso denegado.']);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);
$id_articulo = $input['id'] ?? 0;

if ($id_articulo == 0) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Error: No se recibió un ID de artículo válido.']);
    exit;
}

$exito = restaurarArticulo($id_articulo); 

if ($exito) {
    echo json_encode([
        'estado' => 'ok',
        'mensaje' => 'Artículo restaurado con éxito.'
    ]);
} else {
    echo json_encode([
        'estado' => 'error',
        'mensaje' => 'Error al restaurar el artículo en la base de datos.'
    ]);
}
exit;
?>
