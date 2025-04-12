<?php
include('conexion.php');
$conexion = obtenerConexion();

// Recoger datos del formulario
$id = isset($_POST['id']) ? intval($_POST['id']) : null;
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$formato = $_POST['formato'] ?? '';
$usuario_id = $_POST['usuario_id'] ?? null;
$categoria_id = $_POST['categoria_id'] ?? null;
$codigo_documento = $_POST['codigo_documento'] ?? '';
$link_documento = $_POST['link_documento'] ?? '';
$fecha_resolucion = $_POST['fecha_resolucion'] ?? '';
$activo = isset($_POST['activo']) ? 1 : 0;

if ($id) {
    // Actualizar documento existente
    $query = "UPDATE documentos 
              SET titulo = ?, descripcion = ?, formato = ?, usuario_id = ?, categoria_id = ?, 
                  codigo_documento = ?, link_documento = ?, fecha_resolucion = ?, activo = ?, fecha_modificacion = NOW()
              WHERE id = ?";
    $stmt = $conexion->prepare($query);

    if ($stmt) {
        $stmt->bind_param(
            'sssiisssii',
            $titulo,
            $descripcion,
            $formato,
            $usuario_id,
            $categoria_id,
            $codigo_documento,
            $link_documento,
            $fecha_resolucion,
            $activo,
            $id
        );
    } else {
        die("❌ Error al preparar la consulta de actualización: " . $conexion->error);
    }
} else {
    // Insertar nuevo documento
    $query = "INSERT INTO documentos 
              (titulo, descripcion, formato, usuario_id, categoria_id, codigo_documento, 
               link_documento, fecha_creacion, fecha_resolucion, activo, created_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, NOW())";
    $stmt = $conexion->prepare($query);

    if ($stmt) {
        $stmt->bind_param(
            'sssiisssi',
            $titulo,
            $descripcion,
            $formato,
            $usuario_id,
            $categoria_id,
            $codigo_documento,
            $link_documento,
            $fecha_resolucion,
            $activo
        );
    } else {
        die("❌ Error al preparar la consulta de inserción: " . $conexion->error);
    }
}

// Ejecutar y redirigir o mostrar error
if ($stmt->execute()) {
    header('Location: lista_documentos.php');
    exit();
} else {
    echo "❌ Error al guardar el documento: " . $stmt->error;
}
?>
