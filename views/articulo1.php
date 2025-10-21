<?php
session_start();
require '../controllers/verificacion_usuario.php';

rolRequerido(1);

require "../models/funciones.php";


$id = isset($_GET["id"]) ? $_GET["id"] : 0;
$action = $_GET['action'] ?? null;
$token = md5(session_id());


$panel_admin = '/programacion2/articulos/views/panel_admin.php';

if ($action === 'nuevo') {
    if ($_GET['token'] !== $token) {
        header("Location: " . $panel_admin . "?error=token");
        exit;
    }
} elseif ($id == 0 && $action !== 'nuevo') {
    //SI NO HAY ID Y NO SE SELECCIONO NUEVO
    header("Location: " . $panel_admin);
    exit;
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol_id'] != 1) {
    //ACCESO NO AUTRIZADO
    session_unset();
    session_destroy();
    header("Location: ../views/login.php");
    exit;
}

$categorias_select = obtenerCategorias(); 

if ($id != 0) {
    
    $articulo = buscarPorId($id);
    
    if ($articulo === false) {
        header("Location: " . $panel_admin);
        exit; 
    }
    $titulo = "Modificar Artículo";
} else {
    
    $articulo = [
        "nombre" => "",
        "precio" => "", 
        "id_categoria" => "",
        "imagen" => "",
        "descripcion_corta" => "",
        "stock" => "",
    ];
    $titulo = "Nuevo Artículo";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $titulo ?> | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <header class="bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container-fluid">
            <h1 class="h4 m-0"><i class="bi bi-box-seam me-2"></i> Gestión de Artículos</h1>
        </div>
    </header>

    <div class="container my-5">
        
        <div class="card p-4 shadow-lg mx-auto" style="max-width: 600px;">
            
            <h2 class="text-center mb-4 border-bottom pb-3">
                <i class="bi bi-<?php echo ($id == 0) ? "plus-circle" : "pencil-square" ?> me-2"></i> 
                <?php echo $titulo ?>
            </h2>
            
            <form method="post" action="../controllers/articulo_guardar.php" enctype="multipart/form-data">

                <?php if ($id != 0) { ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($articulo["id"]) ?>" />
                <?php } ?>

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Artículo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                        value="<?php echo htmlspecialchars($articulo["nombre"]) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="precio" class="form-label">Precio ($)</label>
                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                        value="<?php echo htmlspecialchars($articulo["precio"]) ?>" required>
                    <div class="form-text">Usar punto como separador decimal. Ej: 1250.50</div>
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" 
                        value="<?php echo htmlspecialchars($articulo["stock"] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="descripcion_corta" class="form-label">Descripción Corta</label>
                    <textarea class="form-control" id="descripcion_corta" name="descripcion_corta" rows="3"><?php echo htmlspecialchars($articulo["descripcion_corta"]) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen del Artículo</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                    <?php if ($id != 0 && !empty($articulo['imagen'])) { ?>
                        <div class="mt-2">
                            <p>Imagen actual:</p>
                            <img src="../assets/img/<?php echo htmlspecialchars($articulo['imagen']) ?>" alt="Imagen de <?php echo htmlspecialchars($articulo['nombre']) ?>" style="max-width: 150px; height: auto;">
                        </div>
                    <?php } ?>
                </div>

                <div class="mb-4">
                    <label for="id_categoria" class="form-label d-block">Categoría:</label>
                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                        <option value="">Seleccione una Categoría</option>
                        <?php 
                        foreach ($categorias_select as $categoria) { 
                            $selected = ($categoria['id'] == $articulo['id_categoria']) ? 'selected' : '';
                            echo "<option value='{$categoria['id']}' {$selected}>{$categoria['nombre']}</option>";
                        } 
                        ?>
                    </select>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="bi bi-save me-2"></i> Guardar
                    </button>
                    <a href="/programacion2/articulos/views/panel_admin.php" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle me-2"></i> Cancelar
                    </a>
                </div>
            </form>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>