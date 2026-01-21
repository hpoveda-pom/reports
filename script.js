function goToHome() {
    // Limpiar URL y volver al inicio
    window.location.search = '';
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.querySelector('.collapse-btn');
    sidebar.classList.toggle('collapsed');
    
    // Cerrar menú de usuario si está abierto
    const userMenu = document.getElementById('userMenu');
    if (userMenu) {
        userMenu.classList.remove('active');
    }
    
    if (sidebar.classList.contains('collapsed')) {
        collapseBtn.textContent = '→';
    } else {
        collapseBtn.textContent = '←';
    }
}

function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    if (userMenu) {
        userMenu.classList.toggle('active');
    }
}

// Cerrar menú al hacer clic fuera
document.addEventListener('click', function(event) {
    const userSection = document.querySelector('.user-section');
    const userMenu = document.getElementById('userMenu');
    
    if (userMenu && userSection && !userSection.contains(event.target) && !userMenu.contains(event.target)) {
        userMenu.classList.remove('active');
    }
});

function toggleTheme() {
    const body = document.body;
    const isDark = body.classList.toggle('dark-mode');
    
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');
    
    if (isDark) {
        // Modo oscuro activo: mostrar sol para cambiar a claro
        themeIcon.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="3.5" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M9 2 L9 3 M9 15 L9 16 M2 9 L3 9 M15 9 L16 9 M3.5 3.5 L4.2 4.2 M13.8 13.8 L14.5 14.5 M3.5 14.5 L4.2 13.8 M13.8 4.2 L14.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
        themeText.textContent = 'Modo claro';
        localStorage.setItem('theme', 'dark');
    } else {
        // Modo claro activo: mostrar luna para cambiar a oscuro
        themeIcon.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 2 C5 2, 3 3, 2 5 C2 7, 3 9, 5 10 C4 11, 4 12, 5 13 C6 14, 8 14, 9 13 C11 14, 14 12, 15 10 C14 8, 12 7, 10 6 C9 4, 8 2, 7 2 Z" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        themeText.textContent = 'Modo oscuro';
        localStorage.setItem('theme', 'light');
    }
}

// Cargar tema guardado al iniciar
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');
    
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        // Modo oscuro activo: mostrar sol
        if (themeIcon) themeIcon.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="3.5" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M9 2 L9 3 M9 15 L9 16 M2 9 L3 9 M15 9 L16 9 M3.5 3.5 L4.2 4.2 M13.8 13.8 L14.5 14.5 M3.5 14.5 L4.2 13.8 M13.8 4.2 L14.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
        if (themeText) themeText.textContent = 'Modo claro';
    } else {
        // Modo claro activo: mostrar luna
        if (themeIcon) themeIcon.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 2 C5 2, 3 3, 2 5 C2 7, 3 9, 5 10 C4 11, 4 12, 5 13 C6 14, 8 14, 9 13 C11 14, 14 12, 15 10 C14 8, 12 7, 10 6 C9 4, 8 2, 7 2 Z" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }
});

function toggleFolder(element) {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('collapsed')) {
        return; // No expandir carpetas si el sidebar está colapsado
    }
    
    const folderItem = element.parentElement;
    const folderContent = folderItem.querySelector('.folder-content');
    const folderIcon = element.querySelector('.folder-icon');
    
    const isExpanded = folderItem.classList.contains('expanded');
    
    if (isExpanded) {
        folderContent.style.display = 'none';
        folderIcon.textContent = '>';
        folderItem.classList.remove('expanded');
    } else {
        folderContent.style.display = 'block';
        folderIcon.textContent = 'v';
        folderItem.classList.add('expanded');
    }
}

function filterReports() {
    const searchInput = document.getElementById('searchInput');
    const filter = searchInput.value.toLowerCase();
    const reportItems = document.querySelectorAll('.report-item');
    const folderItems = document.querySelectorAll('.folder-item');
    
    if (filter === '') {
        // Si no hay filtro, mostrar todos los reportes y colapsar todas las carpetas
        reportItems.forEach(item => {
            item.style.display = 'flex';
        });
        folderItems.forEach(folder => {
            folder.style.display = 'block';
            // Colapsar la carpeta
            const folderContent = folder.querySelector('.folder-content');
            const folderHeader = folder.querySelector('.folder-header');
            const folderIcon = folderHeader.querySelector('.folder-icon');
            
            if (folder.classList.contains('expanded')) {
                folderContent.style.display = 'none';
                folderIcon.textContent = '>';
                folder.classList.remove('expanded');
            }
        });
        return;
    }
    
    reportItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(filter)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Mostrar/ocultar carpetas según si tienen reportes visibles
    folderItems.forEach(folder => {
        const folderContent = folder.querySelector('.folder-content');
        const reports = folderContent.querySelectorAll('.report-item');
        const hasVisibleReports = Array.from(reports).some(report => {
            return report.style.display !== 'none';
        });
        
        if (hasVisibleReports) {
            folder.style.display = 'block';
            // Expandir la carpeta si tiene resultados
            if (!folder.classList.contains('expanded')) {
                const folderHeader = folder.querySelector('.folder-header');
                toggleFolder(folderHeader);
            }
        } else {
            folder.style.display = 'none';
        }
    });
}

// Variable global para el datatable
let currentDataTable = null;

// Cargar reporte al iniciar si hay parámetro en URL
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si hay un parámetro report en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const reportId = urlParams.get('report');
    
    if (reportId) {
        // Encontrar el reporte en el DOM
        const reportItems = document.querySelectorAll('.report-item');
        reportItems.forEach(item => {
            const itemReportId = parseInt(item.querySelector('.report-number').textContent.replace('.', ''));
            if (itemReportId == reportId) {
                item.classList.add('active');
                
                // Expandir la carpeta padre
                const folderItem = item.closest('.folder-item');
                if (folderItem && !folderItem.classList.contains('expanded')) {
                    const folderHeader = folderItem.querySelector('.folder-header');
                    toggleFolder(folderHeader);
                }
                
                // Cargar el reporte
                const reportName = item.textContent.trim();
                const folderName = folderItem ? folderItem.querySelector('.folder-name').textContent : '';
                loadReport(reportId, reportName, folderName);
            }
        });
    }
    
    // Agregar funcionalidad de clic a los reportes
    const reportItems = document.querySelectorAll('.report-item');
    reportItems.forEach(item => {
        item.addEventListener('click', function() {
            // Obtener el número del reporte
            const reportNumber = parseInt(this.querySelector('.report-number').textContent.replace('.', ''));
            
            // Obtener filtros actuales de la URL (si existen)
            const urlParams = new URLSearchParams(window.location.search);
            const params = new URLSearchParams();
            params.append('report', reportNumber);
            
            // Mantener filtros si existen
            ['agente', 'tipo', 'estado', 'satisfaccion', 'calificacion'].forEach(filter => {
                const value = urlParams.get(filter);
                if (value) {
                    params.append(filter, value);
                }
            });
            
            // Navegar con GET
            window.location.search = params.toString();
        });
    });
});

function loadReport(reportId, reportFullName, folderName) {
    const reportContent = document.getElementById('reportContent');
    
    // Mostrar loading
    reportContent.innerHTML = '<div class="loading-state">Cargando datos...</div>';
    
    // Obtener filtros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const filterParams = new URLSearchParams();
    filterParams.append('report', reportId);
    
    // Agregar todos los filtros de la URL (excepto 'report')
    for (const [key, value] of urlParams.entries()) {
        if (key !== 'report' && value) {
            filterParams.append(key, value);
        }
    }
    
    // Hacer petición a PHP
    fetch(`data.php?${filterParams.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Verificar si hay un error en la respuesta
            if (data.error) {
                console.error('Error del servidor:', data.error);
                reportContent.innerHTML = `<div class="error-state">
                    <h3>Error al cargar los datos</h3>
                    <p>${data.error}</p>
                    <p><small>Reporte ID: ${data.reportId || reportId}</small></p>
                </div>`;
            } else {
                displayReport(data, reportFullName, folderName, reportId);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            reportContent.innerHTML = `<div class="error-state">
                <h3>Error al cargar los datos</h3>
                <p>${error.message}</p>
                <p><small>Por favor, verifica la consola del navegador para más detalles.</small></p>
            </div>`;
        });
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const urlParams = new URLSearchParams(window.location.search);
    const params = new URLSearchParams();
    
    // Mantener el reporte actual de la URL
    const reportId = urlParams.get('report');
    if (reportId) {
        params.append('report', reportId);
    }
    
    // Agregar todos los filtros del formulario dinámicamente
    for (const [key, value] of formData.entries()) {
        if (value && value !== '') {
            params.append(key, value);
        }
    }
    
    // Recargar reporte con filtros
    window.location.search = params.toString();
}

function clearFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    const reportId = urlParams.get('report');
    
    // Limpiar todos los filtros excepto el reportId
    if (reportId) {
        window.location.search = `report=${reportId}`;
    } else {
        window.location.search = '';
    }
}

function toggleFiltersAccordion(header) {
    const accordion = header.closest('.filters-accordion');
    const content = accordion.querySelector('.filters-accordion-content');
    const icon = header.querySelector('.filters-accordion-icon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.textContent = '▼';
        accordion.classList.add('expanded');
    } else {
        content.style.display = 'none';
        icon.textContent = '▶';
        accordion.classList.remove('expanded');
    }
}

function displayReport(data, reportFullName, folderName, reportId) {
    const reportContent = document.getElementById('reportContent');
    
    // Destruir datatable anterior si existe
    if (currentDataTable) {
        currentDataTable.destroy();
    }
    
    // Obtener valores actuales de filtros desde URL
    const urlParams = new URLSearchParams(window.location.search);
    
    // Crear filtros dinámicamente basados en las opciones disponibles
    const filterOptions = data.filterOptions || {};
    const filterKeys = Object.keys(filterOptions);
    
    // Función para formatear nombres de columnas (capitalizar primera letra)
    function formatColumnName(name) {
        return name.charAt(0).toUpperCase() + name.slice(1).replace(/_/g, ' ');
    }
    
    // Generar HTML de filtros dinámicamente
    let filterGroupsHtml = '';
    filterKeys.forEach(key => {
        const options = filterOptions[key] || [];
        const currentValue = urlParams.get(key) || '';
        
        if (Array.isArray(options) && options.length > 0) {
            filterGroupsHtml += `
                <div class="filter-group">
                    <label>${formatColumnName(key)}:</label>
                    <select name="${key}" class="filter-select">
                        <option value="">Todos</option>
                        ${options.map(opt => 
                            `<option value="${opt}" ${currentValue === String(opt) ? 'selected' : ''}>${opt}</option>`
                        ).join('')}
                    </select>
                </div>
            `;
        }
    });
    
    // Crear HTML de filtros con acordeón
    const filterHtml = filterGroupsHtml ? `
        <div class="filters-accordion">
            <div class="filters-accordion-header" onclick="toggleFiltersAccordion(this)">
                <span class="filters-accordion-icon">▶</span>
                <span class="filters-accordion-title">Filtros Avanzados</span>
            </div>
            <div class="filters-accordion-content" style="display: none;">
                <form id="filterForm" class="filters-container">
                    ${filterGroupsHtml}
                    <div class="filter-actions">
                        <button type="button" onclick="applyFilters()" class="filter-btn apply-btn">Aplicar</button>
                        <button type="button" onclick="clearFilters()" class="filter-btn clear-btn">Limpiar</button>
                    </div>
                </form>
            </div>
        </div>
    ` : '';
    
    // Obtener columnas dinámicamente desde los metadatos
    let columnNames = [];
    
    if (data.columns && Array.isArray(data.columns)) {
        // Usar las columnas de los metadatos
        columnNames = data.columns;
    } else if (data.data && data.data.length > 0) {
        // Fallback: obtener nombres de columnas del primer registro
        columnNames = Object.keys(data.data[0]);
    }
    
    // Función para formatear nombres de columnas (capitalizar primera letra)
    function formatColumnName(name) {
        return name.charAt(0).toUpperCase() + name.slice(1).replace(/_/g, ' ');
    }
    
    // Generar encabezados de tabla dinámicamente
    const tableHeaders = columnNames.map(col => `<th>${formatColumnName(col)}</th>`).join('');
    
    // Crear HTML del datatable
    const html = `
        ${filterHtml}
        <nav class="breadcrumbs">
            <a href="#" onclick="goToHome(); return false;" class="breadcrumb-item">POM Reportes</a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-item">${folderName}</span>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-item active">${reportFullName}</span>
        </nav>
        <div class="report-header">
            <h2 class="report-title">${reportFullName}</h2>
        </div>
        <div class="table-container">
            <table id="reportTable" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        ${tableHeaders}
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán vía AJAX con paginación del servidor -->
                </tbody>
            </table>
        </div>
    `;
    
    reportContent.innerHTML = html;
    
    // Inicializar DataTable con paginación del lado del servidor
    currentDataTable = $('#reportTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get_report_data_ajax.php',
            type: 'GET',
            data: function(d) {
                // Agregar el reportId y filtros actuales
                d.report = reportId;
                
                // Agregar filtros de la URL
                const urlParams = new URLSearchParams(window.location.search);
                for (const [key, value] of urlParams.entries()) {
                    if (key !== 'report') {
                        // Manejar arrays (group_by[])
                        if (key === 'group_by[]' || key.startsWith('group_by')) {
                            const groupByValues = urlParams.getAll('group_by[]');
                            if (groupByValues.length > 0) {
                                d['group_by'] = groupByValues;
                            }
                        } else if (key === 'agg_function[]' || key === 'agg_column[]') {
                            // Las agregaciones se manejan por separado
                            continue;
                        } else {
                            d[key] = value;
                        }
                    }
                }
                
                // Manejar agregaciones
                const aggFunctions = urlParams.getAll('agg_function[]');
                const aggColumns = urlParams.getAll('agg_column[]');
                if (aggFunctions.length > 0 && aggColumns.length > 0) {
                    d['agg_function'] = aggFunctions;
                    d['agg_column'] = aggColumns;
                }
            },
            error: function(xhr, error, thrown) {
                console.error('Error al cargar datos:', error);
                alert('Error al cargar los datos. Por favor, intenta nuevamente.');
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Exportar a Excel',
                className: 'export-btn',
                action: function(e, dt, button, config) {
                    // Redirigir a export_excel.php con los filtros actuales
                    const urlParams = new URLSearchParams(window.location.search);
                    let exportUrl = 'export_excel.php?report=' + reportId;
                    for (const [key, value] of urlParams.entries()) {
                        if (key !== 'report') {
                            exportUrl += '&' + key + '=' + encodeURIComponent(value);
                        }
                    }
                    window.location.href = exportUrl;
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        language: {
            "decimal": "",
            "emptyTable": "No hay datos disponibles en la tabla",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron registros coincidentes",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": activar para ordenar columna ascendente",
                "sortDescending": ": activar para ordenar columna descendente"
            }
        },
        order: [[0, 'desc']],
        scrollX: true
    });
}
