<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../controllers/verificacion_usuario.php';
rolRequerido(1);

 if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }

require "../models/funciones.php";
$id_pedido = (int)($_GET['id'] ?? 0);


$pedido = obtenerDetallePedido($id_pedido);


if (!isset($es_contenido_ajax) && ($id_pedido === 0 || !$pedido)) {
    header('Location: gestion_pedidos.php' . ($id_pedido === 0 ? '' : '?error=not_found'));
    exit;
}


$es_contenido_ajax = true; 

?>
<div class="container-fluid py-3"> 
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 m-0">
            <i class="bi bi-receipt-cutoff me-2"></i> Detalle del Pedido #<?php echo htmlspecialchars($id_pedido); ?>
        </h1>
        
        <a href="javascript:void(0);" 
           onclick="loadPage('gestion_pedidos.php', 'pedidos')" 
           class="btn btn-secondary" title="Volver al Listado">
             <i class="bi bi-arrow-left me-2"></i> Volver a Pedidos
        </a>
    </div>

    <?php if (!$pedido): ?>
        <div class="alert alert-warning" role="alert">
            No se encontró el pedido o el ID es inválido.
        </div>
    <?php else: ?>
        <div class="row g-4">
            
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i> Datos del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nombre_cliente']); ?></p>
                        <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                        <hr>
                        <p class="mb-1"><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion_envio']); ?></p>
                        <p class="mb-1"><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['telefono_contacto']); ?></p>
                        <p class="mb-0"><strong>ID Cliente:</strong> <?php echo htmlspecialchars($pedido['id_usuario']); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i> Resumen de Pago</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            
                            <li class="list-group-item d-flex justify-content-between fw-bold text-dark">
                                <span>Método de Pago:</span>
                                <strong><?php echo htmlspecialchars($pedido['metodo_pago'] ?? 'N/A'); ?></strong>
                            </li>
                            <?php if (!empty($pedido['mp_preference_id'])): ?>
                            <li class="list-group-item d-flex justify-content-between small text-muted">
                                <span>Ref. Mercado Pago:</span>
                                <strong><?php echo htmlspecialchars($pedido['mp_preference_id']); ?></strong>
                            </li>
                            <?php endif; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Subtotal Productos:</span>
                                <strong>$<?php echo number_format($pedido['subtotal'], 2, ',', '.'); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Costo de Envío:</span>
                                <span class="<?php echo ($pedido['costo_envio'] ?? 0) == 0 ? 'text-success fw-bold' : ''; ?>">
                                    $<?php echo number_format($pedido['costo_envio'] ?? 0, 2, ',', '.'); ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between fw-bold h5 text-primary">
                                <span>Total Final:</span>
                                <strong>$<?php echo number_format($pedido['total'], 2, ',', '.'); ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <h4 class="mt-3 mb-3"><i class="bi bi-box-seam me-2"></i> Artículos Comprados</h4>
                <div class="table-responsive shadow-sm">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">Imagen</th>
                                <th style="width: 40%;">Artículo</th>
                                <th style="width: 10%;" class="text-center">Cantidad</th>
                                <th style="width: 20%;" class="text-end">Precio Unitario</th>
                                <th style="width: 20%;" class="text-end">Total por Ítem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (empty($pedido['detalles'])): 
                            ?>
                                <tr>
                                    <td colspan="5" class="text-center p-4 text-muted">No se encontraron artículos para este pedido.</td>
                                </tr>
                            <?php 
                            else:
                                foreach($pedido['detalles'] as $detalle) { 
                                    $total_item = $detalle['precio_unitario'] * $detalle['cantidad'];
                            ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo BASE_URL; ?>assets/img/<?php echo htmlspecialchars($detalle['imagen']); ?>" 
                                             alt="Producto" style="max-width: 50px; height: auto; border-radius: 4px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($detalle['nombre'] ?? 'N/A'); ?></td> 
                                    <td class="text-center"><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                                    <td class="text-end">$<?php echo number_format($detalle['precio_unitario'], 2, ',', '.'); ?></td>
                                    <td class="text-end fw-bold">$<?php echo number_format($total_item, 2, ',', '.'); ?></td>
                                </tr>
                            <?php 
                                } 
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>