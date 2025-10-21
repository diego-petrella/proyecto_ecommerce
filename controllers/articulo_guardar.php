<?php
require "../models/funciones.php";

$nombre_final = null;
$directorio_destino = '../assets/img/';

if (!@is_dir($directorio_destino) || !@is_writable($directorio_destino)) {
    
    die("ERROR CRÍTICO: La carpeta '$directorio_destino' no existe o no tiene permisos de escritura.");
}


if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    
    $nombre_archivo_original = $_FILES['imagen']['name'];
    $archivo_temporal = $_FILES['imagen']['tmp_name'];
    $error_codigo = $_FILES['imagen']['error'];

    
    if ($error_codigo === UPLOAD_ERR_OK) {
        
        
        $extension = strtolower(pathinfo($nombre_archivo_original, PATHINFO_EXTENSION));
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($extension, $extensiones_permitidas)) {
            
           
            $nombre_final_upload = uniqid('img_', true) . '.' . $extension;
            $ruta_destino = $directorio_destino . $nombre_final_upload;

            
            if (move_uploaded_file($archivo_temporal, $ruta_destino)) {
              
                $nombre_final = $nombre_final_upload;
            } else {
                
                error_log("Error al mover el archivo temporal. Permisos o destino incorrecto: $ruta_destino");
                
            }
        }
    } else {
        
        error_log("Fallo de subida: Código de error $error_codigo. Archivo: $nombre_archivo_original");
    }
}

$nombre_articulo = trim($_POST["nombre"] ?? 'Artículo sin Nombre');
$precio = (float)($_POST["precio"] ?? 0.00);
$descripcion_corta = $_POST["descripcion_corta"];
$stock = (int)($_POST["stock"] ?? 0);
$marca = $_POST["id_categoria"];

$articulo = [
    "nombre" => $nombre_articulo,
    "precio" => $precio,
    "descripcion_corta" => $descripcion_corta,
    "stock" => $stock,
    "id_categoria" => $marca,
    "imagen" => $nombre_final 
];

$hay_nueva_imagen = ($nombre_final !== null);

if ($hay_nueva_imagen) {
    $articulo_data["imagen"] = $nombre_final;
}

if (isset($_POST["id"])) {
    $articulo["id"] = (int)$_POST["id"];
}

guardarArticulo($articulo, $hay_nueva_imagen);


header("Location: ../views/panel_admin.php");
exit;

