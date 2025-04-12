<?php
require 'includes/auth_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Validación de contraseña
    if (strlen($contrasena) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } elseif (!preg_match('/[A-Z]/', $contrasena) || !preg_match('/[0-9]/', $contrasena) || !preg_match('/[^a-zA-Z0-9]/', $contrasena)) {
        $error = "La contraseña debe contener al menos una mayúscula, un número y un carácter especial";
    } else {
        if (registrarUsuario($nombres, $apellidos, $correo, $contrasena)) {
            header("Location: login.php?registro=exitoso");
            exit();
        } else {
            $error = "El correo electrónico ya está registrado. Por favor, utiliza otro.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Unifranz</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .password-strength {
            height: 5px;
            width: 100%;
            margin-top: 5px;
            background: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background 0.3s ease;
        }
        
        .strength-weak {
            background: #ff4d4d;
            width: 33%;
        }
        
        .strength-medium {
            background: #ffcc00;
            width: 66%;
        }
        
        .strength-strong {
            background: #00cc66;
            width: 100%;
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
        <h1>Registro de Usuario</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        
        <form method="POST" class="login-form" id="registroForm">
            <div class="form-group">
                <input type="text" id="nombres" name="nombres" placeholder="Ingresa tus nombres" required>
            </div>
            
            <div class="form-group">
                <input type="text" id="apellidos" name="apellidos" placeholder="Ingresa tus apellidos" required>
            </div>
            
            <div class="form-group">
                <input type="email" id="correo" name="correo" placeholder="Ingresa tu correo" required>
            </div>
            
            <div class="form-group">
                <input type="password" id="contrasena" name="contrasena" placeholder="Crea una contraseña" 
                       required minlength="8" oninput="checkPasswordStrength(this.value)">
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <div class="password-hint">
                    La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, números y caracteres especiales.
                </div>
            </div>
            
            <button type="submit" class="login-button">Registrarse</button>
            <hr>
            <div class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        </form>
    </div>

    <script>
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrengthBar');
            let strength = 0;
            
            // Reset
            strengthBar.className = 'password-strength-bar';
            
            // Longitud mínima
            if (password.length >= 8) strength += 1;
            
            // Contiene mayúsculas
            if (/[A-Z]/.test(password)) strength += 1;
            
            // Contiene números
            if (/[0-9]/.test(password)) strength += 1;
            
            // Contiene caracteres especiales
            if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
            
            // Aplicar clases según fuerza
            if (password.length > 0) {
                if (strength <= 1) {
                    strengthBar.classList.add('strength-weak');
                } else if (strength <= 3) {
                    strengthBar.classList.add('strength-medium');
                } else {
                    strengthBar.classList.add('strength-strong');
                }
            }
        }
        
        // Validación adicional antes de enviar
        document.getElementById('registroForm').addEventListener('submit', function(e) {
            const password = document.getElementById('contrasena').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                return false;
            }
            
            if (!/[A-Z]/.test(password) || !/[0-9]/.test(password) || !/[^a-zA-Z0-9]/.test(password)) {
                e.preventDefault();
                alert('La contraseña debe contener al menos una mayúscula, un número y un carácter especial');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>