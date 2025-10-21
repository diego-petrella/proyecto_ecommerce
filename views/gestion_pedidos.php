<?php
session_start();

require '../controllers/verificacion_usuario.php';
rolRequerido(1); 

require "../models/funciones.php";


$pedidos = obtenerTodosLosPedidos(); 

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
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Listado de Ventas (<?php echo count($pedidos); ?>)</h2>
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
                            <td colspan="6" class="text-center p-4 text-muted">No se ha registrado ninguna venta aún.</td>
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