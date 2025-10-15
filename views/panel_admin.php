<?php
session_start();

require '../controllers/verificacion_usuario.php';

rolRequerido(1);

require "../models/funciones.php";


// 1. LÓGICA DE FILTRADO
// Se capturan los filtros de la URL (GET) si existen.
$filtro_nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : "";
$filtro_precio_min = isset($_GET["precio_min"]) ? $_GET["precio_min"] : null;
$filtro_marca = isset($_GET["id_categoria"]) ? $_GET["id_categoria"] : "";

// 2. OBTENER ARTÍCULOS CON FILTROS
// Debes actualizar tu función obtenerArticulos() para que acepte estos parámetros.
// Por ahora, pasamos las variables; si están vacías, la función puede ignorarlas.
$articulos = obtenerArticulos($filtro_nombre, $filtro_marca); 

// (Opcional: Obtener una lista de categorías para el <select> si es necesario)
// $categorias = obtenerCategorias();
$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Artículos | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <header class="bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0"><i class="bi bi-box-seam me-2"></i> Panel de Artículos</h1>
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
            <h2 class="mb-0">Listado de Artículos (<?php echo count($articulos); ?>)</h2>
            
            <a href="../views/articulo1.php?action=create&token=<?php echo md5(session_id()); ?>" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i> Agregar Nuevo
            </a>
        </div>

        <div class="card p-3 mb-4 shadow-sm">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-4">
                    <label for="nombre" class="form-label visually-hidden">Buscar Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Buscar por Nombre" 
                        value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="precio_min" class="form-label visually-hidden">Otro Filtro</label>
                    <input type="number" step="0.01" class="form-control" id="precio_min" name="precio_min" placeholder="Precio Mínimo"
                        value="<?php echo htmlspecialchars($filtro_precio_min); ?>">
                </div>
                
                <div class="col-md-3">
                    <label for="id_categoria" class="form-label visually-hidden">Categoría</label>
                    <select class="form-select" id="id_categoria" name="id_categoria">
                        <option value="">Todas las Categorías</option>
                        <option value="1" <?php echo ($filtro_marca == '1' ? 'selected' : ''); ?>>Samsung</option>
                        <option value="2" <?php echo ($filtro_marca == '2' ? 'selected' : ''); ?>>Apple</option>
                        <option value="3" <?php echo ($filtro_marca == '3' ? 'selected' : ''); ?>>Motorola</option>
                        <option value="4" <?php echo ($filtro_marca == '4' ? 'selected' : ''); ?>>Xiaomi</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-secondary" title="Limpiar Filtros">
                        <i class="bi bi-eraser"></i>
                    </a>
                </div>
            </form>
        </div>
        
        <div class="table-responsive shadow-sm">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 10%;">Imagen</th>
                        <th style="width: 35%;">Nombre</th>
                        <th style="width: 10%;">Stock</th>
                        <th style="width: 15%;">Precio</th>
                        <th style="width: 15%;">Categoria</th>
                        <th style="width: 10%;" class="text-center">Modificar</th>
                        <th style="width: 10%;" class="text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (empty($articulos)): 
                    ?>
                        <tr>
                            <td colspan="8" class="text-center p-4 text-muted">No se encontraron artículos que coincidan con los filtros.</td>
                        </tr>
                    <?php 
                    else:
                        foreach($articulos as $articulo) { 
                            $precio_formateado = number_format($articulo["precio"], 2, ',', '.');
                            $imagen_src = !empty($articulo["imagen"]) ? "../assets/img/" . htmlspecialchars($articulo["imagen"]) : "https://via.placeholder.com/80";
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($articulo["id"]) ?></td>
                            <td><img src="<?php echo $imagen_src; ?>" alt="Imagen del producto" style="max-width: 80px; height: auto;"></td>
                            <td><?php echo htmlspecialchars($articulo["nombre"]) ?></td>
                            <td><?php echo htmlspecialchars($articulo["stock"] ?? 'N/A') ?></td>
                            <td>$<?php echo $precio_formateado ?></td>
                            <td><?php echo htmlspecialchars($articulo["nombre_categoria"]) ?></td>
                            
                            <td class="text-center">
                                <a href="../views/articulo1.php?id=<?php echo htmlspecialchars($articulo["id"]) ?>" class="btn btn-sm btn-warning" title="Modificar Artículo">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                            
                            <td class="text-center">
                                <a href="../controllers/articulo_eliminar.php?id=<?php echo htmlspecialchars($articulo["id"]) ?>" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este artículo?')" 
                                    title="Eliminar Artículo">
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