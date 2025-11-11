<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] != 1) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Acceso no autorizado.']);
    exit;
}

require "../models/funciones.php";


$payload = file_get_contents("php://input");
$datos = json_decode($payload, true);

$respuesta = ['estado' => 'error', 'mensaje' => 'ID no proporcionado en la solicitud.']; 

if (isset($datos["id"])) {
    $id_usuario = (int) $datos["id"];
    

    $resultado = restaurarUsuario($id_usuario); 

    if ($resultado) {
        $respuesta['estado'] = 'ok';
        $respuesta['mensaje'] = 'Usuario ID ' . $id_usuario . ' restaurado con éxito.';
    } else {
        $respuesta['mensaje'] = 'Fallo en la base de datos al restaurar el usuario.';
    }
}


echo json_encode($respuesta);
exit;

?>