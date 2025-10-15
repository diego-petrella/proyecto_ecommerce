<?php
// Define la constante de inclusión para seguridad
if (!defined('APP_INCLUDED')) {
    define('APP_INCLUDED', true);
}

// Aseguramos que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require "../models/funciones.php"; 

// --- Redirección de Seguridad ---
// Si el usuario no está logueado o el carrito está vacío, no puede hacer checkout
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    // Aquí puedes redirigir al login o al carrito, dependiendo de la lógica de tu negocio.
    header('Location: login.php'); 
    exit;
}

$usuario_logueado = true; // Ya verificamos arriba
$cliente_email = $_SESSION['usuario_email'] ?? 'Usuario';

// --- Lógica del Carrito y Cálculos (Similar a carrito.php) ---
$carrito = $_SESSION['carrito'] ?? [];
$articulos_en_carrito = [];
$subtotal = 0;
$total_items_carrito = 0;

if (!empty($carrito)) {
    // Simulación de datos (reemplazar con la llamada a la BD real)
    $articulos_simulados = [
        1 => ['nombre' => 'iPhone SE', 'precio' => 430.00, 'imagen' => 'iphone_se.png', 'marca_nombre' => 'Apple'],
        2 => ['nombre' => 'Samsung Galaxy A54', 'precio' => 4500.00, 'imagen' => 'galaxy_a54.png', 'marca_nombre' => 'Samsung'],
    ];

    foreach ($carrito as $id_articulo => $cantidad) {
        if (isset($articulos_simulados[$id_articulo])) {
            $articulo_data = $articulos_simulados[$id_articulo];
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

// Lógica de Envío
$costo_envio = ($subtotal >= 5000) ? 0.00 : 80.00;
$total_final = $subtotal + $costo_envio;
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
                    <i class="bi bi-person-circle me-1"></i> Checkout: **<?php echo htmlspecialchars($cliente_email); ?>**
                </span>
                <a class="btn btn-outline-secondary btn-sm" href="carrito.php">
                    <i class="bi bi-cart4 me-1"></i> Volver al Carrito
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4 text-center">Finalizar Pedido</h1>
        <p class="lead text-center text-muted">Estás a un paso de confirmar tu compra. Revisa los detalles y completa tu información de envío.</p>

        <div class="row g-5">
            <!-- Columna de Formulario de Envío y Pago -->
            <div class="col-md-7 col-lg-8">
                <h4 class="mb-3 text-primary"><i class="bi bi-truck me-2"></i> Información de Envío</h4>
                
                <!-- El formulario envía los datos al controlador para procesar la compra final -->
                <form action="../controllers/procesar_compra.php" method="POST" class="needs-validation" novalidate>
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" required>
                            <div class="invalid-feedback">
                                Se requiere un nombre válido.
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="" required>
                            <div class="invalid-feedback">
                                Se requiere un apellido válido.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Calle, Número y Referencia" required>
                            <div class="invalid-feedback">
                                Se requiere la dirección de envío.
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="pais" class="form-label">País</label>
                            <select class="form-select" id="pais" name="pais" required>
                                <option value="">Seleccionar...</option>
                                <option value="AR">Argentina</option>
                                <option value="MX">México</option>
                                <option value="CO">Colombia</option>
                                <!-- Agregar más opciones -->
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecciona un país válido.
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                            <div class="invalid-feedback">
                                Se requiere la ciudad.
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="cp" class="form-label">C.P.</label>
                            <input type="text" class="form-control" id="cp" name="cp" placeholder="" required>
                            <div class="invalid-feedback">
                                Se requiere el código postal.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3 text-primary"><i class="bi bi-credit-card-2-back me-2"></i> Pago</h4>

                    <div class="my-3">
                        <div class="form-check">
                            <input id="credito" name="metodo_pago" type="radio" class="form-check-input" checked required value="Credito">
                            <label class="form-check-label" for="credito">Tarjeta de Crédito</label>
                        </div>
                        <div class="form-check">
                            <input id="debito" name="metodo_pago" type="radio" class="form-check-input" required value="Debito">
                            <label class="form-check-label" for="debito">Tarjeta de Débito</label>
                        </div>
                        <div class="form-check">
                            <input id="transferencia" name="metodo_pago" type="radio" class="form-check-input" required value="Transferencia">
                            <label class="form-check-label" for="transferencia">Transferencia Bancaria</label>
                        </div>
                    </div>
                    
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="cc-nombre" class="form-label">Nombre en la tarjeta</label>
                            <input type="text" class="form-control" id="cc-nombre" placeholder="" required>
                            <small class="text-muted">Nombre completo como aparece en la tarjeta</small>
                            <div class="invalid-feedback">
                                Se requiere el nombre.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="cc-numero" class="form-label">Número de tarjeta</label>
                            <input type="text" class="form-control" id="cc-numero" placeholder="" required>
                            <div class="invalid-feedback">
                                Se requiere el número de tarjeta de crédito.
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="cc-expiracion" class="form-label">Vencimiento</label>
                            <input type="text" class="form-control" id="cc-expiracion" placeholder="MM/AA" required>
                            <div class="invalid-feedback">
                                Se requiere la fecha de expiración.
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="cc-cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cc-cvv" placeholder="" required>
                            <div class="invalid-feedback">
                                Se requiere el código de seguridad.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <button class="w-100 btn btn-success btn-lg" type="submit">
                        <i class="bi bi-check-circle-fill me-2"></i> Pagar $<?php echo number_format($total_final, 2, ',', '.'); ?> y Confirmar Pedido
                    </button>
                </form>
            </div>

            <!-- Columna de Resumen de Compra -->
            <div class="col-md-5 col-lg-4 order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3 text-primary">
                    <span>Tu Carrito</span>
                    <span class="badge bg-primary rounded-pill"><?php echo $total_items_carrito; ?></span>
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
                    
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Subtotal</span>
                        <strong>$<?php echo number_format($subtotal, 2, ',', '.'); ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Envío</span>
                        <span class="<?php echo $costo_envio == 0 ? 'text-success fw-bold' : ''; ?>">
                             <?php echo $costo_envio == 0 ? 'Gratis' : '$' . number_format($costo_envio, 2, ',', '.'); ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between fw-bold h5 text-primary">
                        <span>Total (ARS)</span>
                        <strong>$<?php echo number_format($total_final, 2, ',', '.'); ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para habilitar la validación de Bootstrap en el formulario
        (function () {
            'use strict';
            var form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
</body>
</html>
