<?php
session_start();

define('APP_INCLUDED', true);

if (isset($_SESSION['usuario_id']) && ($_SESSION['usuario_rol_id'] == 1)) {
    header("Location: /programacion2/articulos/views/panel_admin.php");
    exit;
}

include './views/productos.php'; 
?>