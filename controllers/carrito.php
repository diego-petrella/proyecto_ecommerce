<?php
session_start();
require '../models/funciones.php';

$carrito = $_SESSION['carrito'] ?? [];

$accion = $_POST['accion'] ?? null;

//TOMO ID Y ACCION
$idProducto = (int)($_POST['id'] ?? $_POST['eliminar'] ?? $_POST['eliminarUno'] ?? $_POST['agregarUno'] ?? 0);

if ($accion === 'agregar' && $idProducto > 0) {
    $cantidad_a_agregar = (int)($_POST['cantidad'] ?? 1);
    
    //CANTIDAD MAXIMA QUE PUEDE AGREGAR
    $max_stock = obtenerStockProducto($idProducto); 
    
    $nueva_cantidad = ($carrito[$idProducto] ?? 0) + $cantidad_a_agregar;

    if ($nueva_cantidad <= $max_stock) {
        $carrito[$idProducto] = $nueva_cantidad;
    } else {
     
        $carrito[$idProducto] = $max_stock;
        $_SESSION['mensaje_error'] = "No se pudo agregar más. Solo quedan " . $max_stock . " unidades en stock.";
    }

} 
//ELIMINAR DE A UNO
elseif ($accion === 'eliminar_uno' && $idProducto > 0) {
    if (isset($carrito[$idProducto])) {
        $carrito[$idProducto]--;
        
        //SI LA CANTIDAD ES MENOR AL STOCK, EKIMINO LA ENTRADA DEL CARRITO
        if ($carrito[$idProducto] <= 0) {
            unset($carrito[$idProducto]); 
        }
    }
}
//AGREGAR DE A UNO
elseif ($accion === 'agregar_uno' && $idProducto > 0) {
    if (isset($carrito[$idProducto])) {
        $max_stock = obtenerStockProducto($idProducto); 
        
        if ($carrito[$idProducto] < $max_stock) {
            $carrito[$idProducto]++;
        } else {
            //MENSAJE SI NO HAY MAS PARA AGREGAR
            $_SESSION['mensaje_error'] = "Has alcanzado el límite de stock (" . $max_stock . ") para este producto.";
        }
    }
} 
//ELIMINAR PRODUCTO COMPLETO
elseif ($accion === 'eliminar' && $idProducto > 0) {
    if (isset($carrito[$idProducto])) {
        unset($carrito[$idProducto]); 
    }
} 


// Guardar el carrito actualizado en la sesión
$_SESSION['carrito'] = $carrito;


header('Location: ../views/carrito.php');
exit;
?>