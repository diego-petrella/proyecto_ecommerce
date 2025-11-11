<?php
session_start();
require "../models/funciones.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id'])) {
    header('Location: ../views/mi_cuenta.php?tab=password');
    exit;
}


$id_usuario = (int)$_SESSION['usuario_id'];
$pass_actual = $_POST['pass_actual'] ?? '';
$pass_nueva = $_POST['pass_nueva'] ?? '';
$pass_confirm = $_POST['pass_confirm'] ?? '';


if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirm)) {
    $_SESSION['error_pass'] = "Por favor, completa todos los campos.";
    header('Location: ../views/mi_cuenta.php?tab=password');
    exit;
}
if (strlen($pass_nueva) < 6) {
    $_SESSION['error_pass'] = "La nueva contraseña debe tener al menos 6 caracteres.";
    header('Location: ../views/mi_cuenta.php?tab=password');
    exit;
}
if ($pass_nueva !== $pass_confirm) {
    $_SESSION['error_pass'] = "Las contraseñas nuevas no coinciden.";
    header('Location: ../views/mi_cuenta.php?tab=password');
    exit;
}

// VERIFICAR CONTRASEÑA ACTUAL

$hash_actual_bd = obtenerHashPasswordPorId($id_usuario);

if (!$hash_actual_bd || !password_verify($pass_actual, $hash_actual_bd)) {
    $_SESSION['error_pass'] = "La contraseña actual es incorrecta.";
    header('Location: ../views/mi_cuenta.php?tab=password');
    exit;
}

try {
    $nuevo_hash = password_hash($pass_nueva, PASSWORD_BCRYPT);
    
    $exito = actualizarPasswordUsuario($id_usuario, $nuevo_hash);

    if ($exito) {
        $_SESSION['exito_pass'] = "¡Contraseña actualizada con éxito!";
    } else {
        $_SESSION['error_pass'] = "No se pudo actualizar la contraseña en la base de datos.";
    }

} catch (PDOException $e) {
    error_log("Error de BD al cambiar password: " . $e->getMessage());
    $_SESSION['error_pass'] = "Error de base de datos. Intente más tarde.";
}

header('Location: ../views/mi_cuenta.php?tab=password');
exit;
?>

