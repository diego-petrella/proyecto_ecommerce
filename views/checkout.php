<?php
// Define la constante de inclusión para seguridad
if (!defined('APP_INCLUDED')) {
    define('APP_INCLUDED', true);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../models/funciones.php"; 


if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
   $url_destino = urlencode($_SERVER['REQUEST_URI']);

    header('Location: login.php?url_destino=' . $url_destino);
    exit; 
   
}

$id_usuario = $_SESSION['usuario_id'];
$cliente_email = $_SESSION['usuario_email'] ?? 'Usuario';


$datos_usuario = buscarDatosUsuario($id_usuario); 

if (!$datos_usuario) {
    header('Location: login.php');
    exit;
}


$nombre = htmlspecialchars($datos_usuario['nombre'] ?? '');
$apellido = htmlspecialchars($datos_usuario['apellido'] ?? '');
$direccion = htmlspecialchars($datos_usuario['direccion'] ?? '');
$telefono = htmlspecialchars($datos_usuario['telefono'] ?? '');


$carrito = $_SESSION['carrito'] ?? [];
$articulos_en_carrito = [];
$subtotal = 0;
$total_items_carrito = 0;

$articulos_bd = obtenerArticulosCarrito($carrito); 

if (!empty($carrito) && !empty($articulos_bd)) {
    foreach ($carrito as $id_articulo => $cantidad) {
        if (isset($articulos_bd[$id_articulo])) { 
            $articulo_data = $articulos_bd[$id_articulo];
            $precio_total_articulo = $articulo_data['precio'] * $cantidad;
            $subtotal += $precio_total_articulo;
            $total_items_carrito += $cantidad;

            $articulos_en_carrito[] = [
                'id' => $id_articulo,
                'nombre' => $articulo_data['nombre'],
                'precio' => $articulo_data['precio'],
                'cantidad' => $cantidad,
                'total' => $precio_total_articulo,
                'imagen' => $articulo_data['imagen'],
            ];
        }
    }
}


$total_final = $subtotal;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tienda | Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand text-primary fw-bold" href="../index.php">
                <i class="bi bi-shop me-2"></i> Mi Tienda E-commerce
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3 d-none d-sm-inline">
                    <i class="bi bi-person-circle me-1"></i> Finalizando Compra: **<?php echo htmlspecialchars($cliente_email); ?>**
                </span>
                <a class="btn btn-outline-secondary btn-sm" href="carrito.php">
                    <i class="bi bi-cart4 me-1"></i> Volver al Carrito
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4 text-center">Confirmar Pedido</h1>
        
        <div class="row g-5">
            <div class="col-md-7 col-lg-8">
                <form action="../controllers/procesar_compra.php" method="POST" class="needs-validation" novalidate>

                    <h4 class="mb-3 text-primary"><i class="bi bi-truck me-2"></i> Información de Envío</h4>
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                value="<?php echo $nombre; ?>" required>
                                <div class="invalid-feedback">Se requiere el nombre.</div>
                        </div>

                        <div class="col-sm-6">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" 
                                value="<?php echo $apellido; ?>" required>
                                <div class="invalid-feedback">Se requiere el apellido.</div>
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección Completa</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                value="<?php echo $direccion; ?>" required>
                                <div class="invalid-feedback">Se requiere una dirección.</div>
                        </div>

                        <div class="col-sm-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" 
                                value="<?php echo $telefono; ?>" required>
                                <div class="invalid-feedback">Se requiere un teléfono.</div>
                        </div>
                        
                        <div class="col-sm-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="<?php echo htmlspecialchars($cliente_email); ?>" readonly>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h4 class="mb-3 text-primary"><i class="bi bi-credit-card me-2"></i> Método de Pago</h4>
                    
                    <div class="my-3">
                        <div class="form-check">
                            <input id="pagoTarjeta" name="metodo_pago" type="radio" class="form-check-input" value="Tarjeta" required>
                            <label class="form-check-label" for="pagoTarjeta">Tarjeta de Crédito / Débito</label>
                        </div>
                        <div class="form-check">
                            <input id="pagoTransferencia" name="metodo_pago" type="radio" class="form-check-input" value="Transferencia" required>
                            <label class="form-check-label" for="pagoTransferencia">Transferencia Bancaria</label>
                        </div>
                        <div class="form-check">
                            <input id="pagoEfectivo" name="metodo_pago" type="radio" class="form-check-input" value="Efectivo" required>
                            <label class="form-check-label" for="pagoEfectivo">Efectivo (Pago al Recibir)</label>
                        </div>
                        <div class="invalid-feedback">Se debe seleccionar un método de pago.</div>
                    </div>

                    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                    <input type="hidden" name="total_final_visible" value="<?php echo $total_final; ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                    
                    <hr class="my-4">
                    
                    <button class="w-100 btn btn-success btn-lg" type="submit">
                        <i class="bi bi-check-circle-fill me-2"></i> Confirmar Pedido y Finalizar Compra ($<?php echo number_format($total_final, 2, ',', '.'); ?>)
                    </button>
                </form>
            </div>

            <div class="col-md-5 col-lg-4 order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3 text-primary">
                    <span>Resumen</span>
                    <span class="badge bg-primary rounded-pill"><?php echo $total_items_carrito; ?> ítems</span>
                </h4>
                <ul class="list-group mb-3">
                    <?php foreach ($articulos_en_carrito as $item): ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?php echo htmlspecialchars($item['nombre']); ?> (x<?php echo $item['cantidad']; ?>)</h6>
                            <small class="text-muted">$<?php echo number_format($item['precio'], 2, ',', '.'); ?> c/u</small>
                        </div>
                        <span class="text-muted">$<?php echo number_format($item['total'], 2, ',', '.'); ?></span>
                    </li>
                    <?php endforeach; ?>
                    
                    <li class="list-group-item d-flex justify-content-between fw-bold h5 text-primary">
                        <span>Total Final</span>
                        <strong>$<?php echo number_format($total_final, 2, ',', '.'); ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            'use strict';
            var form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function (event) {
                // Validación para campos de radio. Buscamos el grupo 'metodo_pago'.
                var radios = form.elements['metodo_pago'];
                var pagoSeleccionado = false;
                for (var i = 0; i < radios.length; i++) {
                    if (radios[i].checked) {
                        pagoSeleccionado = true;
                        break;
                    }
                }

                if (!form.checkValidity() || !pagoSeleccionado) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    var pagoField = document.querySelector('.my-3');
                    if (!pagoSeleccionado) {
                        pagoField.classList.add('is-invalid-custom');
                    } else {
                        pagoField.classList.remove('is-invalid-custom');
                    }
                } else {
                    var pagoField = document.querySelector('.my-3');
                    pagoField.classList.remove('is-invalid-custom');
                }
                form.classList.add('was-validated');
            }, false);
            
            // Script CSS simple para mostrar feedback de error en el grupo de radios
            var style = document.createElement('style');
            style.innerHTML = '.is-invalid-custom .invalid-feedback { display: block; }';
            document.head.appendChild(style);
            
        })();
    </script>
</body>
</html>