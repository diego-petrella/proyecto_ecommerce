<?php
session_start();
require '../models/funciones.php';

$carrito = $_SESSION['carrito'] ?? [];

$accion = $_POST['accion'] ?? null;

//Tomo el ID del producto dependiendo de la acción
$idProducto = (int)($_POST['id'] ?? $_POST['eliminar'] ?? $_POST['eliminarUno'] ?? $_POST['agregarUno'] ?? 0);

if ($accion === 'agregar' && $idProducto > 0) {
    $cantidad_a_agregar = (int)($_POST['cantidad'] ?? 1);
    
    $max_stock = obtenerStockProducto($idProducto);
    
    $nueva_cantidad = ($carrito[$idProducto] ?? 0) + $cantidad_a_agregar;

    if ($nueva_cantidad <= $max_stock) {
        $carrito[$idProducto] = $nueva_cantidad;
    } else {
        //Cantidad al máximo permitida
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
// 4. ACTUALIZAR CANTIDADES (Update All: botón "Actualizar Cantidades")
elseif ($accion === 'actualizar' && isset($_POST['cantidades'])) {
    foreach ($_POST['cantidades'] as $id => $nueva_cantidad) {
        $id = (int)$id;
        $nueva_cantidad = (int)$nueva_cantidad;
        
        if ($nueva_cantidad <= 0) {
            // Eliminar si la cantidad es cero o negativa
            unset($carrito[$id]);
        } else {
            // Validar contra stock antes de actualizar
            $max_stock = obtenerStockProducto($id);
            if ($nueva_cantidad <= $max_stock) {
                $carrito[$id] = $nueva_cantidad;
            } else {
                // Si excede, se fija al máximo
                $carrito[$id] = $max_stock;
                $_SESSION['mensaje_error'] = "La cantidad del producto ID: " . $id . " se ajustó al máximo de stock disponible (" . $max_stock . ").";
            }
        }
    }
}

// Guardar el carrito actualizado en la sesión
$_SESSION['carrito'] = $carrito;


header('Location: ../views/carrito.php');
exit;
?>