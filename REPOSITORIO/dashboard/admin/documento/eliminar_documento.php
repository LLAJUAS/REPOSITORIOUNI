<?php
include('../../../includes/conexion.php');
$conexion = obtenerConexion();

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id) {
    // Desactivar documento
    $query = "UPDATE documentos SET activo = 0, fecha_modificacion = NOW() WHERE id = ?";
    $stmt = $conexion->prepare($query);

    if ($stmt) {
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            // Redirigir de vuelta a la lista
            header('Location: lista_documentos.php');
            exit();
        } else {
            echo "❌ Error al ejecutar la consulta: " . $stmt->error;
        }
    } else {
        echo "❌ Error al preparar la consulta: " . $conexion->error;
    }
} else {
    echo "⚠️ ID de documento no proporcionado o inválido.";
}
?>
