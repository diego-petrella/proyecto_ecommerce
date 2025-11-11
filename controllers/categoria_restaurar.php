<?php
session_start();
require '../models/funciones.php'; 


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['estado' => 'error', 'mensaje' => 'Acceso denegado.']);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);
$id_categoria = $input['id'] ?? 0;

if ($id_categoria == 0) {
    header('Content-Type: application/json');
    echo json_encode(['estado' => 'error', 'mensaje' => 'ID de categoría no válido.']);
    exit;
}


$exito = restaurarCategoria($id_categoria); 


header('Content-Type: application/json');
if ($exito) {
    echo json_encode([
        'estado' => 'ok',
        'mensaje' => 'Categoría restaurada con éxito.'
    ]);
} else {
    echo json_encode([
        'estado' => 'error',
        'mensaje' => 'Error al restaurar la categoría en la base de datos.'
    ]);
}
exit;
?>