<?php
include('../../../includes/conexion.php');
$conexion = obtenerConexion();

// Obtener el tipo de área seleccionado (si existe)
$tipo_area = $_GET['tipo_area'] ?? null;

// Consulta base con JOIN a criterio
$query = "SELECT d.*, u.nombres, u.apellidos, c.nombre AS categoria, cr.nombre AS criterio, t.nombre AS tipo_area
          FROM documentos d
          JOIN usuario u ON d.usuario_id = u.id
          JOIN categorias c ON d.categoria_id = c.id
          JOIN criterio cr ON c.criterio_id = cr.id
          JOIN tipo_area t ON c.tipo_area_id = t.id";

// Si hay filtro por tipo de área
if ($tipo_area) {
    $query .= " WHERE t.id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $tipo_area);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conexion->query($query);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos | UNIFRANZ</title>
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
        <!-- Filtros y acciones -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-filter"></i> Filtros y Acciones
            </h2>
            
            <div class="filter-actions">
                <!-- Filtro de tipo de área -->
                <form method="GET" action="lista_documentos.php" class="filter-form">
                    <div class="form-group">
                        <label for="tipo_area">Tipo de Área:</label>
                        <select name="tipo_area" id="tipo_area" onchange="this.form.submit()">
                            <option value="">Todos los tipos</option>
                            <option value="1" <?php echo $tipo_area == 1 ? 'selected' : ''; ?>>Medicina</option>
                            <option value="2" <?php echo $tipo_area == 2 ? 'selected' : ''; ?>>Odontología</option>
                        </select>
                    </div>
                </form>
                
                <div class="action-buttons">
                    <a href="form_documento.php?tipo_area=1" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Medicina
                    </a>
                    <a href="form_documento.php?tipo_area=2" class="btn btn-secondary">
                        <i class="fas fa-plus-circle"></i> Odontología
                    </a>
                </div>
            </div>
        </section>

        <!-- Tabla de documentos -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-list"></i> Listado de Documentos
            </h2>
            
            <div class="table-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar documento...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Criterio</th>
                            <th>Formato</th>
                            <th>Tipo de Área</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows === 0): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay documentos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($documento = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($documento['codigo_documento']); ?></td>
                                    <td><?php echo htmlspecialchars($documento['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($documento['categoria']); ?></td>
                                    <td><?php echo htmlspecialchars($documento['criterio']); ?></td>
                                    <td><?php echo htmlspecialchars($documento['formato']); ?></td>
                                    <td><?php echo htmlspecialchars($documento['tipo_area']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $documento['activo'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $documento['activo'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="form_documento.php?id=<?php echo $documento['id']; ?>" class="action-btn edit" data-tooltip="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($documento['activo'] == 1): ?>
                                            <a href="eliminar_documento.php?id=<?php echo $documento['id']; ?>" class="action-btn deactivate" data-tooltip="Desactivar" onclick="return confirm('¿Seguro que quieres desactivar este documento?')">
                                                <i class="fas fa-toggle-off"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="activar_documento.php?id=<?php echo $documento['id']; ?>" class="action-btn activate" data-tooltip="Activar" onclick="return confirm('¿Seguro que quieres activar este documento?')">
                                                <i class="fas fa-toggle-on"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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