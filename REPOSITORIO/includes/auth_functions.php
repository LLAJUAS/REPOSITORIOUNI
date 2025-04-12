<?php
require_once 'conexion.php';
session_start();

function registrarUsuario($nombres, $apellidos, $correo, $contrasena, $rol_id = 2) {
    $conexion = obtenerConexion();
    $hash = password_hash($contrasena, PASSWORD_BCRYPT);

    $stmt = $conexion->prepare("INSERT INTO Usuario (nombres, apellidos, correo, contrasena, rol_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombres, $apellidos, $correo, $hash, $rol_id);
    return $stmt->execute();
}

function iniciarSesion($correo, $contrasena) {
    $conexion = obtenerConexion();
    $stmt = $conexion->prepare("SELECT id, nombres, contrasena, rol_id FROM Usuario WHERE correo = ? AND activo = TRUE");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombres'];
            $_SESSION['usuario_rol'] = $usuario['rol_id'];
            return true;
        }
    }
    return false;
}

function redirigirSegunRol() {
    switch ($_SESSION['usuario_rol']) {
        case 1:
            header("Location: dashboard/admin/index.php");
            break;
        case 2:
            header("Location: dashboard/administrativos/index.php");
            break;
        case 3:
            header("Location: dashboard/visitantes/index.php");
            break;
        default:
            header("Location: login.php");
    }
    exit();
}

function cerrarSesion() {
    session_unset();
    session_destroy();
    header("Location: login.php");
}
?>
