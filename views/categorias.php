<?php
session_start();

require '../controllers/verificacion_usuario.php';
rolRequerido(1);

require "../models/funciones.php";


$categorias = obtenerCategorias(); 

$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Categorías | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <header class="bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">
                <i class="bi bi-tags-fill me-2"></i> Gestión de Categorías
                <a href="../index.php" class="btn btn-outline-info btn-sm ms-3" title="Ir a la página principal">
                    <i class="bi bi-house-door-fill"></i> Home
                </a>
                <a href="panel_admin.php" class="btn btn-outline-light btn-sm ms-2" title="Volver a Artículos">
                    <i class="bi bi-box-seam"></i> Artículos
                </a>
            </h1>
            <div class="d-flex align-items-center">
                <span class="text-white-50 me-3 small">
                    <i class="bi bi-person-fill"></i> Sesión: <?php echo htmlspecialchars($admin_email); ?>
                </span>
                <a href="../controllers/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <div class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Listado de Categorías (<?php echo count($categorias); ?>)</h2>
            
            <a href="abm_categoria.php" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i> Crear Nueva Categoría
            </a>
        </div>

        <div class="table-responsive shadow-sm">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 60%;">Nombre</th>
                        <th style="width: 15%;" class="text-center">Editar</th>
                        <th style="width: 15%;" class="text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (empty($categorias)): 
                    ?>
                        <tr>
                            <td colspan="4" class="text-center p-4 text-muted">No hay categorías registradas.</td>
                        </tr>
                    <?php 
                    else:
                        foreach($categorias as $categoria) { 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($categoria["id"]) ?></td>
                            <td><?php echo htmlspecialchars($categoria["nombre"]) ?></td>
                            
                            <td class="text-center">
                                <a href="abm_categoria.php?id=<?php echo htmlspecialchars($categoria["id"]) ?>" class="btn btn-sm btn-warning" title="Editar Categoría">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                            
                            <td class="text-center">
                                <a href="../controllers/categoria_eliminar.php?id=<?php echo htmlspecialchars($categoria["id"]) ?>" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="return confirm('ADVERTENCIA: ¿Seguro que deseas eliminar esta categoría?')" 
                                    title="Eliminar Categoría">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php 
                        } 
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>