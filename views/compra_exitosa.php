<?php
session_start();
if (!defined('BASE_URL')) {
    define('BASE_URL', '/programacion2/articulos/');
}

$status_mp = $_GET['collection_status'] ?? $_GET['status'] ?? 'pending';

// Lógica para vaciar el carrito
if (strtolower($status_mp) === 'approved' || strtolower($status_mp) === 'success') {
    
    unset($_SESSION['carrito']); 
}

require "../includes/header.php"; 

// Convertimos el estado de MP a un estado conocido
switch (strtolower($status_mp)) {
    case 'approved':
    case 'success':
        $titulo = "¡Pago Aprobado!";
        $mensaje = "Tu pedido se ha procesado correctamente. Recibirás un correo electrónico de confirmación pronto.";
        $icono = "bi-check-circle-fill";
        $clase = "alert-success";
        $texto_clase = "text-success";
        break;
    case 'pending':
    case 'in_process':
        $titulo = "Pago Pendiente de Aprobación";
        $mensaje = "Tu pago está siendo procesado por Mercado Pago. Esto puede tardar unos minutos. Te notificaremos cuando se apruebe.";
        $icono = "bi-hourglass-split";
        $clase = "alert-warning";
        $texto_clase = "text-warning";
        break;
    case 'rejected':
    case 'failure':
    case 'cancelled':
        $titulo = "Fallo en el Pago";
        $mensaje = "Lamentablemente, la transacción no pudo ser procesada. Por favor, intenta de nuevo o prueba con otro medio de pago.";
        $icono = "bi-x-octagon-fill";
        $clase = "alert-danger";
        $texto_clase = "text-danger";
        break;
    default:
        $titulo = "Estado Desconocido";
        $mensaje = "No pudimos determinar el estado de la transacción. Revisa tu historial de pedidos.";
        $icono = "bi-question-circle";
        $clase = "alert-info";
        $texto_clase = "text-info";
        break;
}
?>


<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg p-4">
                
                <div class="card-body text-center">
                    
                    <i class="bi <?php echo $icono; ?> <?php echo $texto_clase; ?>" style="font-size: 5rem;"></i>
                    
                    <h1 class="my-4 <?php echo $texto_clase; ?>"><?php echo $titulo; ?></h1>
                    
                    <div class="alert <?php echo $clase; ?>" role="alert">
                        <p class="lead mb-0"><?php echo $mensaje; ?></p>
                        
                        <?php if (isset($_GET['collection_id'])): ?>
                            <small class="d-block mt-2">ID de Transacción: <?php echo htmlspecialchars($_GET['collection_id']); ?></small>
                        <?php endif; ?>
                    </div>

                    <?php if (strpos($status_mp, 'fail') !== false || strpos($status_mp, 'reject') !== false || strpos($status_mp, 'pending') !== false): ?>
                        <a href="<?php echo BASE_URL; ?>views/checkout.php" class="btn btn-primary btn-lg mt-3">
                            <i class="bi bi-arrow-clockwise me-2"></i> Intentar de Nuevo
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary mt-3">
                        <i class="bi bi-shop me-2"></i> Volver a la Tienda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require "../includes/footer.php"; 
?>