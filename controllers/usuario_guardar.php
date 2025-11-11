<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../controllers/verificacion_usuario.php';
require_once __DIR__ . '/../models/funciones.php';


if (!isset($_SESSION['usuario_rol_id']) || $_SESSION['usuario_rol_id'] != 1) {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Permiso denegado. Solo administradores pueden realizar esta acción.']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['estado' => 'error', 'mensaje' => 'Método de solicitud no permitido.']);
    exit;
}


//SE GUARDA COMO ACTIVO SIEMPRE
$activo_valor = $_POST['activo'] ?? $_POST['activo_default'] ?? 0;

$datos_usuario = [
    'id' => isset($_POST['id']) ? (int)$_POST['id'] : null,
    'email' => trim($_POST['email'] ?? ''),
    'id_rol' => (int)($_POST['id_rol'] ?? 2),
    'nombre' => trim($_POST['nombre'] ?? ''),
    'apellido' => trim($_POST['apellido'] ?? ''),
    'direccion' => trim($_POST['direccion'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'activo' => (int)$activo_valor,
    'password' => $_POST['password'] ?? null
];

$modo_edicion = ($datos_usuario['id'] !== null && $datos_usuario['id'] > 0);


$errores = [];

// Validar Email
if (empty($datos_usuario['email']) || !filter_var($datos_usuario['email'], FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El formato del email no es válido.";
} else {
    $usuario_existente = buscarUsuarioPorEmail($datos_usuario['email']);
    if ($usuario_existente && $usuario_existente['id'] != $datos_usuario['id']) {
        $errores[] = "El email '" . htmlspecialchars($datos_usuario['email']) . "' ya está en uso por otro usuario.";
    }
}

// Validar Nombre y Apellido
if (empty($datos_usuario['nombre'])) {
    $errores[] = "El nombre es obligatorio.";
}
if (empty($datos_usuario['apellido'])) {
    $errores[] = "El apellido es obligatorio.";
}

// Validar Contraseña
if (!$modo_edicion && empty($datos_usuario['password'])) {
    $errores[] = "La contraseña es obligatoria para nuevos usuarios.";
}
if (!empty($datos_usuario['password']) && strlen($datos_usuario['password']) < 6) {
    $errores[] = "La nueva contraseña debe tener al menos 6 caracteres.";
}


//PROCESAR O DEVOLVER ERRORES
if (empty($errores)) {
    try {
        $exito = guardarUsuario($datos_usuario);
        
        if ($exito) {
            $mensaje = $modo_edicion ? "Usuario actualizado correctamente." : "Usuario creado correctamente.";
            unset($_SESSION['error_abm_usuario']); 
            
            echo json_encode(['estado' => 'ok', 'mensaje' => $mensaje]);
            exit;
        } else {
            $errores[] = "Hubo un error al guardar el usuario en la base de datos. (Error de modelo o PDO).";
        }

    } catch (PDOException $e) {
        error_log("Error de BD al guardar usuario: " . $e->getMessage());
        $errores[] = "Error de base de datos. Contacte al administrador. (PDO Error).";
    }
}


if (!empty($errores)) {
    
    echo json_encode(['estado' => 'error', 'mensaje' => implode('<br>', $errores)]);
    exit;
}
?>