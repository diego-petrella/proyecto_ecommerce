<?php
$index = '/programacion2/articulos/'; 
$login = $index . 'views/login.php';
$cliente_inicio = $index . 'index.php';
$panel_admin = $index . 'views/panel_admin.php';



function rolRequerido($rol) {
    
    // Si no hay sesión, mandamos al login
    if (!isset($_SESSION['usuario_id'])) {
        global $login;
        header("Location: " . $login);
        exit;
    }

    $rol_actual = $_SESSION['usuario_rol_id'] ?? 0;
    
  // Si el rol actual es diferente al requerido, redirigimos según el ROL DEL USUARIO
    if ($rol_actual != $rol) {
        global $cliente_inicio, $panel_admin, $login;
        
        // 1. Si el usuario actual es un Administrador (Rol 1), lo mandamos a su panel.
        if ($rol_actual == 1) {
            header("Location: " . $panel_admin); 
        } 
        
        // 2. Si el usuario actual es un Cliente (Rol 2), lo mandamos a la tienda.
        elseif ($rol_actual == 2) {
            header("Location: " . $cliente_inicio); 
        } 
        
        // 3. Si el rol es desconocido (o 0), lo mandamos al login.
        else {
            header("Location: " . $login); 
        }
        
        exit;
    }


}
?>