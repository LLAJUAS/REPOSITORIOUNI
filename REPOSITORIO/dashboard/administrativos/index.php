<?php
session_start();
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] != 2) {
    header("Location: ../../login.php");
    exit();
}
echo "Bienvenido ADMINISTRATIVO: " . $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Bienvenido, administrativos <?= $_SESSION['usuario_nombre'] ?></h1>
    <a href="../../logout.php">Cerrar sesión</a>
</body>
</html>