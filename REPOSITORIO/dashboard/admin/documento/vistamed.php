<?php
include('../../../includes/conexion.php');
$conexion = obtenerConexion();

$busqueda = $_GET['busqueda'] ?? '';

// Consulta para sugerencias y resultados
$query = "SELECT d.*, u.nombres, u.apellidos, c.nombre AS categoria, cri.nombre AS criterio, t.nombre AS tipo_area
          FROM documentos d
          JOIN usuario u ON d.usuario_id = u.id
          JOIN categorias c ON d.categoria_id = c.id
          JOIN tipo_area t ON c.tipo_area_id = t.id
          LEFT JOIN criterio cri ON c.criterio_id = cri.id
          WHERE d.codigo_documento LIKE ? AND d.codigo_documento LIKE '%A%'";  // Filtra los documentos donde 'codigo_documento' contiene 'A'

if (!empty($busqueda)) {
    $likeBusqueda = '%' . $busqueda . '%';
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $likeBusqueda);  // Usamos "s" para string
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Si no hay búsqueda, simplemente ejecutamos la consulta sin filtro
    $stmt = $conexion->prepare($query);
    $likeBusqueda = '%';  // Buscar todos los documentos si no hay búsqueda
    $stmt->bind_param("s", $likeBusqueda);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Para las sugerencias
$titulosQuery = "SELECT titulo FROM documentos WHERE codigo_documento LIKE '%A%'";  // Filtra solo los títulos con código que contiene 'A'
$titulosResult = $conexion->query($titulosQuery);
$titulos = [];
while ($row = $titulosResult->fetch_assoc()) {
    $titulos[] = $row['titulo'];
}
?>

<h1>MEDICINA</h1>

<!-- Buscador -->
<form method="GET" action="vistamed.php" autocomplete="off">
    <input type="text" id="busqueda" name="busqueda" placeholder="Buscar por título..." oninput="mostrarSugerencias(this.value)" value="<?php echo htmlspecialchars($busqueda); ?>">
    <div id="sugerencias" class="sugerencias"></div>
</form>

<!-- Mostrar documentos en tarjetas -->
<div class="tarjetas-container">
    <?php while ($documento = $result->fetch_assoc()): ?>
        <div class="tarjeta">
            <h3><?php echo $documento['codigo_documento']; ?></h3>
            <p><strong>Título:</strong> <?php echo $documento['titulo']; ?></p>
            <p><strong>Enlace:</strong> <a href="<?php echo $documento['link_documento']; ?>" target="_blank">Ir al Enlace</a></p>
            <p><strong>Categoría:</strong> <?php echo $documento['categoria']; ?></p>
            <p><strong>Criterio:</strong> <?php echo $documento['criterio']; ?></p>
            <p><strong>Tipo de Área:</strong> <?php echo $documento['tipo_area']; ?></p>
        </div>
    <?php endwhile; ?>
</div>

<style>
    .tarjetas-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
        margin-top: 20px;
    }

    .tarjeta {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        width: 30%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .tarjeta:hover {
        transform: scale(1.05);
    }

    .tarjeta h3 {
        color: #007bff;
    }

    .tarjeta a {
        color: #28a745;
        text-decoration: none;
        font-weight: bold;
    }

    .tarjeta a:hover {
        text-decoration: underline;
    }

    input[type="text"] {
        padding: 8px;
        width: 50%;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .sugerencias {
        background: white;
        border: 1px solid #ccc;
        width: 50%;
        max-height: 200px;
        overflow-y: auto;
        position: absolute;
        z-index: 1000;
        display: none;
    }

    .sugerencias div {
        padding: 8px;
        cursor: pointer;
    }

    .sugerencias div:hover {
        background-color: #f0f0f0;
    }

    @media (max-width: 768px) {
        .tarjeta {
            width: 45%;
        }
    }

    @media (max-width: 480px) {
        .tarjeta {
            width: 100%;
        }

        input[type="text"] {
            width: 100%;
        }

        .sugerencias {
            width: 100%;
        }
    }
</style>

<script>
    const sugerencias = <?php echo json_encode($titulos); ?>;

    function mostrarSugerencias(valor) {
        const contenedor = document.getElementById("sugerencias");
        contenedor.innerHTML = "";
        if (valor.length === 0) {
            contenedor.style.display = "none";
            return;
        }

        const coincidencias = sugerencias.filter(titulo =>
            titulo.toLowerCase().includes(valor.toLowerCase())
        );

        if (coincidencias.length === 0) {
            contenedor.style.display = "none";
            return;
        }

        coincidencias.forEach(titulo => {
            const div = document.createElement("div");
            div.textContent = titulo;
            div.onclick = () => {
                document.getElementById("busqueda").value = titulo;
                contenedor.style.display = "none";
                document.forms[0].submit(); // Buscar automáticamente al hacer clic
            };
            contenedor.appendChild(div);
        });

        contenedor.style.display = "block";
    }
</script>
