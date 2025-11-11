<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/programacion2/articulos/');
}


$usuario_logueado = isset($_SESSION['usuario_id']);
$cliente_email = $_SESSION['usuario_email'] ?? null;
$es_admin = (isset($_SESSION['usuario_rol_id']) && $_SESSION['usuario_rol_id'] == 1); 


$total_items_carrito = 0;
if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $cantidad) {
        $total_items_carrito += $cantidad;
    }
}

$titulo_pagina = $titulo_pagina ?? 'Mi Tienda E-commerce'; 


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo $titulo_pagina; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .product-card { transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .product-img { height: 200px; width: 100%; object-fit: contain; padding: 10px; }
        
        .dropdown-menu-end { right: 0; left: auto; }
        .dropdown-item i { width: 20px; }
    </style>
</head>
<body>
      
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-5">
    <div class="container">
        
        <a class="navbar-brand text-primary fw-bold" href="<?php echo BASE_URL; ?>index.php">
            <i class="bi bi-shop me-2"></i> Mi Tienda E-commerce
        </a>
        
        <div class="d-flex align-items-center ms-auto">
    
            <?php if ($usuario_logueado) { ?>
                
                <?php if ($es_admin) { ?>
                    <a class="btn btn-warning me-3" href="<?php echo BASE_URL; ?>views/panel_admin.php">
                        <i class="bi bi-gear-fill me-1"></i> Panel Admin
                    </a>
                <?php } ?>

                <span class="navbar-text me-3 d-none d-sm-inline text-muted small">
                    Hola, <?php echo htmlspecialchars($cliente_email); ?>
                </span>
                
                <div class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle btn btn-light btn-sm" href="#" id="navbarDropdownCuenta" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                         <i class="bi bi-person-fill me-1"></i> Mi Cuenta
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownCuenta">
                        
                        <li>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>views/mi_cuenta.php?tab=datos">
                                <i class="bi bi-person-vcard me-2"></i> Mi Perfil
                            </a>
                        </li>
                        <li>
                            <?php if (!$es_admin) { ?>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>views/mi_cuenta.php?tab=pedidos">
                                <i class="bi bi-receipt me-2"></i> Mis Pedidos
                            </a>
                            <?php } ?>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li>
                            <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>controllers/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
                <?php } else { ?>
                
                <a class="btn btn-primary me-3" href="<?php echo BASE_URL; ?>views/login.php" title="Iniciar Sesión o Registrarse">
                    <i class="bi bi-person"></i> Iniciar Sesión
                </a>

            <?php } ?>
        
            <a class="btn btn-success position-relative" href="<?php echo BASE_URL; ?>views/carrito.php">
                <i class="bi bi-cart4"></i> 
                <span class="d-none d-sm-inline ms-1">Carrito</span>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  <?php echo $total_items_carrito; ?> 
                    <span class="visually-hidden">Productos en el carrito</span>
                </span>
            </a>
        </div>
    </div>
</nav>