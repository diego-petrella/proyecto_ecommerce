<?php
session_start();
require '../models/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
    header('Location: ../views/login.php');
    exit;
}

$id_usuario_sesion = (int)$_SESSION['usuario_id'];

// TOMO LOS DATOS DEL FORMULARIO
$datos_perfil = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'apellido' => trim($_POST['apellido'] ?? ''),
    'direccion' => trim($_POST['direccion'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? '')
];

// LLAMO A LA FUNCION ACTUALIZAR PERFIL
$exito = actualizarPerfilUsuario($id_usuario_sesion, $datos_perfil);

if ($exito) {
    $_SESSION['exito_perfil'] = '¡Tus datos se han actualizado correctamente!';
} else {
    $_SESSION['error_perfil'] = 'Hubo un error al actualizar tus datos. Inténtalo de nuevo.';
}

header('Location: ../views/mi_cuenta.php?tab=datos');
exit;

?>
