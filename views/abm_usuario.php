<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/programacion2/articulos/');
}

require_once(__DIR__ . '/../controllers/verificacion_usuario.php');
rolRequerido(1);
require_once(__DIR__ . '/../models/funciones.php');

$id = $_GET['id'] ?? 0;
$id = (int)$id;
$es_edicion = ($id != 0);

$roles = obtenerRoles(); 
$usuario = [];

// Recuperar errores y datos viejos de la sesión si vienen del controlador
$errores = $_SESSION['error_abm_usuario'] ?? [];
$datos_viejos = $_SESSION['datos_abm_usuario'] ?? [];
unset($_SESSION['error_abm_usuario'], $_SESSION['datos_abm_usuario']);

if ($es_edicion) {
    $titulo = "Modificar Usuario";
    
    if (!empty($datos_viejos)) {
        $usuario = $datos_viejos;
        $usuario['id'] = $id; 
    } else {
        $usuario = buscarUsuarioPorIdAdmin($id); 
    }
    
    if (!$usuario) {
        $es_edicion = false;
        $id = 0;
        $titulo = "Usuario no encontrado";
    }
    
} else {
    $titulo = "Crear Nuevo Usuario";
    
    if (!empty($datos_viejos)) {
        $usuario = $datos_viejos;
    } else {
        $usuario = [
            "id" => 0,
            "nombre" => "",
            "apellido" => "",
            "email" => "",
            "direccion" => "",
            "telefono" => "",
            "activo" => 1,
            "id_rol" => 2 
        ];
    }
}
if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) {
    require '../includes/admin_nav.php'; 
}
?>

<div class="container-fluid py-3"> 
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="bi bi-person-lines-fill me-2"></i> 
            <?php echo $titulo; ?>
        </h1>
        
        <a href="javascript:void(0);" 
           onclick="loadPage('gestion_usuarios.php', 'usuarios')" 
           class="btn btn-secondary" title="Volver al Listado">
             <i class="bi bi-arrow-left me-2"></i> Volver
        </a>
    </div>

    <?php if (!$usuario && $es_edicion): ?>
        <div class="alert alert-danger" role="alert">
            El usuario solicitado no fue encontrado o ya no existe.
        </div>
    <?php else: ?>
        <div class="card p-4 shadow-sm">
            
            <div id="alertPlaceholder" class="mb-4">
                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <?php echo implode('<br>', $errores); ?>
                    </div>
                <?php endif; ?>
            </div>

            <form id="abmUsuarioForm" class="js-ajax-form" action="<?php echo BASE_URL; ?>controllers/usuario_guardar.php" method="POST">
                
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-8">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required <?php echo $es_edicion ? 'readonly' : ''; ?>>
                        <?php if ($es_edicion): ?>
                            <div class="form-text">El email no se puede modificar en modo edición.</div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label for="id_rol" class="form-label">Rol *</label>
                        <select id="id_rol" name="id_rol" class="form-select" required>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol['id']; ?>" <?php echo (isset($usuario['id_rol']) && $rol['id'] == $usuario['id_rol']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rol['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                    </div>

                    <div class="col-12">
                        <hr class="my-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="<?php echo $es_edicion ? 'Dejar vacío para no cambiar' : 'Requerido'; ?>"
                               <?php echo !$es_edicion ? 'required' : ''; ?>>
                        <div class="form-text">
                            <?php echo $es_edicion ? 'Dejar en blanco para no modificar la contraseña actual.' : 'La contraseña es obligatoria para usuarios nuevos.'; ?>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" <?php echo ($usuario['activo'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="activo">Usuario Activo</label>
                            <input type="hidden" name="activo_default" value="0"> 
                        </div>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="button" onclick="loadPage('gestion_usuarios.php', 'usuarios')" class="btn btn-secondary me-2">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> Guardar Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>