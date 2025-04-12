<?php 
include '../../includes/header.php';
include_once '../../includes/conexion.php';
$conexion = obtenerConexion();

$dimensiones = [];
$resultado = $conexion->query("SELECT nombres FROM dimensiones WHERE activo = 1 LIMIT 4");
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $dimensiones[] = $fila['nombres'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dimensiones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/dimensiones.css">
    
</head>
<body>
    <div class="container">
        <div class="titulo-con-linea">
            <h1>DIMENSIONES</h1>
        </div>
        <div class="contc">
        <div class="cards-container">
           <!-- Card 1 -->
<a href="../categorias/doclegal.php" class="card-link">
    <div class="card card-orange">
        <div>
            <h2><?= htmlspecialchars($dimensiones[0] ?? 'Documentación Legal') ?></h2>
            <p>NORMATIVAS, CONTRATOS, POLÍTICAS Y DOCUMENTOS OFICIALES INSTITUCIONALES.</p>
        </div>
        <div class="btn-animado">
            <span class="btn-texto">EXPLORAR</span>
            <span class="btn-icono">↗</span>
        </div>

    </div>
</a>

<!-- Card 2 -->
<a href="../categorias/docacademica.php" class="card-link">
    <div class="card card-pink">
        <div>
            <h2><?= htmlspecialchars($dimensiones[1] ?? 'Documentación Académica') ?></h2>
            <p>ARCHIVOS RELACIONADOS CON INVESTIGACIONES, PLANES DE ESTUDIO Y PUBLICACIONES ACADÉMICAS.</p>
        </div>
        <div class="btn-animado">
            <span class="btn-texto">EXPLORAR</span>
            <span class="btn-icono">↗</span>
        </div>
    </div>
</a>

<!-- Card 3 -->
<a href="../categorias/comunidad.php" class="card-link">
    <div class="card card-blue">
        <div>
            <h2><?= htmlspecialchars($dimensiones[2] ?? 'Comunidad Estudiantil') ?></h2>
            <p>REGLAMENTOS, ACTIVIDADES, EVENTOS Y PARTICIPACIÓN ESTUDIANTIL.</p>
        </div>
        <div class="btn-animado">
            <span class="btn-texto">EXPLORAR</span>
            <span class="btn-icono">↗</span>
        </div>
    </div>
</a>

<!-- Card 4 -->
<a href="../categorias/infrestructura.php" class="card-link">
    <div class="card card-green">
        <div>
            <h2><?= htmlspecialchars($dimensiones[3] ?? 'Infraestructura') ?></h2>
            <p>PLANOS, REPORTES TÉCNICOS, PROYECTOS Y DOCUMENTACIÓN RELACIONADA A ESPACIOS FÍSICOS Y TECNOLÓGICOS.</p>
        </div>
        <div class="btn-animado">
            <span class="btn-texto">EXPLORAR</span>
            <span class="btn-icono">↗</span>
        </div>
    </div>
</a>

        </div>
        </div>
    </div>
</body>
</html>
