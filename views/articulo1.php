<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../controllers/verificacion_usuario.php';
rolRequerido(1);

if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }

//PARA EVITAR QUE INGRESEN DIRECTAMENTE A ESTA VISTA
$ruta_script_actual = $_SERVER['SCRIPT_NAME'];

if (strpos($ruta_script_actual, 'panel_admin.php') === false) {    
    // Evitamos cualquier salida antes de la redirección
    if (ob_get_length()) {
        ob_end_clean();
    }
    header('Location: ' . BASE_URL . 'views/panel_admin.php');
    exit;
}
require_once "../models/funciones.php";

//PAGINACIÓN
$items_por_pagina = 8;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($pagina_actual - 1) * $items_por_pagina;

//FILTRADO
$filtro_nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : "";
$filtro_marca = isset($_GET["id_categoria"]) ? $_GET["id_categoria"] : "";
$filtro_estado = isset($_GET["estado"]) ? $_GET["estado"] : "1"; 

$total_articulos = contarTotalArticulos($filtro_nombre, $filtro_marca, $filtro_estado); 
$total_paginas = ceil($total_articulos / $items_por_pagina);


$articulos = obtenerArticulos($filtro_nombre, $filtro_marca, $filtro_estado, $items_por_pagina, $offset); 
$categorias = obtenerTodasCategoriasActivas(); 

//Para mantener los filtros en los enlaces de paginación
$filtros_query = http_build_query([
    'nombre' => $filtro_nombre,
    'id_categoria' => $filtro_marca,
    'estado' => $filtro_estado 
]);

$es_contenido_ajax = true; 

?>

<div class="container-fluid py-3"> 
    
    <h1 class="mb-4">Gestión de Artículos</h1>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Listado de Artículos (<?php echo $total_articulos; ?>)</h2>
        
        
        <a href="javascript:void(0);" 
           onclick="loadPage('abm_articulo.php?action=nuevo&token=<?php echo md5(session_id()); ?>', 'abm_articulo')" 
           class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i> Agregar Nuevo
        </a>
    </div>

    <!-- FILTROS -->
    <div class="card p-3 mb-4 shadow-sm">
        
        <form action="" method="GET" class="row g-3 align-items-end" id="articleFilterForm">
            <input type="hidden" name="view" value="articulos">
            <div class="col-md-3">
                <label for="nombre" class="form-label visually-hidden">Buscar Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Buscar por Nombre" 
                    value="<?php echo htmlspecialchars($filtro_nombre); ?>">
            </div>
            
            <div class="col-md-3">
                <label for="id_categoria" class="form-label visually-hidden">Categoría</label>
                <select class="form-select" id="id_categoria" name="id_categoria">
                    <option value="">Todas las Categorías</option>
                    <?php 
                    foreach ($categorias as $categoria) { 
                        $selected = ($filtro_marca == $categoria['id']) ? 'selected' : '';
                        echo "<option value='{$categoria['id']}' {$selected}>" . htmlspecialchars($categoria['nombre']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="estado" class="form-label visually-hidden">Estado</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="1" <?php echo ($filtro_estado == '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo ($filtro_estado == '0') ? 'selected' : ''; ?>>Inactivos</option>
                    <option value="" <?php echo ($filtro_estado == '') ? 'selected' : ''; ?>>Todos</option>
                </select>
            </div>

            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="javascript:void(0);" onclick="loadPage('panel_admin.php?view=articulos', 'articulos')" class="btn btn-secondary" title="Limpiar Filtros">
                    <i class="bi bi-eraser"></i>
                </a>
            </div>
        </form>
    </div>
    

    <div class="table-responsive shadow-sm">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 10%;">Imagen</th>
                    <th style="width: 30%;">Nombre</th>
                    <th style="width: 10%;">Stock</th>
                    <th style="width: 10%;">Precio</th>
                    <th style="width: 15%;">Categoria</th>
                    <th style="width: 10%;" class="text-center">Estado</th> 
                    <th style="width: 10%;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (empty($articulos)): 
                ?>
                    <tr>
                        <td colspan="8" class="text-center p-4 text-muted">No se encontraron artículos que coincidan con los filtros.</td>
                    </tr>
                <?php 
                else:
                    foreach($articulos as $articulo) { 
                        $precio_formateado = number_format($articulo["precio"], 2, ',', '.');
                        $imagen_src = !empty($articulo["imagen"]) ? BASE_URL . "assets/img/" . htmlspecialchars($articulo["imagen"]) : "https://placehold.co/80x80/eee/ccc?text=N/A";
                        
                        $es_inactivo = ($articulo['activo'] == 0);
                        $clase_fila = $es_inactivo ? 'table-light text-muted' : '';
                        $badge_estado = $es_inactivo ? 
                            '<span class="badge bg-danger">Inactivo</span>' : 
                            '<span class="badge bg-success">Activo</span>';
                ?>
                    <tr id="fila_<?php echo htmlspecialchars($articulo['id']); ?>" class="<?php echo $clase_fila; ?>">
                        <td><?php echo htmlspecialchars($articulo["id"]) ?></td>
                        <td><img src="<?php echo $imagen_src; ?>" alt="Producto" style="width: 80px; height: 80px; object-fit: contain;" onerror="this.src='https://placehold.co/80x80/eee/ccc?text=Error';"></td>
                        <td><?php echo htmlspecialchars($articulo["nombre"]) ?></td>
                        <td><?php echo htmlspecialchars($articulo["stock"] ?? 'N/A') ?></td>
                        <td>$<?php echo $precio_formateado ?></td>
                        <td><?php echo htmlspecialchars($articulo["nombre_categoria"] ?? 'N/A') ?></td>
                        
                        <td class="text-center"><?php echo $badge_estado; ?></td>
                        
                        <td class="text-center">
                            <a href="javascript:void(0);" 
                               onclick="loadPage('abm_articulo.php?id=<?php echo htmlspecialchars($articulo["id"]) ?>', 'abm_articulo')" 
                               class="btn btn-sm btn-warning" title="Modificar Artículo">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            
                            <?php if ($es_inactivo): ?>
                                <a class="btn btn-sm btn-info" 
                                   onclick="restaurarArticulo(<?php echo htmlspecialchars($articulo['id']); ?>)"
                                   title="Restaurar Artículo">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            <?php else: ?>
                                <a class="btn btn-sm btn-danger" 
                                   onclick="eliminarArticulo(<?php echo htmlspecialchars($articulo['id']); ?>)"
                                   title="Eliminar Artículo">
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

    <!-- Paginación -->
    <?php if ($total_paginas > 1): ?>
    <nav aria-label="Paginación de artículos" class="mt-4">
        <ul class="pagination justify-content-center">
            
            <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" 
                   href="javascript:void(0);" 
                   onclick="loadPage('panel_admin.php?view=articulos&p=<?php echo $pagina_actual - 1; ?>&<?php echo $filtros_query; ?>', 'articulos')">Anterior</a>
            </li>
            
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                    <a class="page-link" 
                       href="javascript:void(0);" 
                       onclick="loadPage('panel_admin.php?view=articulos&p=<?php echo $i; ?>&<?php echo $filtros_query; ?>', 'articulos')"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                <a class="page-link" 
                   href="javascript:void(0);" 
                   onclick="loadPage('panel_admin.php?view=articulos&p=<?php echo $pagina_actual + 1; ?>&<?php echo $filtros_query; ?>', 'articulos')">Siguiente</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>


<script>

const base= "/programacion2/articulos/"

function eliminarArticulo(id) { 
    if (!confirm('¿Estás seguro de que deseas desactivar este artículo?')) {
        return; 
    }
    let datos = JSON.stringify({ "id" : id });
    
    fetch(base + "controllers/articulo_eliminar.php", { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body : datos
    })
    .then(response => response.json())
    .then(function(data) { 
        if (data.estado === 'ok') {
            loadPage('articulo1.php', 'articulos'); 
        } else {
            alert("Error de BD: " + data.mensaje);
        }
    })
    .catch(error => alert("Fallo de comunicación AJAX: " + error.message));
}

function restaurarArticulo(id) {
    if (!confirm('¿Estás seguro de que deseas reactivar este artículo?')) {
        return;
    }
    let datos = JSON.stringify({ "id" : id });
    
    fetch(base + "controllers/articulo_restaurar.php", { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body : datos
    })
    .then(response => response.json())
    .then(function(data) { 
        if (data.estado === 'ok') {
            loadPage('articulo1.php', 'articulos'); 
        } else {
            alert("Error de BD: " + data.mensaje);
        }
    })
    .catch(error => alert("Fallo de comunicación AJAX: " + error.message));
}
</script>


<?php 
// Si accedimos directamente a la página, cerramos el HTML
if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) { 
    echo '</div> <!-- Cierra el div class="flex-grow-1 p-4" -->';
    echo '</div> <!-- Cierra el div class="d-flex" -->';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '</body></html>';
}
?>