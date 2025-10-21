<?php
$index = '/programacion2/articulos/'; 
$login = $index . 'views/login.php';
$cliente_inicio = $index . 'index.php';
$panel_admin = $index . 'views/panel_admin.php';



function rolRequerido($rol) {
    
    //SI NO HAY NADA EN SESSION LO MANDO AL LOGIN
    if (!isset($_SESSION['usuario_id'])) {
        global $login;
        header("Location: " . $login);
        exit;
    }

    $rol_actual = $_SESSION['usuario_rol_id'] ?? 0;
    
  // REDIRIJO según el ROL DEL USUARIO
    if ($rol_actual != $rol) {
        global $cliente_inicio, $panel_admin, $login;
        
        //SI ES ADMIN
        if ($rol_actual == 1) {
            header("Location: " . $panel_admin); 
        } 
        
        // SI ES CLIENTE
        elseif ($rol_actual == 2) {
            header("Location: " . $cliente_inicio); 
        } 
        
        //SI ES DESCONOCIDO O 0 AL LOGIN
        else {
            header("Location: " . $login); 
        }
        
        exit;
    }


}
?>