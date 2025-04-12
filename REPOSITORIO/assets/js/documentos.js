
function filtrarDocumentos() {
    const filtroTexto = document.getElementById('buscador').value.toLowerCase();
    const codigoSeleccionado = document.getElementById('codigo').value.toLowerCase();
    const tarjetas = document.querySelectorAll('.cards-container .card');

    tarjetas.forEach(tarjeta => {
        const titulo = tarjeta.querySelector('h3').innerText.toLowerCase();
        const descripcion = tarjeta.querySelector('p').innerText.toLowerCase();
        const codigo = tarjeta.querySelector('.codigo').innerText.toLowerCase();

        const coincideTexto = titulo.includes(filtroTexto) || descripcion.includes(filtroTexto);
        const coincideCodigo = !codigoSeleccionado || codigo === codigoSeleccionado;

        if (coincideTexto && coincideCodigo) {
            tarjeta.style.display = 'flex';
        } else {
            tarjeta.style.display = 'none';
        }
    });
}

function borrarFiltros() {
    document.getElementById('codigo').value = '';
    document.getElementById('buscador').value = '';
    filtrarDocumentos();
}
