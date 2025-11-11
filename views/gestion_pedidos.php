<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/verificacion_usuario.php';
rolRequerido(1); 
if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }

require_once __DIR__ . "/../models/funciones.php";
$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';


$pedidos = obtenerTodosLosPedidos($fecha_inicio, $fecha_fin); 

$total_ventas = 0;
foreach ($pedidos as $pedido) {
    $total_ventas += $pedido['total'];
}
$total_ventas_formateado = number_format($total_ventas, 2, ',', '.');

// Título dinámico para el filtro
$titulo_filtro = "Historial completo";
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $titulo_filtro = "Mostrando ventas desde " . date('d/m/Y', strtotime($fecha_inicio)) . " hasta " . date('d/m/Y', strtotime($fecha_fin));
} elseif (!empty($fecha_inicio)) {
    $titulo_filtro = "Mostrando ventas desde " . date('d/m/Y', strtotime($fecha_inicio));
} elseif (!empty($fecha_fin)) {
    $titulo_filtro = "Mostrando ventas hasta " . date('d/m/Y', strtotime($fecha_fin));
}




if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) {
    require '../includes/admin_nav.php'; 
}

?>
        
        <div class="container-fluid py-3">
            
            <h1 class="mb-4">Gestión de Pedidos</h1>
            
          
            <div class="card shadow-sm mb-4">
                <div class="card-body bg-light">
                    <h5 class="card-title"><i class="bi bi-funnel-fill me-2"></i>Filtrar por Fecha</h5>
                    
                <form action="" method="GET" class="row g-3 align-items-end" id="dateFilterForm">
                <input type="hidden" name="view" value="pedidos">

                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label">Fecha Desde:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label">Fecha Hasta:</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                </div>
                <div class="col-md-4 d-flex">
                    <button type="submit" class="btn btn-primary me-2 w-100">
                        <i class="bi bi-search me-2"></i>Filtrar
                    </button>
                    <a href="javascript:void(0);" onclick="loadPage('panel_admin.php?view=pedidos', 'pedidos')" class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                <div>
                    <h2 class="mb-0">Listado de Ventas (<?php echo count($pedidos); ?>)</h2>
                    <p class="text-muted mb-0 fst-italic small"><?php echo htmlspecialchars($titulo_filtro); ?></p>
                </div>
                
                <div class="text-end">
                    <div class="bg-success text-white p-3 rounded shadow-sm">
                        <span class="text-white-50 d-block" style="font-size: 0.9rem;">Total de Ventas (Filtrado)</span>
                        <span class="h3 fw-bold mb-0">$<?php echo $total_ventas_formateado; ?></span>
                    </div>
                </div>
            </div>
         
            
            <div class="table-responsive shadow-lg">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 15%;">Fecha</th>
                            <th style="width: 25%;">Cliente / Contacto</th>
                            <th style="width: 30%;">Dirección de Envío</th>
                            <th style="width: 10%;" class="text-end">Total</th>
                            <th style="width: 15%;" class="text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($pedidos)): 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center p-4 text-muted">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    No se encontraron ventas para los filtros seleccionados.
                                </td>
                            </tr>
                        <?php 
                        else:
                            foreach($pedidos as $pedido) { 
                                $total_formateado = number_format($pedido["total"], 2, ',', '.');
                        ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($pedido["pedido_id"]) ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime(htmlspecialchars($pedido["fecha_pedido"]))) ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($pedido["nombre_cliente"]) ?></strong><br>
                                    <span class="small text-muted"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($pedido["email_cliente"]) ?></span><br>
                                    <span class="small text-muted"><i class="bi bi-phone"></i> <?php echo htmlspecialchars($pedido["telefono_contacto"]) ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($pedido["direccion_envio"]) ?></td>
                                <td class="text-end fw-bold text-success">$<?php echo $total_formateado ?></td>
                                
                                <td class="text-center">
                                    <a href="javascript:void(0);" onclick="loadPage('detalle_pedido.php?id=<?php echo htmlspecialchars($pedido['pedido_id']); ?>', 'detalle_pedido_<?php echo htmlspecialchars($pedido['pedido_id']); ?>')" class="btn btn-sm btn-info text-white" title="Ver detalle de productos">
                                        <i class="bi bi-list-columns-reverse"></i> Ver Productos
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            } 
                        endif; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <script>
            // Listener para el formulario de filtro de fechas (usando el ID 'dateFilterForm')
            document.getElementById('dateFilterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const queryString = new URLSearchParams(formData).toString();
                
                
                loadPage('gestion_pedidos.php?' + queryString, 'pedidos');
            });
        </script>


        <?php 
        
        if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) { 
            echo '</div> <!-- Cierra el div class="flex-grow-1 p-4" -->';
            echo '</div> <!-- Cierra el div class="d-flex" -->';
            echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
            echo '</body></html>';
        }
        ?>