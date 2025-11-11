<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/verificacion_usuario.php';
rolRequerido(1);
if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }
require_once __DIR__ . "/../models/funciones.php";


$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';

//PAGINACIÓN
$items_por_pagina = 10;
$pagina_actual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($pagina_actual - 1) * $items_por_pagina;

//FILTRADO
$filtro_email = $_GET['email'] ?? '';
$filtro_rol = $_GET['rol_id'] ?? ''; 
$filtro_estado = $_GET['estado'] ?? '1'; 

//OBTENER DATOS
$total_usuarios = contarTotalUsuarios($filtro_email, $filtro_rol, $filtro_estado);
$total_paginas = ceil($total_usuarios / $items_por_pagina);

$usuarios = obtenerUsuarios($filtro_email, $filtro_rol, $filtro_estado, $items_por_pagina, $offset);
$roles = obtenerRoles(); 

//Para mantener los filtros en los enlaces de paginación
$filtros_query = http_build_query([
    'email' => $filtro_email,
    'rol_id' => $filtro_rol,
    'estado' => $filtro_estado
]);

// Bandera AJAX
$es_contenido_ajax = true; 
?>

<div class="container-fluid py-3"> 
    
    <h1 class="mb-4">Gestión de Usuarios</h1>
    
   
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Listado de Usuarios (<?php echo $total_usuarios; ?>)</h2>
        
        
        <a href="javascript:void(0);" 
           onclick="loadPage('abm_usuario.php', 'abm_usuario')" 
           class="btn btn-success">
             <i class="bi bi-plus-circle me-2"></i> Crear Nuevo Usuario
        </a>
    </div>

    <!-- FILTROS -->
    <div class="card p-3 mb-4 shadow-sm">
        <form action=""; method="GET" class="row g-3 align-items-end" id="userFilterForm">
            <input type="hidden" name="view" value="usuarios">
            <div class="col-md-4">
                <label for="email" class="form-label">Filtrar por Email:</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Email del usuario..." value="<?php echo htmlspecialchars($filtro_email); ?>">
            </div>
            
            <div class="col-md-3">
                <label for="rol_id" class="form-label">Filtrar por Rol:</label>
                <select class="form-select" id="rol_id" name="rol_id">
                    <option value="">Todos los Roles</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?php echo $rol['id']; ?>" <?php echo ($filtro_rol == $rol['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($rol['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="estado" class="form-label">Filtrar por Estado:</label>
                <select class="form-select" id="estado" name="estado">
                    <option value="1" <?php echo ($filtro_estado == '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo ($filtro_estado == '0') ? 'selected' : ''; ?>>Inactivos</option>
                    <option value="" <?php echo ($filtro_estado == '') ? 'selected' : ''; ?>>Todos</option>
                </select>
            </div>

            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="javascript:void(0);" onclick="loadPage('panel_admin.php?view=usuarios', 'usuarios')" class="btn btn-secondary" title="Limpiar Filtros">
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
                    <th style="width: 25%;">Email</th>
                    <th style="width: 20%;">Nombre</th>
                    <th style="width: 15%;">Rol</th>
                    <th style="width: 10%;" class="text-center">Estado</th>
                    <th style="width: 15%;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" class="text-center p-4 text-muted">No se encontraron usuarios que coincidan con los filtros.</td>
                    </tr>
                <?php else:
                    foreach($usuarios as $usuario) { 
                        $es_inactivo = ($usuario['activo'] == 0);
                        $clase_fila = $es_inactivo ? 'table-light text-muted' : '';
                        $badge_estado = $es_inactivo ? 
                            '<span class="badge bg-danger">Inactivo</span>' : 
                            '<span class="badge bg-success">Activo</span>';
                        
                        $nombre_completo = trim(htmlspecialchars($usuario['nombre'] ?? '') . ' ' . htmlspecialchars($usuario['apellido'] ?? ''));
                        if (empty($nombre_completo)) {
                            $nombre_completo = '<em class="text-muted small">Sin datos</em>';
                        }
                ?>
                        <tr id="fila_<?php echo htmlspecialchars($usuario['id']); ?>" class="<?php echo $clase_fila; ?>">
                            <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($usuario["email"]); ?>
                                <?php if ($usuario['id'] == $_SESSION['usuario_id']): ?>
                                    <span class="badge bg-info ms-2">Tú</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $nombre_completo; ?></td>
                            <td><?php echo htmlspecialchars($usuario["rol_nombre"]); ?></td>
                            <td class="text-center"><?php echo $badge_estado; ?></td>
                            
                            <td class="text-center">
                                <a href="javascript:void(0);" 
                                    onclick="loadPage('abm_usuario.php?id=<?php echo htmlspecialchars($usuario['id']); ?>', 'abm_usuario')"
                                    class="btn btn-sm btn-warning" title="Editar Usuario">
                                     <i class="bi bi-pencil-square"></i>
                                </a>
                                
                                <?php if ($usuario['id'] != $_SESSION['usuario_id']):?>
                                    <?php if ($es_inactivo): ?>
                                        <a class="btn btn-sm btn-info" 
                                            onclick="restaurarUsuario(<?php echo htmlspecialchars($usuario['id']); ?>)"
                                            title="Restaurar Usuario">
                                             <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-sm btn-danger" 
                                            onclick="desactivarUsuario(<?php echo htmlspecialchars($usuario['id']); ?>)"
                                            title="Desactivar Usuario">
                                             <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
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
    <nav aria-label="Paginación de usuarios" class="mt-4">
        <ul class="pagination justify-content-center">
            
            <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" 
                    href="javascript:void(0)"
                    onclick="loadPage('gestion_usuarios.php?p=<?php echo $pagina_actual - 1; ?>&<?php echo $filtros_query; ?>', 'usuarios')">Anterior</a>
            </li>
            
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                    <a class="page-link" 
                        href="javascript:void(0)"
                        onclick="loadPage('gestion_usuarios.php?p=<?php echo $i; ?>&<?php echo $filtros_query; ?>', 'usuarios')"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                <a class="page-link" 
                    href="javascript:void(0)"
                    onclick="loadPage('gestion_usuarios.php?p=<?php echo $pagina_actual + 1; ?>&<?php echo $filtros_query; ?>', 'usuarios')">Siguiente</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>


<script>
const base = "/programacion2/articulos/"

function restaurarUsuario(id) {
    if (!confirm('¿Estás seguro de que deseas reactivar este usuario?')) {
        return;
    }
    let datos = JSON.stringify({ "id" : id });
    
    fetch(base + 'controllers/usuario_restaurar.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body : datos
    })
    .then(response => response.json())
    .then(function(data) { 
        if (data.estado === 'ok') {
            
            loadPage('gestion_usuarios.php', 'usuarios'); 
        } else {
            alert("Error: " + data.mensaje);
        }
    })
    .catch(error => alert("Fallo de comunicación: " + error.message));
}

function desactivarUsuario(id) {
    if (!confirm('¿Estás seguro de que deseas desactivar este usuario? No podrá iniciar sesión.')) {
        return; 
    }
    let datos = JSON.stringify({ "id" : id });
    
    fetch(base + 'controllers/usuario_eliminar.php', { 
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body : datos
    })
    .then(response => response.json())
    .then(function(data) { 
        if (data.estado === 'ok') {
            
            loadPage('gestion_usuarios.php', 'usuarios'); 
        } else {
            alert("Error: " + data.mensaje);
        }
    })
    .catch(error => alert("Fallo de comunicación: " + error.message));
}

function attachFilterListener() {
    const form = document.getElementById('userFilterForm');
    
    if (form) {
        form.removeEventListener('submit', handleFilterSubmit); 
        form.addEventListener('submit', handleFilterSubmit); 
    }
}

function handleFilterSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const queryString = new URLSearchParams(formData).toString();

    loadPage('gestion_usuarios.php?' + queryString, 'usuarios');
}


//Ejecutar el listener con un pequeño retraso
setTimeout(attachFilterListener, 10);
</script>

<?php 
// Si accedimos directamente a la página, cerramos el HTML (este bloque es opcional si solo se incluye)
if (!isset($es_contenido_ajax) || $es_contenido_ajax !== true) { 
    echo '</div> <!-- Cierra el div class="flex-grow-1 p-4" -->';
    echo '</div> <!-- Cierra el div class="d-flex" -->';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
    echo '</body></html>';
}
?>