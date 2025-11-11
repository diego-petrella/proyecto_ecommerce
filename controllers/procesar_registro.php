<?php
session_start();
require "../models/funciones.php";


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/registro.php');
    exit;
}


$email = trim($_POST['email'] ?? '');
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';
$rol_cliente = 2; // ID ROL CLIENTE SIEMPRE


$errores = [];

if (empty($email) || empty($pass1) || empty($pass2)) {
    $errores[] = "Todos los campos son obligatorios.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El formato del correo electrónico no es válido.";
}
if (strlen($pass1) < 6) { 
    $errores[] = "La contraseña debe tener al menos 6 caracteres.";
}
if ($pass1 !== $pass2) {
    $errores[] = "Las contraseñas no coinciden.";
}


if (empty($errores)) {
    
    try {
        //BUSCO SI EXISTE EL EMAIL
        $usuario_existente = buscarUsuarioPorEmail($email);
        if ($usuario_existente) {
            $errores[] = "El correo electrónico ya está registrado.";
        } else {
            //HASH CONTRASEÑA
            $password_hash = password_hash($pass1, PASSWORD_BCRYPT);
            
            //GUARDO
            $nuevo_usuario_id = registrarUsuario($email, $password_hash, $rol_cliente);
            
            if ($nuevo_usuario_id) {
                //INICIO SESION AUTOMATICAMENTE
                $_SESSION['usuario_id'] = $nuevo_usuario_id;
                $_SESSION['usuario_email'] = $email;
                $_SESSION['usuario_rol_id'] = $rol_cliente;
                header('Location: ../index.php?registro=exitoso');
                exit;
            } else {
                $errores[] = "Error al crear el usuario. Intente más tarde.";
            }
        }

    } catch (PDOException $e) {
        error_log("Error de BD al registrar: " . $e->getMessage());
        $errores[] = "Ocurrió un error inesperado. Por favor, intente de nuevo.";
    }
}


if (!empty($errores)) {
    $_SESSION['errores_registro'] = $errores;
    $_SESSION['datos_registro'] = ['email' => $email]; 
    header('Location: ../views/registro.php');
    exit;
}
?>

