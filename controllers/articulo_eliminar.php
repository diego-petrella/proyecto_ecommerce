<?php
require "../models/funciones.php";
//$id = $_GET["id"];

$payload = file_get_contents("php://input");


$datos = json_decode($payload, true);

$respuesta = ['estado' => 'error', 'mensaje' => 'ID no proporcionado.'];


if (isset($datos["id"])) {
    $id = (int) $datos["id"];

    if (eliminarArticulo($id)) {
        $respuesta['estado'] = 'ok';
        $respuesta['mensaje'] = 'Artículo ID ' . $id . ' eliminado con éxito.';
    } else {
        $respuesta['mensaje'] = 'Fallo en la base de datos o artículo con dependencias.';
    }
}

echo json_encode($respuesta);
exit;
//header("Location: /programacion2/articulos/views/panel_admin.php");
?>
