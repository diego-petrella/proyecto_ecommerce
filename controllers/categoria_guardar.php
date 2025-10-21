<?php
session_start();
require '../controllers/verificacion_usuario.php';
rolRequerido(1); 

require "../models/funciones.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/categorias.php");
    exit;
}

$nombre = trim($_POST["nombre"] ?? '');
$id = (int)($_POST["id"] ?? 0);

if (empty($nombre)) {
    header("Location: ../views/categorias.php?error=nombre_vacio");
    exit;
}

$datos_categoria = [
    "nombre" => $nombre
];

if ($id > 0) {
    $datos_categoria["id"] = $id;
}

if (guardarCategoria($datos_categoria)) {
    header("Location: ../views/categorias.php?success=guardado");
} else {
    header("Location: ../views/categorias.php?error=bd_fallo");
}
exit;