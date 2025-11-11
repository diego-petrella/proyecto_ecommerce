<?php
session_start();
if (!defined('APP_INCLUDED')) {
    define('APP_INCLUDED', true);
}


$id_pedido = (int)($_GET['pedido_id'] ?? 0); 

$mensaje_confirmacion = $_SESSION['compra_exitosa'] ?? 'Tu pedido se ha realizado con éxito.';
$compra_confirmada_en_sesion = isset($_SESSION['compra_exitosa']);

if ($compra_confirmada_en_sesion) {
    unset($_SESSION['compra_exitosa']);
}

if ($id_pedido === 0 && !$compra_confirmada_en_sesion) {
    header('Location: ../index.php');
    exit;
}
// ------------------------------------

$cliente_email = $_SESSION['usuario_email'] ?? 'Estimado Cliente';
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>¡Pedido Registrado! | Mi Tienda</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            min-height: 100vh;
        }
        .alert-info p, .alert-info h2 {
             margin: 0;
        }
    </style>
</head>
<body class="d-flex flex-column h-100"> 

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary fw-bold" href="../index.php">
                <i class="bi bi-shop me-2"></i> Mi Tienda E-commerce
            </a>
        </div>
    </nav>
</header>

<main class="flex-shrink-0 d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6"> 
                <div class="card shadow-lg p-4 p-md-5 text-center">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    
                    <h1 class="mt-4 mb-3 text-success">
                        <?php echo ($id_pedido > 0) ? "¡Pedido Registrado!" : "¡Compra Exitosa!"; ?>
                    </h1>
                    
                    <p class="lead">
                        Gracias por tu compra, **<?php echo htmlspecialchars($cliente_email); ?>**.
                    </p>
                    
                    <p class="mb-4">
                        <?php 
                            if ($id_pedido > 0 && !$compra_confirmada_en_sesion) {
                                echo "Tu orden ha sido registrada correctamente. Revisa tu email para los detalles de pago y entrega.";
                            } else {
                                echo htmlspecialchars($mensaje_confirmacion);
                            }
                        ?>
                    </p>
                    
                    <?php if ($id_pedido > 0): ?>
                        <div class="alert alert-info py-2">
                            <p class="mb-0 fw-bold">Tu número de pedido es:</p>
                            <h2 class="display-6 mb-0 text-primary">#<?php echo $id_pedido; ?></h2>
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">
                    
                    <a href="../index.php" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-arrow-left me-2"></i> Volver a la Tienda
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">Mi Tienda E-commerce &copy; <?php echo date('Y'); ?></span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>