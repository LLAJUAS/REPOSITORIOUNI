<?php
include '../../includes/header.php';
include_once '../../includes/conexion.php';

$conexion = obtenerConexion();

$categoriaId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$codigoSeleccionado = isset($_GET['codigo']) ? $_GET['codigo'] : null;

if (!$categoriaId && !$codigoSeleccionado) {
    die("No se especificó ni una categoría ni un código válido");
}

$nombreCategoria = "Búsqueda de código";
$nombreDimension = "Documentos";

// Obtener nombre de la categoría y dimensión si hay categoría
if ($categoriaId) {
    $sqlCategoria = "SELECT c.nombre, d.nombres as nombre_dimension 
                     FROM categorias c 
                     JOIN dimensiones d ON c.dimensiones_id = d.id 
                     WHERE c.id = ?";
    $stmtCategoria = $conexion->prepare($sqlCategoria);
    $stmtCategoria->bind_param("i", $categoriaId);
    $stmtCategoria->execute();
    $resultadoCategoria = $stmtCategoria->get_result();

    if ($resultadoCategoria->num_rows === 0) {
        die("Categoría no encontrada");
    }

    $categoria = $resultadoCategoria->fetch_assoc();
    $nombreCategoria = htmlspecialchars($categoria['nombre']);
    $nombreDimension = htmlspecialchars($categoria['nombre_dimension']);
}

// Obtener documentos según filtros
if ($codigoSeleccionado) {
    $sqlDocumentos = "SELECT * FROM documentos WHERE codigo_documento = ? AND activo = 1 ORDER BY titulo";
    $stmtDocumentos = $conexion->prepare($sqlDocumentos);
    $stmtDocumentos->bind_param("s", $codigoSeleccionado);
} elseif ($categoriaId) {
    $sqlDocumentos = "SELECT * FROM documentos WHERE categoria_id = ? AND activo = 1 ORDER BY titulo";
    $stmtDocumentos = $conexion->prepare($sqlDocumentos);
    $stmtDocumentos->bind_param("i", $categoriaId);
}
$stmtDocumentos->execute();
$resultadoDocumentos = $stmtDocumentos->get_result();

// Obtener códigos únicos (solo si hay categoría)
if ($categoriaId) {
    $sqlCodigos = "SELECT DISTINCT codigo_documento FROM documentos WHERE categoria_id = ? AND activo = 1 ORDER BY codigo_documento";
    $stmtCodigos = $conexion->prepare($sqlCodigos);
    $stmtCodigos->bind_param("i", $categoriaId);
    $stmtCodigos->execute();
    $resultadoCodigos = $stmtCodigos->get_result();
}
?>

<head>
    <title>Documentos</title>
    <link rel="stylesheet" href="RECURSOS/css/documentos.css">
    <link rel="stylesheet" href="../../assets/css/documentos.css">
    <script src="https://kit.fontawesome.com/7c61ac1c1a.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/categorias.css">
</head>

<div class="container">
    <div class="TITULOYHERRAMIENTAS">
        <div class="titulo-con-linea">
            <h1><?= $nombreDimension ?></h1>
        </div>
        <div class="barra-de-herramientas">
            <button type="button" onclick="borrarFiltros()" class="btn-borrar-filtros">
                <i class="fas fa-times-circle"></i> Borrar filtros
            </button>
            <?php if ($categoriaId): ?>
            <div class="select-container">
                <select id="codigo" name="codigo" onchange="filtrarDocumentos()">
                    <option value="">Todos los códigos</option>
                    <?php while ($codigo = $resultadoCodigos->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($codigo['codigo_documento']) ?>" <?= $codigoSeleccionado == $codigo['codigo_documento'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($codigo['codigo_documento']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <?php endif; ?>
            <input type="text" id="buscador" name="buscador" class="input" placeholder="Buscar..." onkeyup="filtrarDocumentos()">
        </div>
    </div>

    <h2>Documentos: <?= $nombreCategoria ?></h2>

    <?php if ($resultadoDocumentos->num_rows > 0): ?>
        <div class="cards-container">
            <?php
            $colores = ['card-orange', 'card-pink', 'card-blue', 'card-green'];
            $i = 0;
            ?>
            <?php while ($doc = $resultadoDocumentos->fetch_assoc()): ?>
                <?php $colorClass = $colores[$i % count($colores)]; ?>
                <a href="<?= htmlspecialchars($doc['link_documento']) ?>" target="_blank" class="card <?= $colorClass ?>">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($doc['titulo']) ?></h3>
                        <p><?= htmlspecialchars($doc['descripcion']) ?></p>
                        <p><strong>Código:</strong> <span class="codigo"><?= htmlspecialchars($doc['codigo_documento']) ?></span></p>
                        <p><strong>Formato:</strong> <?= htmlspecialchars($doc['formato']) ?></p>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </a>
                <?php $i++; ?>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            No se encontraron documentos.
        </div>
    <?php endif; ?>
</div>

<?php
// Cerrar conexiones
$stmtDocumentos->close();
if (isset($stmtCategoria)) $stmtCategoria->close();
if (isset($stmtCodigos)) $stmtCodigos->close();
$conexion->close();
?>

<script src="../../assets/js/documentos.js"></script>
<script>
function filtrarDocumentos() {
    const codigo = document.getElementById('codigo')?.value || '';
    const texto = document.getElementById('buscador').value.toLowerCase();
    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        const titulo = card.querySelector('h3').textContent.toLowerCase();
        const descripcion = card.querySelector('p').textContent.toLowerCase();
        const cod = card.querySelector('.codigo')?.textContent || '';
        const coincideTexto = titulo.includes(texto) || descripcion.includes(texto);
        const coincideCodigo = !codigo || cod === codigo;
        card.style.display = coincideTexto && coincideCodigo ? 'block' : 'none';
    });
}

function borrarFiltros() {
    document.getElementById('buscador').value = '';
    if (document.getElementById('codigo')) {
        document.getElementById('codigo').selectedIndex = 0;
        window.location.href = 'documentos.php?id=<?= $categoriaId ?>';
    } else {
        filtrarDocumentos();
    }
}
</script>
