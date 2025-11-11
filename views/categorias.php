<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../controllers/verificacion_usuario.php';
rolRequerido(1);
if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }
$ruta_script_actual = $_SERVER['SCRIPT_NAME'];

if (strpos($ruta_script_actual, 'panel_admin.php') === false) {

    if (ob_get_length()) {
        ob_end_clean();
    }
    
    header('Location: ' . BASE_URL . 'views/panel_admin.php?view=categorias');
    exit;
}
require_once "../models/funciones.php";

//PAGINACIÓN
$items_por_pagina = 10;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($pagina_actual - 1) * $items_por_pagina;

//FILTRADO
$filtro_estado = isset($_GET["estado"]) ? $_GET["estado"] : "1"; // Default: Activos

//OBTENER DATOS
$total_categorias = contarTotalCategorias($filtro_estado); 
$total_paginas = ceil($total_categorias / $items_por_pagina);

$categorias = obtenerCategorias($filtro_estado, $items_por_pagina, $offset); 

//Para mantener los filtros en los enlaces de paginación
$filtros_query = http_build_query([
    'estado' => $filtro_estado
]);

// Bandera para indicar que este archivo NO debe ser cargado directamente
$es_contenido_ajax = true; 

?>
<div class="container-fluid py-3"> 
    
    <h1 class="mb-4">Gestión de Categorías</h1>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Listado de Categorías (<?php echo $total_categorias; ?>)</h2>
        
        <a href="javascript:void(0);" 
            onclick="loadPage('abm_categoria.php', 'abm_categoria')" 
            class="btn btn-success">
             <i class="bi bi-plus-circle me-2"></i> Crear Nueva Categoría
        </a>
    </div>

    <div class="card p-3 mb-4 shadow-sm">
        <form action="<?php echo BASE_URL; ?>views/panel_admin.php" method="GET" class="row g-3 align-items-end" id="categoryFilterForm">
            <input type="hidden" name="view" value="categorias">
            
            <div class="col-md-4">
                <label for="estado" class="form-label">Filtrar por Estado:</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="1" <?php echo ($filtro_estado == '1') ? 'selected' : ''; ?>>Activas</option>
                    <option value="0" <?php echo ($filtro_estado == '0') ? 'selected' : ''; ?>>Inactivas</option>
                    <option value="" <?php echo ($filtro_estado == '') ? 'selected' : ''; ?>>Todas</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0);" onclick="loadPage('panel_admin.php?view=categorias', 'categorias')" class="btn btn-secondary w-100" title="Limpiar Filtros">
                    <i class="bi bi-eraser"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
    
    <div class="table-responsive shadow-sm">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 50%;">Nombre</th>
                    <th style="width: 15%;" class="text-center">Artículos</th>
                    <th style="width: 15%;" class="text-center">Estado</th> 
                    <th style="width: 20%;" class="text-center">Acciones</th> 
                </tr>
            </thead>
            <tbody>
                <?php 
                if (empty($categorias)): 
                ?>
                    <tr>
                        <td colspan="4" class="text-center p-4 text-muted">No hay categorías que coincidan con el filtro.</td>
                    </tr>
                <?php 
                else:
                    foreach($categorias as $categoria) { 
                        $es_inactivo = ($categoria['activo'] == 0);
                        $clase_fila = $es_inactivo ? 'table-light text-muted' : '';
                        $badge_estado = $es_inactivo ? 
                            '<span class="badge bg-danger">Inactiva</span>' : 
                            '<span class="badge bg-success">Activa</span>';
                        $conteo_articulos = $categoria["conteo_articulos"] ?? 0;
                ?>
                        <tr id="fila_<?php echo htmlspecialchars($categoria['id']); ?>" class="<?php echo $clase_fila; ?>">
                            <td><?php echo htmlspecialchars($categoria["nombre"]) ?></td>
                            <td class="text-center">
                                <span class="<?php echo ($conteo_articulos > 0) ? 'fw-bold' : 'text-muted'; ?>">
                                    <?php echo htmlspecialchars($conteo_articulos) ?>
                                </span>
                            </td>
                            <td class="text-center"><?php echo $badge_estado; ?></td>
                            
                            <td class="text-center">
                                <a href="javascript:void(0);" 
                                    onclick="loadPage('abm_categoria.php?id=<?php echo htmlspecialchars($categoria["id"]) ?>', 'abm_categoria')" 
                                    class="btn btn-sm btn-warning me-1" title="Editar Categoría">
                                     <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <?php if ($es_inactivo): ?>
                                    <a class="btn btn-sm btn-info" 
                                        onclick="restaurarCategoria(<?php echo htmlspecialchars($categoria['id']); ?>)"
                                        title="Restaurar Categoría">
                                         <i class="bi bi-arrow-clockwise"></i>
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-danger" 
                                        onclick="desactivarCategoria(<?php echo htmlspecialchars($categoria['id']); ?>)"
                                        title="Desactivar Categoría">
                                         <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                <?php 
                    } 
                endif; 
                ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_paginas > 1): ?>
    <nav aria-label="Paginación de categorías" class="mt-4">
        <ul class="pagination justify-content-center">
            
            <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" 
                    href="javascript:void(0)"
                    onclick="loadPage('panel_admin.php?view=categorias&p=<?php echo $pagina_actual - 1; ?>&<?php echo $filtros_query; ?>', 'categorias')">Anterior</a>
            </li>
            
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                    <a class="page-link" 
                        href="javascript:void(0)"
                        onclick="loadPage('panel_admin.php?view=categorias&p=<?php echo $i; ?>&<?php echo $filtros_query; ?>', 'categorias')"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                <a class="page-link" 
                    href="javascript:void(0)"
                    onclick="loadPage('panel_admin.php?view=categorias&p=<?php echo $pagina_actual + 1; ?>&<?php echo $filtros_query; ?>', 'categorias')">Siguiente</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script>

const BASE_URL_LOCAL = "<?php echo BASE_URL; ?>";

function desactivarCategoria(id) {
    if (!confirm('ADVERTENCIA: ¿Estás seguro de que deseas desactivar esta categoría?')) {
        return; 
    }
    let datos = JSON.stringify({ "id" : id });
    
    fetch(BASE_URL_LOCAL + 'controllers/categoria_eliminar.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body : datos
    })
    .then(response => response.json())
    .then(function(data) { 
        if (data.estado === 'ok') {
            // Recargamos el contenido de la vista actual (categorias)
            loadPage('panel_admin.php?view=categorias', 'categorias'); 
        } else {
            alert("Error de BD: " + data.mensaje);
        }
    })
    .catch(error => alert("Fallo de comunicación AJAX: " + error.message));
}


function restaurarCategoria(id) {
    if (!confirm('¿Estás seguro de que deseas reactivar esta categoría?')) {
        return;
    }
    let datos = JSON.stringify({ "id" : id });
    
    fetch(BASE_URL_LOCAL + 'controllers/categoria_restaurar.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body : datos
    })
    .then(response => response.json())
    .then(function(data) { 
        if (data.estado === 'ok') {
            // Recargamos el contenido de la vista actual (categorias)
            loadPage('panel_admin.php?view=categorias', 'categorias'); 
        } else {
            alert("Error de BD: " + data.mensaje);
        }
    })
    .catch(error => alert("Fallo de comunicación AJAX: " + error.message));
}


document.getElementById('categoryFilterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const queryString = new URLSearchParams(formData).toString();
    
    // Al filtrar, cargamos el controlador principal con la vista y los filtros
    loadPage('panel_admin.php?' + queryString, 'categorias');
});
</script>
<?php 
// Si accedimos directamente a la página, cerramos el HTML
if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) { 
    echo '</div> ';
    echo '</div> ';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '</body></html>';
}
?>