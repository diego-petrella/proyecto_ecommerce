<?php
session_start();

require '../controllers/verificacion_usuario.php';
rolRequerido(1);

require "../models/funciones.php";


$id = isset($_GET["id"]) ? $_GET["id"] : 0;


$categoria = [
    "id" => 0,
    "nombre" => ""
];
$titulo = "Crear Nueva Categoría";

if ($id != 0) {
    
    $datos = buscarCategoriaPorId($id);
    
    if ($datos) {
        $categoria = $datos;
        $titulo = "Modificar Categoría ID: " . $id;
    } else {
        
        header("Location: categorias.php?error=no_existe");
        exit;
    }
}

$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';
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
            <h1 class="h4 m-0"><i class="bi bi-tags-fill me-2"></i> Gestión de Categorías</h1>
        </div>
    </header>

    <div class="container my-5">
        
        <div class="card p-4 shadow-lg mx-auto" style="max-width: 500px;">
            
            <h2 class="text-center mb-4 border-bottom pb-3">
                <i class="bi bi-<?php echo ($id == 0) ? "plus-circle" : "pencil-square" ?> me-2"></i> 
                <?php echo $titulo ?>
            </h2>
            
            <form method="post" action="../controllers/categoria_guardar.php" autocomplete="off">

                <?php if ($id != 0) { ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($categoria["id"]) ?>" />
                <?php } ?>

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Categoría</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" 
                        value="<?php echo htmlspecialchars($categoria["nombre"]) ?>" required>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="bi bi-save me-2"></i> Guardar
                    </button>
                    <a href="categorias.php" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle me-2"></i> Cancelar
                    </a>
                </div>
            </form>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>