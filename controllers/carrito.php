<?php
session_start();
require '../models/funciones.php'; // Asegúrate de que esta ruta sea correcta

// ---------------------------------------------
// FUNCIÓN DE EJEMPLO para STOCK (Debe ir en funciones.php)
// Usamos un array de ejemplo. En producción, esto consulta la DB.
// ---------------------------------------------
function obtenerStockProducto($id) {
    // Ejemplo: Stock fijo para simulación.
    // Reemplaza esto con una consulta real a tu base de datos.
    $stock_ejemplo = [
        1 => 10,
        2 => 5,
        3 => 8,
        // ... otros productos
    ];
    return $stock_ejemplo[$id] ?? 999; // Retorna 999 si no se encuentra (stock alto por defecto)
}
// ---------------------------------------------

// Inicializar el carrito si no existe
$carrito = $_SESSION['carrito'] ?? [];

// Obtener la acción y el ID del producto
$accion = $_POST['accion'] ?? null;
// El ID del producto puede venir de diferentes campos dependiendo de la acción
$idProducto = (int)($_POST['id'] ?? $_POST['eliminar'] ?? $_POST['eliminarUno'] ?? $_POST['agregarUno'] ?? 0);

if ($accion === 'agregar' && $idProducto > 0) {
    // Lógica para agregar un producto desde la vista principal
    $cantidad_a_agregar = (int)($_POST['cantidad'] ?? 1);
    
    // Obtener el stock máximo
    $max_stock = obtenerStockProducto($idProducto);
    
    // Calcular la nueva cantidad total en el carrito
    $nueva_cantidad = ($carrito[$idProducto] ?? 0) + $cantidad_a_agregar;

    if ($nueva_cantidad <= $max_stock) {
        $carrito[$idProducto] = $nueva_cantidad;
    } else {
        // Establecer la cantidad al máximo permitido y generar un mensaje
        $carrito[$idProducto] = $max_stock;
        $_SESSION['mensaje_error'] = "No se pudo agregar más. Solo quedan " . $max_stock . " unidades en stock.";
    }

} 
// 1. ELIMINAR 1 UNIDAD (Remove One: botón "-")
elseif ($accion === 'remove_one' && $idProducto > 0) {
    if (isset($carrito[$idProducto])) {
        $carrito[$idProducto]--;
        
        // Si la cantidad llega a cero o menos, eliminamos la entrada del carrito
        if ($carrito[$idProducto] <= 0) {
            unset($carrito[$idProducto]); 
        }
    }
}
// 2. AGREGAR 1 UNIDAD (Add One: botón "+")
elseif ($accion === 'add_one' && $idProducto > 0) {
    if (isset($carrito[$idProducto])) {
        $max_stock = obtenerStockProducto($idProducto); 
        
        if ($carrito[$idProducto] < $max_stock) {
            $carrito[$idProducto]++;
        } else {
            // Opcional: Generar un mensaje de stock si se intenta superar
            $_SESSION['mensaje_error'] = "Has alcanzado el límite de stock (" . $max_stock . ") para este producto.";
        }
    }
} 
// 3. ELIMINAR PRODUCTO COMPLETO (Remove All: botón de papelera)
elseif ($accion === 'eliminar' && $idProducto > 0) {
    if (isset($carrito[$idProducto])) {
        // Eliminamos la clave del array (el producto completo)
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

// Redireccionar siempre a la vista del carrito
header('Location: ../views/carrito.php');
exit;
?>