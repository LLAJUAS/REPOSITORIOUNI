<div class="cards-container">
    <?php
    $i = 0;
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()):
            $colorClass = $colores[$i % count($colores)];
            $icono = $iconos[$i % count($iconos)];
            $criterio = !empty($fila['criterio']) ? $fila['criterio'] : 'Sin criterio asignado';
            ?>
            <a href="../documentos/documentos.php?id=<?= $fila['id'] ?>" class="card <?= $colorClass ?>">
                <div class="card-icon">
                    <i class="fas <?= $icono ?>"></i>
                </div>
                <div class="card-content">
                    <h3><?= htmlspecialchars($fila['categoria']) ?></h3>
                    <p><?= htmlspecialchars($criterio) ?></p>
                </div>
            </a>
            <?php $i++; 
        endwhile;
    } else {
        echo '<p class="no-results">No se encontraron categorías con los filtros seleccionados.</p>';
    }
    ?>
</div>