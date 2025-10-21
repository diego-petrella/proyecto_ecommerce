<?php
session_start();

require '../controllers/verificacion_usuario.php';
rolRequerido(1);

require "../models/funciones.php";


$id_pedido = (int)($_GET['id'] ?? 0);


if ($id_pedido === 0) {
    header('Location: gestion_pedidos.php');
    exit;
}

$pedido = obtenerDetallePedido($id_pedido);

if (!$pedido) {
    header('Location: gestion_pedidos.php?error=not_found');
    exit;
}

$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle Pedido #<?php echo $id_pedido; ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <header class="bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">
                <i class="bi bi-receipt-cutoff me-2"></i> Detalle del Pedido #<?php echo $id_pedido; ?>
                <a href="gestion_pedidos.php" class="btn btn-outline-light btn-sm ms-3" title="Volver al Listado">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </h1>
            <a href="../controllers/logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </header>

    <div class="container my-5">
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
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Subtotal Productos:</span>
                                <strong>$<?php echo number_format($pedido['subtotal'], 2, ',', '.'); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Costo de Envío:</span>
                                <span class="<?php echo $pedido['costo_envio'] == 0 ? 'text-success fw-bold' : ''; ?>">
                                    $<?php echo number_format($pedido['costo_envio'], 2, ',', '.'); ?>
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
                                        <img src="../assets/img/<?php echo htmlspecialchars($detalle['imagen']); ?>" 
                                             alt="Producto" style="max-width: 50px; height: auto; border-radius: 4px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($detalle['nombre_articulo']); ?></td>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>