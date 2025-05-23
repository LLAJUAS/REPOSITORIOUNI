<?php
include '../../includes/header.php';
include_once '../../includes/conexion.php';

$conexion = obtenerConexion();

// Parámetros desde GET
$categoriaSeleccionada = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$codigoSeleccionado = isset($_GET['codigo']) ? $_GET['codigo'] : null;
$busqueda = isset($_GET['buscador']) ? trim($_GET['buscador']) : null;

// Consulta principal para obtener categorías de dimensión 2
$sql = "SELECT c.id, c.nombre AS categoria, cr.nombre AS criterio
        FROM categorias c
        LEFT JOIN criterio cr ON c.criterio_id = cr.id
        WHERE c.activo = 1 AND c.dimensiones_id = 2";

if ($categoriaSeleccionada) {
    $sql .= " AND c.id = $categoriaSeleccionada";
}

if ($busqueda) {
    $busquedaEscapada = $conexion->real_escape_string($busqueda);
    $sql .= " AND (c.nombre LIKE '%$busquedaEscapada%' OR cr.nombre LIKE '%$busquedaEscapada%')";
}

$resultado = $conexion->query($sql);

// Consulta para llenar el select de categorías
$sql_categorias = "SELECT c.id, c.nombre AS categoria 
                   FROM categorias c 
                   WHERE c.activo = 1 AND c.dimensiones_id = 2";
$resultado_categorias = $conexion->query($sql_categorias);

// Consulta para llenar el select de códigos (filtrado por dimensión 2)
$sql_codigos = "SELECT DISTINCT d.codigo_documento 
                FROM documentos d
                JOIN categorias c ON d.categoria_id = c.id
                WHERE d.codigo_documento IS NOT NULL 
                AND d.codigo_documento != '' 
                AND d.activo = 1
                AND c.dimensiones_id = 2
                ORDER BY d.codigo_documento";
$resultado_codigos = $conexion->query($sql_codigos);

// Colores e íconos para las tarjetas
$colores = ['card-green', 'card-blue', 'card-orange', 'card-pink'];
$iconos = ['fa-microscope', 'fa-vials', 'fa-dna', 'fa-flask'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación Académica</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/categorias.css">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="TITULOYHERRAMIENTAS">
            <div class="titulo-con-linea">
                <h1>DOCUMENTACIÓN ACADÉMICA</h1>
            </div>
            <div class="barra-de-herramientas">
              <!-- Botón para borrar filtros -->
<button type="button" onclick="borrarFiltros()" class="btn-borrar-filtros">
    <i class="fas fa-times-circle"></i> Borrar filtros
</button>

            <!-- Filtro de código -->
                <div class="select-container">
                    <select id="codigo" name="codigo">
                        <option value="" <?= !$codigoSeleccionado ? 'selected' : '' ?> disabled>Selecciona un código</option>
                        <?php 
                        if ($resultado_codigos->num_rows > 0):
                            while ($row = $resultado_codigos->fetch_assoc()):
                                $codigo = $row['codigo_documento'];
                        ?>
                            <option value="<?= htmlspecialchars($codigo) ?>" <?= $codigoSeleccionado == $codigo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($codigo) ?>
                            </option>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <option disabled>No hay códigos existentes</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Filtro de categorías -->
                <div class="select-container">
                    <select id="categoria" name="categoria">
                        <option value="0" <?= !$categoriaSeleccionada ? 'selected' : '' ?>>Todas las categorías</option>
                        <?php while ($cat = $resultado_categorias->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoriaSeleccionada == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['categoria']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Buscador -->
                <input type="text" id="buscador" name="buscador" class="input" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>">
            </div>
        </div>

        <?php
        include '../../includes/cardscategorias.php';
        ?>
    </div>
    <script src="../../assets/js/categorias.js"></script>


</body>
</html>
