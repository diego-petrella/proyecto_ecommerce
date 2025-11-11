<?php
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


require '../models/funciones.php'; 

$usuario_logueado = isset($_SESSION['usuario_id']);
$cliente_email = $_SESSION['usuario_email'] ?? null;
$total_items_carrito = contarProductosCarrito();

// Lógica principal del Carrito
$carrito = $_SESSION['carrito'] ?? [];
$productos_en_carrito = [];
$total_general = 0;


if (!empty($carrito)) {
    //Obtener la lista de IDs del carrito
    $product_ids = array_keys($carrito);
     

    //Obtener todos los datos de los productos
    $productos_data = obtenerProductosPorListaDeIds($product_ids);

    //Unir los datos de la DB con las cantidades de la sesión
    foreach ($carrito as $id => $cantidad) {
        if (isset($productos_data[$id])) {
            $producto = $productos_data[$id];
            $subtotal = $producto['precio'] * $cantidad;
            $total_general += $subtotal;

            $productos_en_carrito[] = [
                'id' => $id,
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'marca' => $producto['nombre_categoria'],
                'imagen' => $producto['imagen'],
                'cantidad' => $cantidad,
                'stock' => $producto['stock'],
                'subtotal' => $subtotal,
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito de Compras</title>
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
                <?php if ($usuario_logueado) { ?>
                    <span class="navbar-text me-3 d-none d-sm-inline">
                        <i class="bi bi-person-circle me-1"></i> Bienvenido, **<?php echo htmlspecialchars($cliente_email); ?>**
                    </span>
                    <a class="btn btn-outline-danger btn-sm me-3" href="../controllers/logout.php" title="Cerrar Sesión">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </a>
                <?php } else { ?>
                    <a class="btn btn-primary me-3" href="login.php?url_destino=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" title="Iniciar Sesión o Registrarse">
                        <i class="bi bi-person"></i> Iniciar Sesión
                    </a>
                <?php } ?>
                <a class="btn btn-success position-relative" href="carrito.php">
                    <i class="bi bi-cart4"></i> Carrito
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $total_items_carrito; ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container my-5">
        <h1 class="mb-4">Tu Carrito de Compras</h1>

        <?php if (empty($productos_en_carrito)) { ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-cart-x-fill display-4 d-block mb-3"></i>
                <h4 class="alert-heading">¡Tu carrito está vacío!</h4>
                <p>Parece que no has agregado ningún producto todavía. <a href="../index.php">Ir a la tienda</a>.</p>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            
                            <form action="../controllers/carrito.php" method="POST">
                                <input type="hidden" name="action" value="update_all"> 
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($productos_en_carrito as $item) { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <img src="../assets/img/<?php echo htmlspecialchars($item['imagen'])?>" 
                                                    alt="<?php echo htmlspecialchars($item['nombre']); ?>" 
                                                    class="rounded me-3" 
                                                    style="width: 60px; height: 60px; object-fit: cover;"
                                                    onerror="this.onerror=null; this.src='../assets/img/default.jpg';"
                                                >
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['nombre']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($item['marca']); ?></small>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex me-4" style="width: 150px;"> 
                                                    
                                                    <!-- ELIMINAR DE A UNO -->
                                                    <form action="../controllers/carrito.php" method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="accion" value="eliminar_uno"> 
                                                        <button type="submit" 
                                                            name="eliminarUno"  
                                                            class="btn btn-outline-secondary btn-sm rounded-end-0 border-end-0" 
                                                            title="Quitar una unidad"
                                                            <?php echo ($item['cantidad'] <= 1) ? 'disabled' : ''; ?> 
                                                        >
                                                            <i class="bi bi-dash-lg"></i>
                                                        </button>
                                                    </form>

                                                    <!-- INPUT DE CANTIDAD -->
                                                    <input type="number" 
                                                        name="cantidades[<?php echo $item['id']; ?>]" 
                                                        value="<?php echo $item['cantidad']; ?>" 
                                                        min="1" 
                                                        max="<?php echo $item['stock']; ?>" 
                                                        class="form-control form-control-sm text-center border-secondary" 
                                                        style="width: 40px; padding: 0; z-index: 1;"
                                                    >
                                                    
                                                    <!-- AGREGAR DE A UNO -->
                                                    <form action="../controllers/carrito.php" method="POST">
                                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="accion" value="agregar_uno"> 
                                                        <button type="submit" 
                                                            name="agregarUno"  
                                                            class="btn btn-outline-secondary btn-sm rounded-start-0 border-start-0" 
                                                            title="Agregar una unidad"
                                                            <?php echo ($item['cantidad'] >= $item['stock']) ? 'disabled' : ''; ?> 
                                                        >
                                                            <i class="bi bi-plus-lg"></i>
                                                        </button>
                                                    </form>
                                                </div> 
                                                    <div class="text-end me-3" style="width: 100px;">
                                                    <span class="fw-bold">$<?php echo number_format($item['subtotal'], 2, ',', '.'); ?></span>
                                                </div>

                                                <!-- ELIMINAR PRODUCTO COMPLETO -->
                                                <form action="../controllers/carrito.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <input type="hidden" name="accion" value="eliminar"> 
                                                    
                                                    <button type="submit" 
                                                        name="eliminar" 
                                                        value="<?php echo $item['id']; ?>"
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Eliminar producto completo"
                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            
                                
                                <div class="p-3 d-flex justify-content-between">
                                   
                                    <a href="../index.php" class="btn btn-outline-primary">
                                        <i class="bi bi-box-arrow-left"></i> Seguir Comprando
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RESUMEN -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card bg-light shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Resumen de Compra</h4>
                            
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item bg-light d-flex justify-content-between fw-bold">
                                    <span>Productos (<?php echo $total_items_carrito; ?> ítems)</span>
                                    <span>$<?php echo number_format($total_general, 2, ',', '.'); ?></span>
                                </li>
                                <li class="list-group-item bg-light d-flex justify-content-between">
                                    <span>Envío</span>
                                    <span class="text-success fw-bold">Gratis</span> 
                                </li>
                            </ul>

                            <h3 class="d-flex justify-content-between mb-4">
                                <span>Total:</span>
                                <span class="text-primary fw-bolder">$<?php echo number_format($total_general, 2, ',', '.'); ?></span>
                            </h3>

                            <a href="checkout.php" class="btn btn-lg btn-success w-100">
                                <i class="bi bi-wallet2 me-2"></i> Finalizar Compra
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
