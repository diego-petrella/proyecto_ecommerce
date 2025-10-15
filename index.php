<?php
session_start();

define('APP_INCLUDED', true);

// 1. VERIFICACIÓN DE ADMINISTRADOR LOGUEADO
if (isset($_SESSION['usuario_id']) && ($_SESSION['usuario_rol_id'] == 1)) {
    // Redirige al administrador a su panel
    header("Location: /programacion2/articulos/views/panel_admin.php");
    exit;
}

include './views/productos.php'; 
?>