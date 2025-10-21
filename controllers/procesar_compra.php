<?php
session_start();
require '../models/funciones.php'; 

//Seguridad
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header('Location: ../views/carrito.php');
    exit;
}

$id_usuario = (int)$_POST['id_usuario'];
$carrito = $_SESSION['carrito']; 
$total_final_form = (float)$_POST['total_final_visible']; 
$subtotal_form = (float)$_POST['subtotal'];

//CARGO LOS DATOS DEL USUARIO REGISTRADO
$datos_envio = [
    'nombre' => trim($_POST['nombre']),
    'apellido' => trim($_POST['apellido']),
    'direccion' => trim($_POST['direccion']),
    'telefono' => trim($_POST['telefono']),
    'email' => trim($_POST['email']),
];

//GUARDO VENTA
$id_pedido = guardarOrdenDeVenta($id_usuario, $datos_envio, $carrito, $subtotal_form, $total_final_form); 

if ($id_pedido) {
    //SI SE REALIZO LA VENTA, VACIO EL CARRITO
    unset($_SESSION['carrito']); 
    $_SESSION['compra_exitosa'] = "Tu pedido #{$id_pedido} ha sido confirmado.";
    
    header('Location: ../views/confirmacion.php?pedido=' . $id_pedido); 
} else {
   
    $_SESSION['error_compra'] = "Hubo un error al procesar tu pedido. Inténtalo de nuevo.";
    header('Location: ../views/checkout.php');
}
exit;