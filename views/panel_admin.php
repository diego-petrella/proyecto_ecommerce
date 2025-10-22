<?php
session_start();

require '../controllers/verificacion_usuario.php';

rolRequerido(1);

require "../models/funciones.php";

//PAGINACIÓN ---
$items_por_pagina = 8;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($pagina_actual - 1) * $items_por_pagina;

//FILTRADO
$filtro_nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : "";
$filtro_marca = isset($_GET["id_categoria"]) ? $_GET["id_categoria"] : "";

//OBTENER DATOS
$total_articulos = contarTotalArticulos($filtro_nombre, $filtro_marca); 
$total_paginas = ceil($total_articulos / $items_por_pagina);


$articulos = obtenerArticulos($filtro_nombre, $filtro_marca, $items_por_pagina, $offset); 
$categorias = obtenerCategorias(); 

$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';

//Para mantener los filtros en los enlaces de paginación
$filtros_query = http_build_query([
    'nombre' => $filtro_nombre,
    'id_categoria' => $filtro_marca
]);
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
            <h1 class="h4 m-0">
                <i class="bi bi-box-seam me-2"></i> Panel de Artículos
                <a href="../index.php" class="btn btn-outline-info btn-sm ms-3" title="Ir a la página principal">
                    <i class="bi bi-house-door-fill"></i> Home
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
            <h2 class="mb-0">Listado de Artículos (<?php echo $total_articulos; ?>)</h2>
            
            <div>
                <a href="../views/gestion_pedidos.php" class="btn btn-warning me-3">
                    <i class="bi bi-receipt-cutoff me-2"></i> Gestión de Pedidos
                </a>
                
                <a href="../views/categorias.php" class="btn btn-info me-3">
                    <i class="bi bi-tags-fill me-2"></i> Gestión de Categorías
                </a>
                
                <a href="../views/articulo1.php?action=nuevo&token=<?php echo md5(session_id()); ?>" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i> Agregar Nuevo
                </a>
            </div>
            </div>

        <div class="card p-3 mb-4 shadow-sm">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-5">
                    <label for="nombre" class="form-label visually-hidden">Buscar Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Buscar por Nombre" 
                        value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="id_categoria" class="form-label visually-hidden">Categoría</label>
                    <select class="form-select" id="id_categoria" name="id_categoria">
                        <option value="">Todas las Categorías</option>
                        <?php 
                        foreach ($categorias as $categoria) { 
                            $selected = ($filtro_marca == $categoria['id']) ? 'selected' : '';
                            echo "<option value='{$categoria['id']}' {$selected}>" . htmlspecialchars($categoria['nombre']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex">
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
                        <tr id="fila_<?php echo htmlspecialchars($articulo['id']); ?>">
                            <td><?php echo htmlspecialchars($articulo["id"]) ?></td>
                            <td><img src="<?php echo $imagen_src; ?>" alt="Imagen del producto" style="max-width: 80px; height: auto;"></td>
                            <td><?php echo htmlspecialchars($articulo["nombre"]) ?></td>
                            <td><?php echo htmlspecialchars($articulo["stock"] ?? 'N/A') ?></td>
                            <td>$<?php echo $precio_formateado ?></td>
                            <td><?php echo htmlspecialchars($articulo["nombre_categoria"] ?? 'N/A') ?></td>
                            
                            <td class="text-center">
                                <a href="../views/articulo1.php?id=<?php echo htmlspecialchars($articulo["id"]) ?>" class="btn btn-sm btn-warning" title="Modificar Artículo">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                            
                            <td   class="text-center">
                                
                                <a class="btn btn-sm btn-danger" 
                                onclick="eliminar(<?php echo htmlspecialchars($articulo['id']); ?>)"
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

        <?php if ($total_paginas > 1): ?>
        <nav aria-label="Paginación de artículos" class="mt-4">
            <ul class="pagination justify-content-center">
                
                <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?p=<?php echo $pagina_actual - 1; ?>&<?php echo $filtros_query; ?>">Anterior</a>
                </li>
                
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?p=<?php echo $i; ?>&<?php echo $filtros_query; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?p=<?php echo $pagina_actual + 1; ?>&<?php echo $filtros_query; ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function eliminar(id) {
        let datos = JSON.stringify({
        "id" : id
        });
        
        fetch("../controllers/articulo_eliminar.php", {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json'
        },
        body : datos})
        .then(response => response.json()).then(function(data) { 
        
        if (data.estado === 'ok') {
            
            
            let idBuscado = "fila_" + id; 
            let filaAEliminar = document.getElementById(idBuscado);
            
            if (filaAEliminar) {
                filaAEliminar.remove(); 
                alert(data.mensaje || "Artículo eliminado con éxito.");
            } else {
                
                alert("Éxito en BD, pero la vista no se pudo actualizar.");
            }
            
        } else {
            
            alert("Error de BD: " + data.mensaje);
        }
    })
    .catch(error => alert("Fallo de comunicación AJAX: " + error.message));
}
    </script>
</body>
</html>