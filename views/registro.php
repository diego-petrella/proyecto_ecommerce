<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}


$errores = $_SESSION['errores_registro'] ?? [];
$datos = $_SESSION['datos_registro'] ?? [];
unset($_SESSION['errores_registro']);
unset($_SESSION['datos_registro']);


require "../includes/header.php"; 
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold text-primary">Crear una Cuenta</h2>
                    
                    <form id="registroForm" action="../controllers/procesar_registro.php" method="POST">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        
                        <?php if (!empty($errores)): ?>
                            <div id="errorMensajeServidor" class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div id="errorMensajeJS" class="alert alert-danger d-none" role="alert">
                           
                        </div>

                        <div id="loadingMensaje" class="alert alert-info d-none" role="alert">
                            <i class="bi bi-arrow-repeat"></i> Procesando...
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Registrarse</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">¿Ya tienes una cuenta? <a href="login.php" class="fw-bold">Inicia Sesión</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registroForm').addEventListener('submit', function(event) {
    const pass1 = document.getElementById('password').value;
    const pass2 = document.getElementById('password_confirm').value;
    const errorDivJS = document.getElementById('errorMensajeJS');

    const errorDivServidor = document.getElementById('errorMensajeServidor');
    if (errorDivServidor) {
        errorDivServidor.classList.add('d-none');
    }
    
    if (pass1 !== pass2) {
        event.preventDefault();
        errorDivJS.textContent = 'Las contraseñas no coinciden.';
        errorDivJS.classList.remove('d-none');
    } else {
        errorDivJS.classList.add('d-none');
        document.getElementById('loadingMensaje').classList.remove('d-none');
    }
});
</script>

<?php
require "../includes/footer.php";
?>
