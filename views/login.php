<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    
    
    $rol_id = $_SESSION['usuario_rol_id'] ?? 2; 

    if ($rol_id == 1) {
        //1= Administrador
        header("Location: ./panel_admin.php"); 
        exit;
    } else {
        // 2= Cliente Final
        header("Location:/programacion2/articulos/index.php"); 
        exit;
    }
}


$error_message = $_SESSION['login_error'] ?? null; 


unset($_SESSION['login_error']); 

$url_destino = '';
if (isset($_GET['url_destino'])) {
    // Usamos htmlspecialchars por seguridad al ponerlo en el 'value' del input
    // urldecode es importante por si la URL viene codificada
    $url_destino = htmlspecialchars(urldecode($_GET['url_destino']));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .login-container {
            min-height: 80vh; 
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

    <header class="navbar navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-muted" href="../index.php">
                <i class="bi bi-arrow-left me-2"></i> Volver a la Tienda
            </a>
        </div>
    </header>
    <div class="container login-container">
        <div class="row w-100 justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card shadow-lg p-4">
                    <h2 class="card-title text-center mb-4">Acceso de Usuario</h2>

                    <?php if ($error_message) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php } ?>
                    
                    <form action="../controllers/login.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <input type="hidden" name="redirect_url" value="<?php echo $url_destino; ?>">

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Iniciar Sesión
                        </button>
                        
                    </form>

                    <div class="mt-3 text-center">
                        <small class="text-muted">¿No tienes cuenta? <a href="#">Regístrate aquí</a></small>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>