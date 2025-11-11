<?php
session_start();
require __DIR__ . '/../controllers/verificacion_usuario.php';
rolRequerido(1);

if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }

require __DIR__ . "/../models/funciones.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$categoria = [
    "id" => 0,
    "nombre" => ""
];
$titulo = "Crear Nueva Categoría";

if ($id != 0) {
    $datos = buscarCategoriaPorId($id);
    
    if ($datos) {
        $categoria = $datos;
        $titulo = "Modificar Categoría ID: " . $id;
    } else {
        $redirect_ajax = "javascript:loadPage('categorias.php', 'categorias')";
        echo "<script>alert('Categoría no encontrada. Volviendo al listado.'); window.location.href = '$redirect_ajax';</script>";
        exit;
    }
}

if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) {
    require '../includes/admin_nav.php'; 
}
?>
<div class="container-fluid py-3">
    
    <h1 class="mb-4"><?php echo $titulo ?></h1>

    <div class="card p-4 shadow-lg mx-auto" style="max-width: 500px;">
        
    <form id="abmCategoriaForm" class="js-ajax-form" 
    data-success-page="categorias.php" 
    data-success-view="categorias" 
    action="<?php echo BASE_URL; ?>controllers/categoria_guardar.php" method="POST"> 
    
    <?php if ($id != 0) { ?>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($categoria["id"]) ?>" />
    <?php } ?>

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre de la Categoría</label>
        <input type="text" class="form-control" id="nombre" name="nombre" 
            value="<?php echo htmlspecialchars($categoria["nombre"] ?? '') ?>" required>
    </div>

    <div class="text-center mt-4 border-top pt-3">
        <button type="submit" class="btn btn-primary btn-lg me-3">
            <i class="bi bi-save me-2"></i> Guardar
        </button>
        
        <a href="javascript:void(0);" onclick="loadPage('categorias.php', 'categorias')" class="btn btn-secondary btn-lg">
            <i class="bi bi-x-circle me-2"></i> Cancelar
        </a>
    </div>
</form>
        
    </div>
</div>
<?php 
// Si accedimos directamente a la página, cerramos el HTML
if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) {
    echo '</div> ';
    echo '</div> ';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '</body></html>';
}
?>