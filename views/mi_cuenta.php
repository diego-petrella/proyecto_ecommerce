<?php
session_start();
define('BASE_URL', '/programacion2/articulos/');

require __DIR__ . '/../controllers/verificacion_usuario.php'; 
require __DIR__ . '/../models/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'views/login.php');
    exit;
}

$id_usuario_sesion = (int)$_SESSION['usuario_id'];

$datos_usuario = buscarDatosUsuario($id_usuario_sesion); 
$nombre_usuario = $datos_usuario['nombre'] ?? '';
$apellido_usuario = $datos_usuario['apellido'] ?? '';
$direccion_usuario = $datos_usuario['direccion'] ?? '';
$telefono_usuario = $datos_usuario['telefono'] ?? '';
$email_usuario = $datos_usuario['email'] ?? '';


$pedidos = obtenerPedidosPorUsuario($id_usuario_sesion);


$tab_activa = $_GET['tab'] ?? 'pedidos'; 
$mostrar_exito_perfil = false;
$mostrar_error_perfil = false;


if ($tab_activa == 'datos') {
    if (isset($_SESSION['exito_perfil'])) {
        $mostrar_exito_perfil = true;
        $mensaje_perfil = $_SESSION['exito_perfil'];
        unset($_SESSION['exito_perfil']);
    }
    if (isset($_SESSION['error_perfil'])) {
        $mostrar_error_perfil = true;
        $mensaje_perfil = $_SESSION['error_perfil'];
        unset($_SESSION['error_perfil']);
    }
}

require __DIR__ . "/../includes/header.php"; 
?>


<div class="container my-5">
    <div class="row">

        <div class="col-md-3">
            <h4 class="mb-3"><i class="bi bi-person-fill me-2"></i> Mi Cuenta</h4>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                
                <a class="nav-link <?php echo ($tab_activa == 'pedidos') ? 'active' : ''; ?>" 
                   id="v-pills-pedidos-tab" 
                   data-bs-toggle="pill" 
                   href="#v-pills-pedidos" 
                   role="tab" 
                   aria-controls="v-pills-pedidos" 
                   aria-selected="<?php echo ($tab_activa == 'pedidos') ? 'true' : 'false'; ?>">
                    <i class="bi bi-receipt-cutoff me-2"></i> Mis Pedidos
                </a>
                
                <a class="nav-link <?php echo ($tab_activa == 'datos') ? 'active' : ''; ?>" 
                   id="v-pills-datos-tab" 
                   data-bs-toggle="pill" 
                   href="#v-pills-datos" 
                   role="tab" 
                   aria-controls="v-pills-datos" 
                   aria-selected="<?php echo ($tab_activa == 'datos') ? 'true' : 'false'; ?>">
                    <i class="bi bi-person-lines-fill me-2"></i> Mis Datos
                </a>
                
                <a class="nav-link <?php echo ($tab_activa == 'password') ? 'active' : ''; ?>" 
                   id="v-pills-password-tab" 
                   data-bs-toggle="pill" 
                   href="#v-pills-password" 
                   role="tab" 
                   aria-controls="v-pills-password" 
                   aria-selected="<?php echo ($tab_activa == 'password') ? 'true' : 'false'; ?>">
                    <i class="bi bi-key-fill me-2"></i> Cambiar Contraseña
                </a>
            </div>
        </div>

        
        <div class="col-md-9">
            <div class="tab-content" id="v-pills-tabContent">
                <!-- PESTAÑA MIS PEDIDOS -->
                <div class="tab-pane fade <?php echo ($tab_activa == 'pedidos') ? 'show active' : ''; ?>" id="v-pills-pedidos" role="tabpanel" aria-labelledby="v-pills-pedidos-tab">
                    <h2 class="mb-4">Mis Pedidos</h2>
                    <?php if (empty($pedidos)): ?>
                        <div class="alert alert-info">Aún no has realizado ningún pedido.</div>
                    <?php else: ?>
                        <div class="accordion" id="accordionPedidos">
                            <?php foreach ($pedidos as $pedido_lista): 
                                $pedido = obtenerDetalleDePedido($pedido_lista['id'], $id_usuario_sesion);
                                if (!$pedido) continue; 
                            ?>
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header" id="heading-<?php echo $pedido['id']; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse-<?php echo $pedido['id']; ?>" aria-expanded="false" 
                                                aria-controls="collapse-<?php echo $pedido['id']; ?>">
                                            <span class="fw-bold me-3">Pedido #<?php echo htmlspecialchars($pedido['id']); ?></span>
                                            <span class="me-auto text-muted">Fecha: <?php echo date("d/m/Y", strtotime($pedido['fecha_pedido'])); ?></span>
                                            <span class="fw-bold text-primary">$<?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                                        </button>
                                    </h2>
                                    <div id="collapse-<?php echo $pedido['id']; ?>" class="accordion-collapse collapse" 
                                         aria-labelledby="heading-<?php echo $pedido['id']; ?>" data-bs-parent="#accordionPedidos">
                                        
                                        <!-- ACORDEON -->
                                        <div class="accordion-body">
                                            <div class="row g-4">
                                                <div class="col-md-7">
                                                    <h5>Detalle de Productos</h5>
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Producto</th>
                                                                <th class="text-center">Cant.</th>
                                                                <th class="text-end">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($pedido['detalles'] as $detalle): ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo htmlspecialchars($detalle['nombre'] ?? 'Producto no disponible'); ?>
                                                                    <br>
                                                                    <small class="text-muted">$<?php echo number_format($detalle['precio_unitario'], 2, ',', '.'); ?> c/u</small>
                                                                </td>
                                                                <td class="text-center align-middle"><?php echo $detalle['cantidad']; ?></td>
                                                                <td class="text-end align-middle">$<?php echo number_format($detalle['precio_unitario'] * $detalle['cantidad'], 2, ',', '.'); ?></td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-5">
                                                    <h5>Datos de Envío</h5>
                                                    <ul class="list-unstyled small">
                                                        <li><strong>Nombre:</strong> <?php echo htmlspecialchars($pedido['nombre_cliente']); ?></li>
                                                        <li><strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion_envio']); ?></li>
                                                        <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($pedido['telefono_contacto']); ?></li>
                                                    </ul>
                                                    <hr>
                                                    <div class="d-flex justify-content-between">
                                                        <span>Subtotal:</span>
                                                        <span>$<?php echo number_format($pedido['subtotal'], 2, ',', '.'); ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span>Envío:</span>
                                                        <span>$<?php echo number_format($pedido['costo_envio'], 2, ',', '.'); ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between fw-bold h5 mt-2 pt-2 border-top">
                                                        <span>Total:</span>
                                                        <span>$<?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- PESTAÑA MIS DATOS -->
                
                <div class="tab-pane fade <?php echo ($tab_activa == 'datos') ? 'show active' : ''; ?>" id="v-pills-datos" role="tabpanel" aria-labelledby="v-pills-datos-tab">
                    <h2 class="mb-4">Mis Datos Personales</h2>
                    
                    <?php if ($mostrar_exito_perfil): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($mensaje_perfil); ?></div>
                    <?php endif; ?>
                    <?php if ($mostrar_error_perfil): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje_perfil); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>controllers/actualiza_perfil_usuario.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="perfil-nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="perfil-nombre" name="nombre" value="<?php echo htmlspecialchars($nombre_usuario); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="perfil-apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="perfil-apellido" name="apellido" value="<?php echo htmlspecialchars($apellido_usuario); ?>">
                            </div>
                            <div class="col-12">
                                <label for="perfil-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="perfil-email" name="email" value="<?php echo htmlspecialchars($email_usuario); ?>" readonly disabled>
                                <div class="form-text">El email no se puede modificar.</div>
                            </div>
                            <div class="col-12">
                                <label for="perfil-direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="perfil-direccion" name="direccion" value="<?php echo htmlspecialchars($direccion_usuario); ?>" placeholder="Ej: Calle Falsa 123">
                            </div>
                            <div class="col-md-6">
                                <label for="perfil-telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="perfil-telefono" name="telefono" value="<?php echo htmlspecialchars($telefono_usuario); ?>" placeholder="Ej: 1122334455">
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- PESTAÑA CAMBIAR CONTRASEÑA -->
                <div class="tab-pane fade <?php echo ($tab_activa == 'password') ? 'show active' : ''; ?>" id="v-pills-password" role="tabpanel" aria-labelledby="v-pills-password-tab">
                    <h2 class="mb-4">Cambiar Contraseña</h2>
                    
                    <?php if (isset($_SESSION['exito_pass'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['exito_pass']; unset($_SESSION['exito_pass']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error_pass'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error_pass']; unset($_SESSION['error_pass']); ?></div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>controllers/cambiar_contrasena.php" method="POST">
                         <div class="mb-3">
                            <label for="pass-actual" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="pass-actual" name="pass_actual" required>
                         </div>
                         <div class="mb-3">
                            <label for="pass-nueva" class="form-label">Contraseña Nueva</label>
                            <input type="password" class="form-control" id="pass-nueva" name="pass_nueva" required>
                         </div>
                         <div class="mb-3">
                            <label for="pass-confirm" class="form-label">Confirmar Contraseña Nueva</label>
                            <input type="password" class="form-control" id="pass-confirm" name="pass_confirm" required>
                         </div>
                        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                    </form>
                </div>
                
            </div>
        </div>

    </div> 
</div> 


<?php

require __DIR__ . "/../includes/footer.php"; 
?>

