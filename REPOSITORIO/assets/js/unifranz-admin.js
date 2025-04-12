// unifranz-admin.js

document.addEventListener('DOMContentLoaded', function() {
    // 1. Sistema de Notificaciones (común a todos)
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <div class="notification-content">
                <div class="notification-title">${type === 'success' ? 'Éxito' : type === 'error' ? 'Error' : 'Información'}</div>
                <div>${message}</div>
            </div>
            <button class="notification-close" aria-label="Cerrar"><i class="fas fa-times"></i></button>
        `;
        
        document.body.appendChild(notification);
        
        notification.querySelector('.notification-close').addEventListener('click', function() {
            notification.remove();
        });
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Mostrar notificaciones basadas en parámetros URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === 'true') {
        showNotification('Operación realizada exitosamente', 'success');
    } else if (urlParams.get('action_success') === 'true') {
        showNotification('Estado actualizado exitosamente', 'success');
    } else if (urlParams.get('delete_success') === 'true') {
        showNotification('Registro eliminado exitosamente', 'success');
    } else if (urlParams.has('error')) {
        showNotification('Ocurrió un error al procesar la solicitud', 'error');
    }

    // 2. Búsqueda en Tablas (común a todos)
    const searchInputs = document.querySelectorAll('#searchInput');
    searchInputs.forEach(searchInput => {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = this.closest('.table-actions').nextElementSibling.querySelector('table');
            const tableRows = table.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                let found = false;
                const cells = row.querySelectorAll('td');
                
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                    }
                });
                
                row.style.display = found ? '' : 'none';
            });
        });
    });

    // 3. Validación de Formularios (común a todos)
    const forms = document.querySelectorAll('form[id$="Form"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error-field');
                    isValid = false;
                    
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'Este campo es obligatorio';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('error-field');
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Por favor complete todos los campos obligatorios', 'error');
            }
        });
    });

    // 4. Confirmación de Acciones (común a todos)
    const actionLinks = document.querySelectorAll('.activate, .deactivate, .delete');
    actionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const action = this.classList.contains('activate') ? 'activar' : 
                          this.classList.contains('deactivate') ? 'desactivar' : 'eliminar';
            if (!confirm(`¿Está seguro que desea ${action} este registro?`)) {
                e.preventDefault();
            }
        });
    });

    // 5. Resaltar Fila en Edición (común a todos)
    if (document.body.dataset.editando === 'true') {
        const editId = document.body.dataset.editId || '0';
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const idCell = row.cells[0];
            if (idCell && idCell.textContent == editId) {
                row.style.backgroundColor = 'rgba(255, 81, 0, 0.1)';
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    // 6. Sistema de Paginación (tu código adaptado)
    function addTablePagination() {
        const tables = document.querySelectorAll('.table-container table');
        
        tables.forEach(table => {
            if (table.querySelector('tbody')) {
                const rows = Array.from(table.querySelectorAll('tbody tr'));
                const rowsPerPage = 10;
                const pageCount = Math.ceil(rows.length / rowsPerPage);
                
                if (pageCount <= 1) return; // No paginar si hay menos de 1 página
                
                // Crear contenedor de paginación
                const paginationContainer = document.createElement('div');
                paginationContainer.className = 'pagination-container';
                paginationContainer.style.display = 'flex';
                paginationContainer.style.gap = '8px';
                paginationContainer.style.justifyContent = 'center';
                paginationContainer.style.marginTop = '20px';
                
                table.parentNode.appendChild(paginationContainer);
                
                // Función para mostrar página
                const showPage = (pageNum) => {
                    const start = (pageNum - 1) * rowsPerPage;
                    const end = start + rowsPerPage;
                    
                    rows.forEach((row, index) => {
                        row.style.display = (index >= start && index < end) ? '' : 'none';
                    });
                    
                    // Actualizar botones activos
                    paginationContainer.querySelectorAll('.page-btn').forEach(btn => {
                        btn.classList.remove('active');
                        btn.style.backgroundColor = 'white';
                        btn.style.color = 'inherit';
                        btn.style.borderColor = 'var(--gray-300)';
                    });
                    
                    const activeBtn = paginationContainer.querySelector(`.page-btn:nth-child(${pageNum})`);
                    if (activeBtn) {
                        activeBtn.classList.add('active');
                        activeBtn.style.backgroundColor = 'var(--primary-color)';
                        activeBtn.style.color = 'white';
                        activeBtn.style.borderColor = 'var(--primary-color)';
                    }
                };
                
                // Crear botones de paginación
                for (let i = 1; i <= pageCount; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.className = 'page-btn';
                    pageButton.textContent = i;
                    pageButton.style.width = '36px';
                    pageButton.style.height = '36px';
                    pageButton.style.borderRadius = '50%';
                    pageButton.style.border = '1px solid var(--gray-300)';
                    pageButton.style.background = 'white';
                    pageButton.style.cursor = 'pointer';
                    pageButton.style.transition = 'all 0.2s ease';
                    
                    pageButton.addEventListener('mouseover', function() {
                        if (!this.classList.contains('active')) {
                            this.style.backgroundColor = 'var(--gray-100)';
                        }
                    });
                    
                    pageButton.addEventListener('mouseout', function() {
                        if (!this.classList.contains('active')) {
                            this.style.backgroundColor = 'white';
                        }
                    });
                    
                    pageButton.addEventListener('click', function() {
                        showPage(i);
                    });
                    
                    paginationContainer.appendChild(pageButton);
                }
                
                // Mostrar la primera página por defecto
                showPage(1);
            }
        });
    }
    
    // Iniciar paginación en todas las tablas
    addTablePagination();

    // 7. Mejoras para selects
    const selects = document.querySelectorAll('select');
    selects.forEach(select => {
        select.addEventListener('focus', function() {
            this.style.borderColor = 'var(--primary-color)';
        });
        
        select.addEventListener('blur', function() {
            this.style.borderColor = '';
        });
    });
});

// Función global para mostrar notificaciones desde otros scripts
window.showNotification = showNotification;