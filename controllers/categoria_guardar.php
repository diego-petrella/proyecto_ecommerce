<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../controllers/verificacion_usuario.php';
rolRequerido(1); 

require "../models/funciones.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
   
    echo json_encode(['estado' => 'error', 'mensaje' => 'Método de solicitud no permitido.']);
    exit;
}


$nombre = trim($_POST["nombre"] ?? '');
$id = (int)($_POST["id"] ?? 0);


$errores = [];

if (empty($nombre)) {
    $errores[] = "El nombre de la categoría es obligatorio.";
}


if (empty($errores)) {
    $datos_categoria = ["nombre" => $nombre];

    if ($id > 0) {
        $datos_categoria["id"] = $id;
    }

    try {
        if (guardarCategoria($datos_categoria)) {
            $mensaje_exito = ($id > 0) ? "Categoría actualizada correctamente." : "Categoría creada correctamente.";
            echo json_encode(['estado' => 'ok', 'mensaje' => $mensaje_exito]);
            exit;
        } else {
            $errores[] = "Hubo un error al guardar la categoría en la base de datos.";
        }
    } catch (PDOException $e) {
        error_log("Error de BD al guardar categoría: " . $e->getMessage());
        $errores[] = "Error de base de datos. Contacte al administrador.";
    }
}


if (!empty($errores)) {
    echo json_encode(['estado' => 'error', 'mensaje' => implode('<br>', $errores)]);
    exit;
}
?>