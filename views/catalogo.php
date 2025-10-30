<?php
//FILTRADO



$base_url = htmlspecialchars($_SERVER["PHP_SELF"]); 
$filtro_marca = $_GET['id_categoria'] ?? '';
$filtro_nombre = isset($_GET["nombre"]) ? $_GET["nombre"] : "";
$pagina_actual = (int)($_GET['p'] ?? 1); 

$titulo_actual = "Todos los Productos";
if (!empty($filtro_marca)) {
    $cat = array_filter($categorias_lista, fn($c) => $c['id'] == $filtro_marca);
    $titulo_actual = $cat ? reset($cat)['nombre'] . " | Productos" : "Categoría Desconocida";
} elseif (!empty($filtro_nombre)) {
    $titulo_actual = "Resultados para: \"" . htmlspecialchars($filtro_nombre) . "\"";
}
?>

<div class="row g-4">
    
    <div class="col-12">
        
        <div class="mb-4">
            <form action="<?php echo $base_url; ?>" method="GET" class="input-group input-group-lg shadow-sm">
                <input type="hidden" name="id_categoria" value="<?php echo htmlspecialchars($filtro_marca); ?>"> 
                
                <input type="text" class="form-control" name="nombre" placeholder="Buscar productos por nombre..." 
                       value="<?php echo htmlspecialchars($filtro_nombre); ?>">
                
                <?php if (!empty($filtro_nombre)): ?>
        <a href="<?php echo $base_url; ?>?id_categoria=<?php echo htmlspecialchars($filtro_marca); ?>" class="btn btn-outline-danger" title="Limpiar búsqueda">
            <i class="bi bi-x-lg"></i>
        </a>
    <?php endif; ?>
    <button class="btn btn-primary" type="submit"><i class="bi bi-search me-2"></i> Buscar</button>
            </form>
        </div>
        
        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded shadow-sm p-0 mb-4">
            <div class="container-fluid">
                
                <span class="navbar-brand text-primary fw-bold ms-2"><i class="bi bi-tags-fill me-2"></i> Categorías:</span>
                
                <div class="collapse navbar-collapse" id="horizontalMenu">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        
                        <?php $is_active_all = empty($filtro_marca) ? 'active' : ''; ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $is_active_all; ?>" 
                               href="<?php echo $base_url; ?>?nombre=<?php echo htmlspecialchars($filtro_nombre); ?>">
                                Todos
                            </a>
                        </li>
                        
                        <?php 
                        foreach ($categorias_lista as $categoria) { 
                            $id = htmlspecialchars($categoria['id']);
                            $nombre = htmlspecialchars($categoria['nombre']);
                            $is_active = ($filtro_marca == $id) ? 'active' : '';
                            $enlace_filtro = "{$base_url}?id_categoria={$id}&nombre=" . htmlspecialchars($filtro_nombre);
                        ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $is_active; ?>" href="<?php echo $enlace_filtro; ?>">
                                    <?php echo $nombre; ?>
                                </a>
                            </li>
                        <?php 
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
        
        <h3 class="mb-4 border-bottom pb-2 text-primary"><?php echo $titulo_actual; ?> (<?php echo $total_articulos; ?> artículos)</h3>
    </div>
    
    <div class="col-12">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            
            <?php if (empty($articulos)): ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center p-4 shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> No se encontraron productos que coincidan con los filtros.
                    </div>
                </div>
            <?php else: ?>
                
                <?php foreach($articulos as $articulo) { 
                    $precio_formateado = number_format($articulo["precio"], 2, ',', '.');
                    $marca_nombre = $articulo["nombre_categoria"] ?? 'Sin Cat.'; 
                    $imagen_src = !empty($articulo["imagen"]) ? "./assets/img/" . htmlspecialchars($articulo["imagen"]) : "https://via.placeholder.com/400x300?text=No+Image";
                ?>
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm border-0">
                            <img src="<?php echo $imagen_src; ?>" class="card-img-top product-img" alt="Producto" style="height: 250px; object-fit: cover;" onerror="this.onerror=null; this.src='assets/img/default.jpg';">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate"><?php echo htmlspecialchars($articulo["nombre"]) ?></h5>
                                <p class="card-text text-muted mb-3 small">Categoría: <?php echo htmlspecialchars($marca_nombre) ?></p>
                                <h3 class="mt-auto text-primary fw-bold">$<?php echo $precio_formateado ?></h3>
                                <a href="views/detalle_articulo.php?id=<?php echo htmlspecialchars($articulo["id"]); ?>" class="btn btn-primary mt-3">
                                    <i class="bi bi-bag-plus me-2"></i> Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                
            <?php endif; ?>
        </div>
    </div>
    </div>
    