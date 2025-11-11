<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/verificacion_usuario.php';
rolRequerido(1);
require_once __DIR__ . "/../models/funciones.php";


if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$action = $_GET['action'] ?? null;
$token = md5(session_id()); 

$panel_admin_url = BASE_URL . 'views/panel_admin.php';

if ($action === 'nuevo') {
    if (($_GET['token'] ?? null) !== $token) {
        header("Location: " . $panel_admin_url . "?error=token");
        exit;
    }
} elseif ($id == 0 && $action !== 'nuevo') {
    header("Location: " . $panel_admin_url);
    exit;
}


if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol_id'] != 1) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "views/login.php");
    exit;
}

$categorias_select = obtenerTodasCategoriasActivas(); 


if ($id != 0) {
    $articulo = buscarPorId($id);
    if ($articulo === false) {
        header("Location: " . $panel_admin_url . "?error=articulo_no_encontrado");
        exit; 
    }
    $titulo = "Modificar Artículo #{$id}";
} else {
    $articulo = [
        "nombre" => "",
        "precio" => "", 
        "id_categoria" => "",
        "imagen" => "",
        "descripcion_corta" => "",
        "stock" => "",
        "activo" => 1 
    ];
    $titulo = "Nuevo Artículo";
}

$es_activo = isset($articulo['activo']) && $articulo['activo'] == 1;


$es_contenido_ajax = true; 

if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) {
    require '../includes/admin_nav.php'; 
}
?>

<!-- INICIO CONTENIDO A INYECTAR -->
<div class="container-fluid py-3">
    
    <h1 class="mb-4"><?php echo $titulo ?></h1>
    
    <div class="card p-3 shadow-lg mx-auto" style="max-width: 900px;">
        
       
        <form method="post" action="<?php echo BASE_URL; ?>controllers/articulo_guardar.php" enctype="multipart/form-data">

            <?php if ($id != 0) { ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($articulo["id"]) ?>" />
            <?php } ?>

            <div class="row g-3">
                <div class="col-md-7">
                    <div class="mb-2">
                        <label for="nombre" class="form-label">Nombre del Artículo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                            value="<?php echo htmlspecialchars($articulo["nombre"] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="col-md-5">
                     <div class="mb-2">
                        <label for="id_categoria" class="form-label">Categoría:</label>
                        <select class="form-select" id="id_categoria" name="id_categoria" required>
                            <option value="">Seleccione una Categoría</option>
                            <?php 
                            foreach ($categorias_select as $categoria) { 
                                $selected = (($articulo['id_categoria'] ?? 0) == $categoria['id']) ? 'selected' : '';
                                echo "<option value='{$categoria['id']}' {$selected}>" . htmlspecialchars($categoria['nombre']) . "</option>";
                            } 
                            ?>
                        </select>
                    </div>
                </div>
            </div> 

            <div class="row">
                <div class="col-md-7">
                    <div class="mb-2">
                        <label for="precio" class="form-label">Precio ($)</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                            value="<?php echo htmlspecialchars($articulo["precio"] ?? '') ?>" required>
                        <div class="form-text small">Usar punto como separador decimal. Ej: 1250.50</div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-2">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" 
                            value="<?php echo htmlspecialchars($articulo["stock"] ?? 0) ?>" required>
                    </div>
                </div>
            </div> 

            <div class="mb-2">
                <label for="descripcion_corta" class="form-label">Descripción Corta</label>
                <textarea class="form-control" id="descripcion_corta" name="descripcion_corta" rows="2"><?php echo htmlspecialchars($articulo["descripcion_corta"] ?? '') ?></textarea>
            </div>

            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="mb-2">
                        <label for="imagen" class="form-label">Imagen del Artículo</label>
                        <input type="file" class="form-control form-control-sm" id="imagen" name="imagen" accept="image/*">
                        <?php if ($id != 0 && !empty($articulo['imagen'])) { ?>
                            <div class="mt-1">
                                <p class="mb-0 small">Imagen actual:</p>
                             
                                <img src="<?php echo BASE_URL; ?>assets/img/<?php echo htmlspecialchars($articulo['imagen']) ?>" alt="Imagen de producto" style="max-width: 100px; height: auto; border-radius: 4px;">
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label d-block">Estado</label>
                        <input type="hidden" name="activo" value="0">
                        <div class="form-check form-switch"> 
                            <input class="form-check-input" type="checkbox" role="switch" id="activo" name="activo" value="1" 
                                   <?php echo $es_activo ? 'checked' : '' ?>>
                            <label class="form-check-label" for="activo">Artículo Activo / Visible</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 border-top pt-3">
                <button type="submit" class="btn btn-primary btn-lg me-3">
                    <i class="bi bi-save me-2"></i> Guardar
                </button>
                
                <a href="javascript:void(0);" onclick="loadPage('articulo1.php', 'articulos')" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle me-2"></i> Cancelar
                </a>
            </div>
        </form>
        
    </div>
</div>
<!-- FIN DEL CONTENIDO INYECTADO-->

<?php 

if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) {
    echo '</div> <!-- Cierra el div class="flex-grow-1 p-4" -->';
    echo '</div> <!-- Cierra el div class="d-flex" -->';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '</body></html>';
}
?>