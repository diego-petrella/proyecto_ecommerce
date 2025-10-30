<?php
session_start();

require '../controllers/verificacion_usuario.php';
rolRequerido(1); 

require "../models/funciones.php";

// --- NUEVO: OBTENER FILTROS DE FECHA ---
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
// --- FIN NUEVO ---

// --- MODIFICADO: PASAR FILTROS A LA FUNCIÓN ---
$pedidos = obtenerTodosLosPedidos($fecha_inicio, $fecha_fin); 
// --- FIN MODIFICADO ---

// --- NUEVO: CALCULAR TOTAL DE VENTAS Y TÍTULO ---
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
// --- FIN NUEVO ---

$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Pedidos | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <header class="bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">
                <i class="bi bi-receipt-cutoff me-2"></i> Gestión de Pedidos
                <a href="../index.php" class="btn btn-outline-info btn-sm ms-3" title="Ir a la página principal">
                    <i class="bi bi-house-door-fill"></i> Home
                </a>
                <a href="panel_admin.php" class="btn btn-outline-light btn-sm ms-2" title="Volver a Artículos">
                    <i class="bi bi-box-seam"></i> Volver a Artículos
                </a>
            </h1>
            <div class="d-flex align-items-center">
                <span class="text-white-50 me-3 small">
                    <i class="bi bi-person-fill"></i> Sesión: <?php echo htmlspecialchars($admin_email); ?>
                </span>
                <a href="../controllers/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <div class="container my-5">
        
        <!-- --- NUEVO: FILTRO DE FECHAS --- -->
        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light">
                <h5 class="card-title"><i class="bi bi-funnel-fill me-2"></i>Filtrar por Fecha</h5>
                <form action="gestion_pedidos.php" method="GET" class="row g-3 align-items-end">
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
                        <a href="gestion_pedidos.php" class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <!-- --- FIN NUEVO --- -->

        <!-- --- MODIFICADO: TÍTULO Y TOTAL DE VENTAS --- -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <div>
                <h2 class="mb-0">Listado de Ventas (<?php echo count($pedidos); ?>)</h2>
                <p class="text-muted mb-0 fst-italic"><?php echo htmlspecialchars($titulo_filtro); ?></p>
            </div>
            
            <div class="text-end">
                <div class_ ="bg-success text-white p-3 rounded shadow-sm">
                    <span class="text-white-50 d-block" style="font-size: 0.9rem;">Total de Ventas (Filtrado)</span>
                    <span class="h3 fw-bold mb-0">$<?php echo $total_ventas_formateado; ?></span>
                </div>
            </div>
        </div>
        <!-- --- FIN MODIFICADO --- -->
        
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
                                <a href="detalle_pedido.php?id=<?php echo htmlspecialchars($pedido['pedido_id']); ?>" class="btn btn-sm btn-info text-white" title="Ver detalle de productos">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
