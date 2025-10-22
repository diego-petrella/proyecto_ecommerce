<?php
require "../models/funciones.php";


$payload = file_get_contents("php://input");


$datos = json_decode($payload, true);

$respuesta = ['estado' => 'error', 'mensaje' => 'ID no proporcionado en la solicitud.']; 

if (isset($datos["id"])) {
    $id = (int) $datos["id"];
    
    
    $resultado = eliminarArticulo($id); 

    
    
    if ($resultado) {
        $respuesta['estado'] = 'ok';
        $respuesta['mensaje'] = 'Artículo ID ' . $id . ' eliminado con éxito.';
    } else {
        
        $respuesta['mensaje'] = 'Fallo en la base de datos';
    }
}


echo json_encode($respuesta);
exit;

?>
