<?php
require 'includes/auth_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y validar entradas
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'] ?? '';
    
    // Validaciones básicas
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor ingresa un correo electrónico válido";
    } elseif (empty($contrasena)) {
        $error = "La contraseña no puede estar vacía";
    } elseif (strlen($contrasena) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } else {
        // Intento de inicio de sesión
        if (iniciarSesion($correo, $contrasena)) {
            redirigirSegunRol();
        } else {
            $error = "Correo o contraseña incorrectos";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Unifranz</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .error-message {
            color: #ff4d4d;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .password-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
            <div class="success-message" style="color: #00cc66; margin-bottom: 15px;">
                ¡Registro exitoso! Por favor inicia sesión.
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form" id="loginForm">
            <div class="form-group">
                <input type="email" id="correo" name="correo" 
                       placeholder="Ingresa tu correo" 
                       required
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
            </div>
            
            <div class="form-group">
                <input type="password" id="contrasena" name="contrasena" 
                       placeholder="Ingresa tu contraseña" 
                       required
                       minlength="8">
                <div class="password-hint">Mínimo 8 caracteres</div>
            </div>
            
            <button type="submit" class="login-button">Ingresar</button>
            <hr>
            <div class="register-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
            </div>
        </form>
    </div>

    <script>
        // Validación básica del formulario antes de enviar
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const password = document.getElementById('contrasena').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>