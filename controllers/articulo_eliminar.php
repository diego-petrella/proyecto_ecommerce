<?php
require "../models/funciones.php";
$id = $_GET["id"];
eliminarArticulo($id);

header("Location: /programacion2/articulos/views/panel_admin.php");
?>