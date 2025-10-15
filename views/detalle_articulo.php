<?php
// views/detalle.php
session_start();

// 1. Incluimos las funciones del modelo
// Asume que la carpeta 'models' está un nivel arriba de 'views'
require '../models/funciones.php'; 

// 2. Obtener el ID del artículo de la URL
$articulo_id = $_GET['id'] ?? null;

// Si no hay ID o no es un número válido, redirigimos a la tienda
if (!$articulo_id || !is_numeric($articulo_id)) {
    header("Location: ../index.php");
    exit;
}

// 3. Obtener los datos del artículo
$articulo = buscarPorId($articulo_id);

// Si el artículo no existe, mostramos un error o redirigimos
if (!$articulo) {
    header("Location: ../index.php?error=notfound"); // Podrías redirigir con un mensaje
    exit;
}

// Variables de sesión para la barra de navegación (solo para el display)
$usuario_logueado = isset($_SESSION['usuario_id']);
$cliente_email = $_SESSION['usuario_email'] ?? null;

// Formato de precio
$precio_formateado = number_format($articulo["precio"], 2, ',', '.');
$marca_nombre = $articulo["nombre_categoria"] ?? 'Sin Marca';


$total_items_carrito = contarProductosCarrito();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($articulo["nombre"]); ?> | Detalle</title>
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
                    <a class="btn btn-outline-danger btn-sm me-3" href="../controllers/logout_controller.php" title="Cerrar Sesión">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </a>
                <?php } else { ?>
                    <a class="btn btn-primary me-3" href="login.php" title="Iniciar Sesión o Registrarse">
                        <i class="bi bi-person"></i> Iniciar Sesión
                    </a>
                <?php } ?>
            
                <a class="btn btn-success position-relative" href="../views/carrito.php">
                    <i class="bi bi-cart4"></i> Carrito
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                       <?php echo $total_items_carrito; ?> 
                        <span class="visually-hidden">Productos en el carrito</span>
                    </span>
                </a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <img src="../assets/img/<?php echo $articulo['imagen']; ?>" 
                     class="img-fluid rounded shadow-lg" 
                     alt="<?php echo htmlspecialchars($articulo["nombre"]) ?>" 
                     onerror="this.onerror=null; this.src='../assets/img/default.jpg';"
                >
            </div>

            <div class="col-md-6">
    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($articulo["nombre"]) ?></h1>
    
    <div class="mb-4">
        <span class="badge bg-secondary me-3 p-2">
            <i class="bi bi-tag-fill me-1"></i> Categoria: <?php echo htmlspecialchars($marca_nombre) ?>
        </span>
        
        <?php if ($articulo["stock"] > 0) { ?>
            <span class="badge bg-success p-2">
                <i class="bi bi-check-circle-fill me-1"></i> Stock Disponible: <?php echo htmlspecialchars($articulo["stock"]) ?>
            </span>
        <?php } else { ?>
            <span class="badge bg-danger p-2">
                <i class="bi bi-x-octagon-fill me-1"></i> ¡Agotado!
            </span>
        <?php } ?>
    </div>
    
    <h2 class="text-success fw-bold mb-4">$<?php echo $precio_formateado ?></h2>
    
    <p class="mb-4"><?php echo htmlspecialchars($articulo["descripcion_corta"] ?? 'Producto sin descripción detallada.'); ?></p>

    <form action="../controllers/carrito.php" method="POST" class="d-flex align-items-center">
        
        <input type="hidden" name="id" value="<?php echo $articulo['id']; ?>">
        <input type="hidden" name="accion" value="agregar">

        <?php if ($articulo["stock"] > 0) { ?>
            <div class="input-group me-3" style="width: 150px;">
                <span class="input-group-text">Cant.</span>
                <input type="number" name="cantidad" class="form-control text-center" value="1" min="1" max="<?php echo htmlspecialchars($articulo["stock"]); ?>" required>
            </div>
            
            <button type="submit" class="btn btn-lg btn-primary flex-grow-1">
                <i class="bi bi-cart-plus me-2"></i> Agregar al Carrito
            </button>
        <?php } else { ?>
            <button type="button" class="btn btn-lg btn-danger flex-grow-1" disabled>
                Producto Agotado
            </button>
        <?php } ?>
        
    </form>
    </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>