<?php
include('conexion.php');
$conexion = obtenerConexion();

// Obtener el tipo de área por GET (al crear un nuevo documento)
$tipo_area = $_GET['tipo_area'] ?? null;

// Si estamos editando, obtenemos los datos del documento junto con el tipo de área desde la categoría
$id_documento = $_GET['id'] ?? null;
$documento = null;
$tipo_area_documento = $tipo_area;

if ($id_documento) {
    $queryDocumento = "
        SELECT d.*, c.tipo_area_id 
        FROM documentos d
        JOIN categorias c ON d.categoria_id = c.id
        WHERE d.id = ?
    ";
    $stmt = $conexion->prepare($queryDocumento);
    $stmt->bind_param('i', $id_documento);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $documento = $resultado->fetch_assoc();

    // Usamos el tipo_area_id directamente desde el resultado del join
    $tipo_area_documento = $documento['tipo_area_id'];
}

// Obtener categorías filtradas por tipo de área
$queryCategorias = "SELECT id, nombre FROM categorias WHERE activo = 1 AND tipo_area_id = ?";
$stmtCategorias = $conexion->prepare($queryCategorias);
$stmtCategorias->bind_param('i', $tipo_area_documento);
$stmtCategorias->execute();
$resultCategorias = $stmtCategorias->get_result();

// Obtener usuarios activos
$queryUsuarios = "SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre_completo FROM usuario WHERE activo = 1";
$resultUsuarios = $conexion->query($queryUsuarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($documento['id']) ? 'Editar Documento' : 'Nuevo Documento'; ?> | UNIFRANZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../assets/css/criterio.css">
</head>
<body data-editando="<?= $editando ? 'true' : 'false' ?>" data-edit-id="<?= $editando ? $categoria['id'] : '0' ?>">
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="https://unifranz.edu.bo/wp-content/themes/unifranz-web/public/images/logos/logo-light-min.442cee.svg" alt="UNIFRANZ" class="logo">
                
            </div>
            <nav class="nav-menu">
                <a href="#"><i class="fas fa-home"></i> Inicio</a>
                <a href="../cruds/categoria/categorias.php"><i class="fas fa-layer-group"></i> Categorías</a>
                <a href="../cruds/dimensiones/dimension.php"><i class="fas fa-cube"></i> Dimensiones</a>
                <a href="../cruds/tipoarea/tipo_area.php"><i class="fas fa-map-signs"></i> Tipos de Área</a>
                <a href="../cruds/criterio/criterio.php"><i class="fas fa-check-square"></i> Criterios</a>
                <a href="lista_documentos.php" class="active"><i class="fas fa-file-alt"></i> Documentos</a>
            </nav>
        </div>
    </header>

    <main>
    <h1 class="site-title">Documentos</h1>
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-<?php echo isset($documento['id']) ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo isset($documento['id']) ? 'Editar Documento' : 'Nuevo Documento'; ?>
            </h2>
            
            <form action="guardar_documento.php" method="POST" id="documentoForm">
                <input type="hidden" name="id" value="<?php echo $documento['id'] ?? ''; ?>">
                <input type="hidden" name="tipo_area" value="<?php echo $tipo_area_documento; ?>">
                
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($documento['titulo'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($documento['descripcion'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="formato">Formato:</label>
                    <input type="text" id="formato" name="formato" value="<?php echo htmlspecialchars($documento['formato'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="usuario_id">Usuario:</label>
                    <select id="usuario_id" name="usuario_id" required>
                        <?php while ($usuario = $resultUsuarios->fetch_assoc()): ?>
                            <option value="<?php echo $usuario['id']; ?>" <?php echo isset($documento['usuario_id']) && $documento['usuario_id'] == $usuario['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="categoria_id">Categoría:</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <?php while ($categoria = $resultCategorias->fetch_assoc()): ?>
                            <option value="<?php echo $categoria['id']; ?>" <?php echo isset($documento['categoria_id']) && $documento['categoria_id'] == $categoria['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="codigo_documento">Código Documento:</label>
                    <input type="text" id="codigo_documento" name="codigo_documento" value="<?php echo htmlspecialchars($documento['codigo_documento'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="link_documento">Link Documento:</label>
                    <input type="url" id="link_documento" name="link_documento" value="<?php echo htmlspecialchars($documento['link_documento'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="fecha_resolucion">Fecha Resolución:</label>
                    <input type="date" id="fecha_resolucion" name="fecha_resolucion" value="<?php echo $documento['fecha_resolucion'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group checkbox-group">
                    <label for="activo">Activo:</label>
                    <input type="checkbox" id="activo" name="activo" value="1" <?php echo isset($documento['activo']) && $documento['activo'] == 1 ? 'checked' : ''; ?>>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="lista_documentos.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <img src="https://unifranz.edu.bo/wp-content/themes/unifranz-web/public/images/logos/logo-light-min.442cee.svg" alt="UNIFRANZ" class="footer-logo">
            <div class="footer-links">
                <a href="#">Términos y Condiciones</a>
                <a href="#">Política de Privacidad</a>
                <a href="#">Contacto</a>
            </div>
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> UNIFRANZ. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script src="../../../assets/js/unifranz-admin.js"></script>
</body>
</html>