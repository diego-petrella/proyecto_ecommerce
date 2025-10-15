<?php
session_start();
require '../controllers/verificacion_usuario.php';

rolRequerido(1);

require "../models/funciones.php";

// 1. OBTENER ID y ACCIÓN
$id = isset($_GET["id"]) ? $_GET["id"] : 0;
$action = $_GET['action'] ?? null;
$token = md5(session_id());

// Definimos la ruta de redirección
$panel_admin = '/programacion2/articulos/views/panel_admin.php';

// 2. VALIDACIÓN DE REDIRECCIÓN (Solo se aplica a CREATE)
// Si la acción es CREAR, validamos el token. 
// Si la acción es EDITAR (solo viene con ID), permitimos el paso.
if ($action === 'create') {
    if ($_GET['token'] !== $token) {
        header("Location: " . $panel_admin . "?error=token");
        exit;
    }
} elseif ($id == 0 && $action !== 'create') {
    // Si no hay ID y no se especificó 'create', redirigimos.
    header("Location: " . $panel_admin);
    exit;
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol_id'] != 1) {
    // Limpiamos la sesión si es un intento de acceso no autorizado
    session_unset();
    session_destroy();
    
    // Redirigimos al formulario de login
    header("Location: ../views/login.php");
    exit;
}
// Asumimos que también necesitarás cargar la lista de marcas para el <select>
$marcas = obtenerTodasLasMarcas(); 

// 3. LÓGICA DE CARGA DE DATOS
if ($id != 0) {
    // Lo vamos a buscar a la base de datos (MODO EDICIÓN)
    $articulo = buscarPorId($id);
    
    // Si la búsqueda falla, redirigimos
    if ($articulo === false) {
        header("Location: " . $panel_admin); // Redirección corregida
        exit; 
    }
    $titulo = "Modificar Artículo";
} else {
    // Si es nuevo (MODO CREACIÓN), creamos un articulo vacio
    $articulo = [
        "nombre" => "",
        "precio" => "", 
        "id_categoria" => "",
        "imagen" => "",
        "descripcion_corta" => "",
        "stock" => "", // Agregamos el campo de stock
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
                    <label for="id_categoria" class="form-label d-block">Categoria:</label>
                    <div class="input-group">
                        <select class="form-select" id="id_categoria" name="id_categoria" required>
                            <option value="">Seleccione una Categoria</option>
                            <?php 
                            foreach ($marcas as $marca) { 
                                $selected = ($marca['id'] == $articulo['id_categoria']) ? 'selected' : '';
                                echo "<option value='{$marca['id']}' {$selected}>{$marca['nombre']}</option>";
                            } 
                            ?>
                        </select>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#nuevaMarcaModal" title="Agregar nueva marca">
                            <i class="bi bi-plus-circle"></i>
                        </button>
                    </div>
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
    
    <div class="modal fade" id="nuevaMarcaModal" tabindex="-1" aria-labelledby="nuevaMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevaMarcaModalLabel">Agregar Nueva Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaMarca">
                        <div class="mb-3">
                            <label for="nombre_marca" class="form-label">Nombre de la Marca</label>
                            <input type="text" class="form-control" id="nombre_marca" name="nombre_marca" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarMarca">Guardar Marca</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnGuardarMarca').addEventListener('click', function() {
            var nombreMarca = document.getElementById('nombre_marca').value;

            if (nombreMarca) {
                // Aquí deberías usar AJAX para enviar el nombre de la marca a un nuevo controlador
                // (por ejemplo, 'guardar_marca.php') y luego actualizar el select.
                // Como este código es solo para el front-end, simulo la acción.
                
                var selectMarca = document.getElementById('id_categoria');
                var nuevaOpcion = document.createElement('option');
                nuevaOpcion.value = 'nuevo_id_generado'; // Este ID debe ser real
                nuevaOpcion.text = nombreMarca;
                nuevaOpcion.selected = true;
                selectMarca.appendChild(nuevaOpcion);

                // Cierra el modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('nuevaMarcaModal'));
                modal.hide();
                document.getElementById('nombre_marca').value = ''; // Limpia el input
            }
        });
    </script>
</body>
</html>