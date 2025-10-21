<?php
session_start();
require "../models/funciones.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'], $_POST['password'])) {
    header("Location: /views/login.php");
    exit;
}

  $email = $_POST["email"];
  $password = $_POST["password"];

$usuario= verificarUsuario($email, $password);

if ($usuario) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rol_id'] = $usuario['id_rol'];
   
    if ($usuario['id_rol'] == 1) {
        vaciarCarrito();
        
        header("Location: /programacion2/articulos/views/panel_admin.php"); 
        
    } elseif ($usuario['id_rol'] == 2) {
        header("Location: /programacion2/articulos/index.php"); 
        
    } else {
      
        header("Location: /programacion2/articulos/index.php");
    }
    
    exit;

} else {
    //FALLO DE AUTENTICACIÓN
    $_SESSION['login_error'] = "Email o contraseña incorrectos.";
    header("Location: /programacion2/articulos/views/login.php");
    exit;
}


?>