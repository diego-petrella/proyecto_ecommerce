<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] != 1) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Acceso no autorizado. La sesión ha expirado o no es administrador.']);
    exit;
}


require_once __DIR__ . '/../models/funciones.php'; 


$payload = file_get_contents("php://input");
$datos = json_decode($payload, true);

$respuesta = ['estado' => 'error', 'mensaje' => 'ID de usuario no proporcionado.']; 

if (isset($datos["id"])) {
    $id_usuario = (int) $datos["id"];
    
    $id_usuario_logueado = (int)$_SESSION['usuario_id'];
    $id_super_admin_protegido = 1; //USUARIO SUPER ADMIN

    // BLOQUEO ELIMINAR USUARIO SUPER ADMIN
    if ($id_usuario == $id_usuario_logueado || $id_usuario == $id_super_admin_protegido) {
         $respuesta['mensaje'] = 'No tienes permiso para desactivar este usuario.';
         echo json_encode($respuesta);
         exit;
    }
   
    
    
    $resultado = eliminarUsuario($id_usuario); 

    if ($resultado) {
        $respuesta['estado'] = 'ok';
        $respuesta['mensaje'] = 'Usuario ID ' . $id_usuario . ' desactivado con éxito.';
    } else {
        $respuesta['mensaje'] = 'Fallo en la base de datos al desactivar el usuario.';
    }
}


echo json_encode($respuesta);
exit;