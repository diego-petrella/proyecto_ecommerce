<?php
$usuario_logueado = isset($_SESSION['usuario_id']);
$cliente_email = $_SESSION['usuario_email'] ?? null;
$es_admin = (isset($_SESSION['usuario_rol_id']) && $_SESSION['usuario_rol_id'] == 1); 

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
        .product-card { transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .product-img { height: 200px; width: 100%; object-fit: contain; padding: 10px; }
    </style>
</head>
<body>
      
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-5">
    <div class="container">
        
        <a class="navbar-brand text-primary fw-bold" href="index.php">
            <i class="bi bi-shop me-2"></i> Mi Tienda E-commerce
        </a>
        
        <div class="d-flex align-items-center">
        
            <?php 
            //SOLO PARA ADMINISTRADOR
            if ($es_admin) { ?>
                <a class="btn btn-warning me-3 d-none d-sm-inline" href="./views/panel_admin.php" title="Ir al Panel de Administración">
                    <i class="bi bi-gear-fill me-1"></i> Panel Admin
                </a>
            <?php } ?>

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
    