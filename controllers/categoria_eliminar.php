<?php
session_start();
require '../controllers/verificacion_usuario.php';
rolRequerido(1);

require "../models/funciones.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: ../views/categorias.php?error=id_invalido");
    exit;
}


if (eliminarCategoria($id)) {
    
    header("Location: ../views/categorias.php?success=eliminado");
} else {
    
    header("Location: ../views/categorias.php?error=no_se_puede_eliminar");
}
exit;