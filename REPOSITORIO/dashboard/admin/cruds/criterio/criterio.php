<?php
require '../../documento/conexion.php';
$conn = obtenerConexion();

// CREAR o ACTUALIZAR
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE criterio SET nombre = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO criterio (nombre, activo, created_at) VALUES (?, 1, NOW())");
        $stmt->bind_param("s", $nombre);
    }

    $stmt->execute();
    header("Location: criterio.php?success=true");
    exit;
}

// ACTIVAR / DESACTIVAR
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $accion = $_GET['accion'];
    $id = intval($_GET['id']);
    $activo = ($accion === 'activar') ? 1 : 0;

    $stmt = $conn->prepare("UPDATE criterio SET activo = ? WHERE id = ?");
    $stmt->bind_param("ii", $activo, $id);
    $stmt->execute();

    header("Location: criterio.php?action_success=true");
    exit;
}

// CARGAR CRITERIO PARA EDITAR
$editando = false;
$criterio = ['id' => '', 'nombre' => ''];
if (isset($_GET['editar'])) {
    $editando = true;
    $id = intval($_GET['editar']);
    $res = $conn->prepare("SELECT * FROM criterio WHERE id = ?");
    $res->bind_param("i", $id);
    $res->execute();
    $resultado = $res->get_result();
    if ($resultado->num_rows > 0) {
        $criterio = $resultado->fetch_assoc();
    }
}

// LISTADO
$resultado = $conn->query("SELECT * FROM criterio ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Criterios | UNIFRANZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../../assets/css/criterio.css">
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
                <a href="../categoria/categorias.php"><i class="fas fa-layer-group"></i> Categorías</a>
                <a href="../dimensiones/dimension.php"><i class="fas fa-cube"></i> Dimensiones</a>
                <a href="../tipoarea/tipo_area.php"><i class="fas fa-map-signs"></i> Tipos de Área</a>
                <a href="../criterio/criterio.php" class="active"><i class="fas fa-check-square"></i> Criterios</a>
                <a href="../../documento/lista_documentos.php" class="active"><i class="fas fa-file-alt"></i> Documentos</a>
            </nav>
        </div>
    </header>

    <main>
    <h1 class="site-title">Criterio</h1>
        <!-- Formulario de criterios -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-<?= $editando ? 'edit' : 'plus-circle' ?>"></i>
                <?= $editando ? 'Editar Criterio' : 'Crear Nuevo Criterio' ?>
            </h2>
            <form method="post" action="criterio.php" id="criterioForm">
                <input type="hidden" name="id" value="<?= $criterio['id'] ?>">
                <div class="form-group">
                    <label for="nombre">Nombre del Criterio:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($criterio['nombre']) ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-<?= $editando ? 'save' : 'plus' ?>"></i>
                        <?= $editando ? 'Actualizar Criterio' : 'Guardar Criterio' ?>
                    </button>
                    <?php if ($editando): ?>
                        <a href="criterio.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- Tabla de criterios -->
        <section class="card">
            <h2 class="section-title">
                <i class="fas fa-list"></i> Listado de Criterios
            </h2>
            
            <div class="table-actions">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar criterio...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay criterios registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $fila['id'] ?></td>
                                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                    <td>
                                        <span class="status-badge <?= $fila['activo'] ? 'status-active' : 'status-inactive' ?>">
                                            <?= $fila['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="criterio.php?editar=<?= $fila['id'] ?>" class="action-btn edit" data-tooltip="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <?php if ($fila['activo']): ?>
                                            <a href="criterio.php?accion=desactivar&id=<?= $fila['id'] ?>" class="action-btn deactivate" data-tooltip="Desactivar">
                                                <i class="fas fa-toggle-off"></i> Desactivar
                                            </a>
                                        <?php else: ?>
                                            <a href="criterio.php?accion=activar&id=<?= $fila['id'] ?>" class="action-btn activate" data-tooltip="Activar">
                                                <i class="fas fa-toggle-on"></i> Activar
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
                &copy; <?= date('Y') ?> UNIFRANZ. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script src="../../../../assets/js/unifranz-admin.js"></script>
</body>
</html>