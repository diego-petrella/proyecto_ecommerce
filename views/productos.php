<?php
if (!defined('APP_INCLUDED')) {
    // Si la llave no está definida, aborta la ejecución y muestra un error 403 (Prohibido)
    http_response_code(403);
    die('Acceso no permitido.');
}
require "./models/funciones.php"; 

$total_items_carrito = contarProductosCarrito();
$articulos = obtenerArticulos($filtro_nombre=0, $filtro_marca=0); 

$usuario_logueado = isset($_SESSION['usuario_id']);
$cliente_email = $_SESSION['usuario_email'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tienda | Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Estilo simple para las tarjetas de producto */
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .product-img {
           height: 200px;
           width: 100%;
           object-fit: contain;
           padding: 10px; 
        }
    </style>
</head>
<body>
      
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-5">
        <div class="container">
            
            <a class="navbar-brand text-primary fw-bold" href="index.php">
                <i class="bi bi-shop me-2"></i> Mi Tienda E-commerce
            </a>
            
            <div class="d-flex align-items-center">
            
                <?php if ($usuario_logueado) { ?>
                    <span class="navbar-text me-3 d-none d-sm-inline">
                        <i class="bi bi-person-circle me-1"></i> Bienvenido, **<?php echo htmlspecialchars($cliente_email); ?>**
                    </span>
                    
                    <a class="btn btn-outline-danger btn-sm me-3" href="controllers/logout.php" title="Cerrar Sesión">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </a>
                <?php } else { ?>
                    <a class="btn btn-primary me-3" href="./views/login.php" title="Iniciar Sesión o Registrarse">
                        <i class="bi bi-person"></i> Iniciar Sesión
                    </a>
                <?php } ?>
            
                <a class="btn btn-success position-relative" href="./views/carrito.php">
                    <i class="bi bi-cart4"></i> Carrito
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                      <?php echo $total_items_carrito; ?> 
                        <span class="visually-hidden">Productos en el carrito</span>
                    </span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4 text-center">Nuestros Artículos</h1>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            
            <?php 
            if (empty($articulos)): 
            ?>
                <div class="col-12 text-center my-5">
                    <p class="lead text-muted">Aún no hay productos disponibles en la tienda.</p>
                </div>
            <?php 
            else:
                foreach($articulos as $articulo) {
                   
                    $precio_formateado = number_format($articulo["precio"], 2, ',', '.');
                    $marca_nombre = $articulo["nombre_marca"] ?? 'Sin Marca'; 
                    $imagen_nombre = $articulo["imagen"] ?? 'default.jpg'; 
            ?>
                <div class="col">
                    <div class="card h-100 product-card shadow-sm border-0">
                       <!-- CÓDIGO CORREGIDO PARA LA IMAGEN -->
<img src="assets/img/<?php echo htmlspecialchars($imagen_nombre); ?>" 
     class="card-img-top product-img" 
     alt="<?php echo htmlspecialchars($articulo["nombre"]) ?>"  
     onerror="this.onerror=null; this.src='assets/img/default.jpg';">
                    
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($articulo["nombre"]) ?></h5>
                            <p class="card-text text-muted mb-1 small"><?php echo htmlspecialchars($marca_nombre) ?></p>
                            
                            <div class="mt-auto mb-3">
                                <span class="h4 fw-bold text-success">$<?php echo $precio_formateado ?></span>
                            </div>
                            
                            <a href='/programacion2/articulos/views/detalle_articulo.php?id=<?php echo htmlspecialchars($articulo["id"]) ?>' class="btn btn-primary w-100">
                                <i class="bi bi-bag-plus me-2"></i> Ver Detalle
                            </a>
                        </div>
                    </div>
                </div>
            <?php 
                } 
            endif; 
            ?>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>