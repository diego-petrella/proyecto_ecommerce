<?php
require "./models/funciones.php"; 

//PAGINACIÓN
$items_por_pagina = 8; 
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($pagina_actual - 1) * $items_por_pagina;

$filtro_nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : "";
$filtro_marca = isset($_GET['id_categoria']) ? $_GET['id_categoria'] : 0;


$total_articulos = contarTotalArticulos($filtro_nombre, $filtro_marca); 
$total_paginas = ceil($total_articulos / $items_por_pagina);
$total_items_carrito = contarProductosCarrito();


$articulos = obtenerArticulos($filtro_nombre, $filtro_marca, $items_por_pagina, $offset); 

$categorias_lista = obtenerCategorias();

$usuario_logueado = isset($_SESSION['usuario_id']);
$cliente_email = $_SESSION['usuario_email'] ?? null;


$filtros_query = http_build_query([
    'nombre' => $filtro_nombre,
    'id_categoria' => $filtro_marca
]);


?>