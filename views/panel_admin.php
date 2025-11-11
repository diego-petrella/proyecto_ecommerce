<?php
session_start();

require_once __DIR__ . '/../controllers/verificacion_usuario.php';
rolRequerido(1); 
require_once __DIR__ . "/../models/funciones.php";


if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }

$current_view = $_GET['view'] ?? 'articulos';
$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';
$usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['usuario_id'] ?? 0; 

//MAPEO DE VISTAS
$view_map = [
    'articulos' => 'articulo1.php', 
    'categorias' => 'categorias.php',
    'usuarios' => 'gestion_usuarios.php',
    'pedidos' => 'gestion_pedidos.php',
    'abm_articulo' => 'abm_articulo.php', 
    'abm_usuario' => 'abm_usuario.php', 
    'abm_categoria' => 'abm_categoria.php'
];

$file_to_include = $view_map[$current_view] ?? 'articulo1.php';

$es_contenido_ajax = true; 

require '../includes/admin_nav.php'; 


$path_to_file = __DIR__ . '/' . $file_to_include;

if (file_exists($path_to_file)) {
    require $path_to_file;
} else {
    echo '<div class="container-fluid py-3">';
    echo '<div class="alert alert-danger" role="alert">';
    echo '<strong>Error 404:</strong> Vista no encontrada para el parámetro "' . htmlspecialchars($current_view) . '". Archivo buscado: ' . htmlspecialchars($file_to_include);
    echo '</div>';
    echo '</div>';
}

?>
    </div> 
</div> 


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


<div class="container-fluid py-3"> 
   
</div>