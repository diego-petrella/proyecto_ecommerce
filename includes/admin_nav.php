<?php
if (!defined('BASE_URL')) { define('BASE_URL', '/programacion2/articulos/'); }

$admin_email = $_SESSION['usuario_email'] ?? 'Administrador';
$usuario_id = $_SESSION['usuario_id'] ?? 0;

// LEO EL PARAMETRO VIEW DEL LA URL
$current_view = $_GET['view'] ?? 'articulos'; 

// Función auxiliar para saber si el enlace está activo
function isActive($view_name, $current_view) {
    return $view_name === $current_view ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel Admin | Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
                
        .sidebar { 
            width: 280px;
           
            min-height: 800px; 
            z-index: 1000;
        }
        .sidebar .nav-link { color: #dee2e6; cursor: pointer; }
        .sidebar .nav-link.active { background-color: #0d6efd; color: white; }
        
      
        .nav-scroll-area {
            overflow-y: visible;
            margin-top: 10px; 
            padding-right: 5px; 
        }
        
       
        #main-content {
            flex-grow: 1; 
            min-width: 0; 
            width: 100%;
        }
    </style>
    <script>
        const BASE_URL_JS = "<?php echo BASE_URL; ?>";

        function loadPage(pagePath, viewName) {
            const url = BASE_URL_JS + 'views/' + pagePath;
            const mainContent = document.getElementById('main-content');
            
            mainContent.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando contenido...</p></div>';

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar la página: ' + response.status);
                    }
                    return response.text(); 
                })
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const contentContainer = doc.querySelector('.container-fluid'); 
                    
                    if (contentContainer) {
                        //INYECTAR HTML: Se inyecta el contenido HTML visible.
                        mainContent.innerHTML = contentContainer.innerHTML;
                        
                        //EJECUTAR SCRIPTS: Buscamos todos los scripts del fragmento y los re-ejecutamos.
                        const scripts = doc.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            Array.from(oldScript.attributes).forEach(attr => {
                                newScript.setAttribute(attr.name, attr.value);
                            });
                            newScript.textContent = oldScript.textContent;
                            mainContent.appendChild(newScript); 
                        });

                        history.pushState(null, viewName, `?view=${viewName}`);
                        updateSidebarState(viewName);
                        
                    } else {
                        mainContent.innerHTML = `<div class="alert alert-danger">Error: El contenido del archivo (${pagePath}) no está envuelto en un contenedor principal (container-fluid).</div>`;
                    }
                })
                .catch(error => {
                    console.error("Fallo AJAX:", error);
                    mainContent.innerHTML = `<div class="alert alert-danger">Error: Falló la comunicación con el servidor. ${error.message}. Asegúrate de que el archivo exista y no contenga errores PHP.</div>`;
                });
        }
        
        function updateSidebarState(viewName) {
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('onclick') && link.getAttribute('onclick').includes(`'${viewName}'`)) {
                    link.classList.add('active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialView = new URLSearchParams(window.location.search).get('view') || 'articulos';
            updateSidebarState(initialView);
        });

        
        // Esto previene la navegación HTTP tradicional en formularios AJAX.
     
        document.addEventListener('submit', function(e) {
            
            if (e.target.matches('.js-ajax-form')) {
                e.preventDefault(); 
                
                const form = e.target;
                const formData = new FormData(form);
                const url = form.getAttribute('action');
                const alertPlaceholder = document.getElementById('alertPlaceholder');

                // Leer atributos de redirección para saber a dónde ir después de guardar
                const successPage = form.getAttribute('data-success-page') || 'gestion_usuarios.php';
                const successView = form.getAttribute('data-success-view') || 'usuarios';


                if (alertPlaceholder) alertPlaceholder.innerHTML = '';
                
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('El servidor devolvió un error ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    
                    const showAlert = (message, type) => {
                        if (alertPlaceholder) {
                            alertPlaceholder.innerHTML = `
                                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                    ${message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                            alertPlaceholder.scrollIntoView({ behavior: 'smooth' });
                        } else {
                            alert(message);
                        }
                    };

                    if (data.estado === 'ok') {
                        showAlert(data.mensaje, 'success');
                        
                        setTimeout(() => {
                            loadPage(successPage, successView);
                        }, 800); 

                    } else {
                    
                        showAlert(data.mensaje, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Fallo AJAX:', error);
                    alert('Fallo de comunicación con el servidor al procesar el formulario. Detalle: ' + error.message);
                });
            }
        });

        

    </script>
   
</head>
<body>
    
<div class="d-flex">
    <div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark sidebar">
        
        <a href="<?php echo BASE_URL; ?>views/panel_admin.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <i class="bi bi-gear-fill me-2 fs-4 text-warning"></i>
            <span class="fs-4">Panel Admin</span>
        </a>
        <hr>
<div class="mb-3 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center">
                <div class="small">
                    <strong class="d-block text-truncate" title="<?php echo htmlspecialchars($admin_email); ?>"><?php echo htmlspecialchars($admin_email); ?></strong>
                </div>
                <a href="<?php echo BASE_URL; ?>controllers/logout.php" class="btn btn-sm btn-outline-danger" title="Cerrar Sesión">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
        <hr>
        
        <div class="nav-scroll-area flex-grow-1">
            <ul class="nav nav-pills flex-column mb-auto"> 
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('articulos', $current_view); ?>" 
                        onclick="loadPage('articulo1.php', 'articulos')" 
                        href="javascript:void(0);">
                         <i class="bi bi-box-seam me-2"></i> Gestión de Artículos
                    </a>
                </li>
                <li>
                    <a class="nav-link <?php echo isActive('categorias', $current_view); ?>" 
                        onclick="loadPage('categorias.php', 'categorias')" 
                        href="javascript:void(0);">
                         <i class="bi bi-tags me-2"></i> Gestión de Categorías
                    </a>
                </li>
                <li>
                    <a class="nav-link <?php echo isActive('pedidos', $current_view); ?>" 
                        onclick="loadPage('gestion_pedidos.php', 'pedidos')" 
                        href="javascript:void(0);">
                         <i class="bi bi-receipt-cutoff me-2"></i> Gestión de Pedidos
                    </a>
                </li>
                <li>
                    <a class="nav-link <?php echo isActive('usuarios', $current_view); ?>" 
                        onclick="loadPage('gestion_usuarios.php', 'usuarios')" 
                        href="javascript:void(0);">
                         <i class="bi bi-people me-2"></i> Gestión de Usuarios
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="flex-grow-1 p-4" id="main-content">