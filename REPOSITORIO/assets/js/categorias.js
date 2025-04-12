// Función para actualizar URL con los parámetros
function actualizarURL() {
    const params = new URLSearchParams();
    const categoriaId = document.getElementById('categoria')?.value;
    const codigoValue = document.getElementById('codigo')?.value;
    const buscadorValue = document.getElementById('buscador')?.value.trim();

    if (categoriaId && categoriaId !== '0') {
        params.set('cat', categoriaId);
    }

    if (codigoValue) {
        // Redirigir directamente si hay código
        window.location.href = '../../dashboard/documentos/documentos.php?codigo=' + encodeURIComponent(codigoValue);
        return;
    }

    if (buscadorValue) {
        params.set('buscador', buscadorValue);
    }

    window.location.href = window.location.pathname + (params.toString() ? `?${params.toString()}` : '');
}

// Evento al cambiar categoría
document.getElementById('categoria')?.addEventListener('change', function() {
    document.getElementById('codigo').selectedIndex = 0;
    actualizarURL();
});

// Evento al cambiar código (ya manejado en actualizarURL)
document.getElementById('codigo')?.addEventListener('change', actualizarURL);

// Buscador se activa al presionar Enter
document.getElementById('buscador')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        actualizarURL();
    }
});

// Función para borrar filtros
function borrarFiltros() {
    window.location.href = window.location.pathname;
}

// Al cargar la página, marcar el código si viene en la URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const codigoSeleccionado = urlParams.get('codigo');

    if (codigoSeleccionado) {
        const selectCodigo = document.getElementById('codigo');
        for (let i = 0; i < selectCodigo.options.length; i++) {
            if (selectCodigo.options[i].value === codigoSeleccionado) {
                selectCodigo.selectedIndex = i;
                break;
            }
        }
    }
});
