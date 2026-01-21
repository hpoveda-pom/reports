<?php
// Configuración básica
session_start();
require_once 'helpers.php';

// Manejar tema (dark mode)
if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'] === 'dark' ? 'dark' : 'light';
}
$currentTheme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';

// Obtener parámetro report de GET
$currentReport = isset($_GET['report']) ? intval($_GET['report']) : 0;
$reportData = null;

// Si hay un reporte seleccionado, cargar los datos
$executionTime = null;
$isSqlServerReport = false;
if ($currentReport > 0) {
    require_once 'config.php';
    $reportConfig = getReportConfig($currentReport);
    $isDatabaseReport = ($reportConfig && isset($reportConfig['source']) && in_array($reportConfig['source'], ['mysql']));
    $isSqlServerReport = ($reportConfig && isset($reportConfig['source']) && $reportConfig['source'] === 'sqlserver');
    
    // Para reportes SQL Server o MySQL, solo cargar metadatos (filtros, columnas) sin los datos
    // Los datos se cargarán vía AJAX con paginación
    if ($isDatabaseReport) {
        $startTime = microtime(true);
        require_once 'get_report_data.php';
        // Cargar solo metadatos para filtros y columnas
        $reportData = get_report_data_metadata($currentReport);
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        // Si no se pudo cargar metadata, retornar estructura vacía con error
        if (!$reportData || is_null($reportData)) {
            $reportData = [
                'reportName' => 'Error',
                'folderName' => 'Error',
                'data' => [],
                'filterOptions' => [],
                'currentFilters' => [],
                'columns' => [],
                'error' => 'No se pudo cargar la configuración del reporte. Verifique que esté configurado correctamente en config.php'
            ];
        }
    } else {
        // Si no es un reporte de base de datos configurado, cargar metadata de MySQL por defecto
        $startTime = microtime(true);
        require_once 'get_report_data.php';
        $reportData = get_report_data_metadata($currentReport);
        if (!$reportData || is_null($reportData)) {
            $reportData = [
                'reportName' => 'Error',
                'folderName' => 'Error',
                'data' => [],
                'filterOptions' => [],
                'currentFilters' => [],
                'columns' => [],
                'error' => 'No se pudo cargar la configuración del reporte. Verifique que esté configurado correctamente en config.php'
            ];
        }
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
    }
}

// Búsqueda
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POM Reportes</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
</head>
<body class="<?php echo $currentTheme === 'dark' ? 'dark-mode' : ''; ?>">
    <div class="container">
        <!-- Left Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1 class="app-name"><a href="index.php" style="text-decoration: none; color: inherit;">POM Reportes</a></h1>
                <button class="collapse-btn" onclick="toggleSidebar()" title="Colapsar sidebar">←</button>
            </div>
            <form method="get" action="" class="search-container">
                <?php if ($currentReport > 0): ?>
                    <input type="hidden" name="report" value="<?php echo $currentReport; ?>">
                <?php endif; ?>
                <input type="text" class="search-input" name="search" id="searchInput" placeholder="Buscar reportes..." value="<?php echo htmlspecialchars($searchQuery); ?>" onkeyup="filterReports()">
            </form>
            <nav class="nav-menu">
                <?php
                require_once 'get_report_names.php';
                $reportNames = get_report_names();
                
                // Agrupar reportes por carpeta
                $folders = [];
                foreach ($reportNames as $id => $report) {
                    $folder = $report['folder'];
                    if (!isset($folders[$folder])) {
                        $folders[$folder] = [];
                    }
                    $folders[$folder][$id] = $report['name'];
                }
                
                // Separar en tres grupos: Contact Center, POM Reportes v2 y POM Reportes
                $contactCenterFolders = [];
                $pomV2Folders = [];
                $pomFolders = [];
                
                foreach ($folders as $folderName => $reports) {
                    if (strpos($folderName, 'Contact Center') === 0) {
                        $contactCenterFolders[$folderName] = $reports;
                    } elseif (strpos($folderName, 'POM Reportes v2') === 0) {
                        $pomV2Folders[$folderName] = $reports;
                    } else {
                        $pomFolders[$folderName] = $reports;
                    }
                }
                
                // Ordenar alfabéticamente cada grupo
                ksort($contactCenterFolders);
                ksort($pomV2Folders);
                ksort($pomFolders);
                
                // Función para renderizar carpetas
                function renderFolders($folders, $currentReport, $searchQuery) {
                    foreach ($folders as $folderName => $reports):
                        $reportIds = array_keys($reports);
                        $isExpanded = in_array($currentReport, $reportIds);
                    ?>
                    <div class="folder-item <?php echo $isExpanded ? 'expanded' : ''; ?>">
                        <div class="folder-header" onclick="toggleFolder(this)">
                            <span class="folder-icon"><?php echo $isExpanded ? 'v' : '>'; ?></span>
                            <span class="folder-name"><?php echo htmlspecialchars($folderName); ?></span>
                            <span class="folder-tooltip"><?php echo htmlspecialchars($folderName); ?></span>
                        </div>
                        <div class="folder-content" style="<?php echo $isExpanded ? 'display: block;' : 'display: none;'; ?>">
                            <?php 
                            foreach ($reports as $num => $name):
                                $fullName = "$num. $name";
                                if (empty($searchQuery) || stripos($fullName, $searchQuery) !== false || stripos($folderName, $searchQuery) !== false):
                            ?>
                            <a href="?report=<?php echo $num; ?><?php echo !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : ''; ?>" class="report-item <?php echo ($currentReport == $num) ? 'active' : ''; ?>"><span class="report-number"><?php echo $num; ?>.</span> <?php echo htmlspecialchars($name); ?></a>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <?php endforeach;
                }
                
                // Sección: REPORTES DE PRUEBA (Contact Center)
                if (!empty($contactCenterFolders)):
                ?>
                <h2 class="section-title">REPORTES DE PRUEBA</h2>
                <?php 
                renderFolders($contactCenterFolders, $currentReport, $searchQuery);
                endif;
                
                // Sección: POM Reportes (MySQL)
                if (!empty($pomFolders)):
                ?>
                <h2 class="section-title">POM Reportes</h2>
                <?php 
                renderFolders($pomFolders, $currentReport, $searchQuery);
                endif;
                
                // Sección: POM Reportes v2 (SQL Server)
                if (!empty($pomV2Folders)):
                ?>
                <h2 class="section-title">POM Reportes v2</h2>
                <?php 
                renderFolders($pomV2Folders, $currentReport, $searchQuery);
                endif;
                ?>
            </nav>
            <div class="user-section" onclick="toggleUserMenu()">
                <div class="user-avatar">
                    <span class="avatar-initials">U</span>
                </div>
                <div class="user-info">
                    <div class="user-name">Usuario</div>
                    <div class="user-email">usuario@email.com</div>
                </div>
            </div>
            <div class="user-menu" id="userMenu">
                <div class="user-menu-header">
                    <div class="user-avatar-menu">
                        <span class="avatar-initials-menu">U</span>
                    </div>
                    <div class="user-info-menu">
                        <div class="user-name-menu">Usuario</div>
                        <div class="user-username">@usuario</div>
                    </div>
                </div>
                <div class="user-menu-items">
                    <div class="menu-item" onclick="toggleTheme()">
                        <span class="menu-icon" id="themeIcon">
                            <?php if ($currentTheme === 'dark'): ?>
                                <!-- Sol para modo claro -->
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="9" cy="9" r="3.5" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    <path d="M9 2 L9 3 M9 15 L9 16 M2 9 L3 9 M15 9 L16 9 M3.5 3.5 L4.2 4.2 M13.8 13.8 L14.5 14.5 M3.5 14.5 L4.2 13.8 M13.8 4.2 L14.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            <?php else: ?>
                                <!-- Luna para modo oscuro -->
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 2 C5 2, 3 3, 2 5 C2 7, 3 9, 5 10 C4 11, 4 12, 5 13 C6 14, 8 14, 9 13 C11 14, 14 12, 15 10 C14 8, 12 7, 10 6 C9 4, 8 2, 7 2 Z" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            <?php endif; ?>
                        </span>
                        <span class="menu-text" id="themeText"><?php echo $currentTheme === 'dark' ? 'Modo claro' : 'Modo oscuro'; ?></span>
                    </div>
                    <div class="menu-item">
                        <span class="menu-icon">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 3 L4 3 C3 3, 2 4, 2 5 L2 13 C2 14, 3 15, 4 15 L6 15" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                <path d="M10 5 L13 5 L16 9 L13 13 L10 13" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 9 L16 9" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="menu-text">Cerrar sesión</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <div id="reportContent" class="report-content">
                <?php if ($reportData): ?>
                    <!-- Filtros -->
                    <form method="get" action="" class="filters-container">
                        <input type="hidden" name="report" value="<?php echo $currentReport; ?>">
                        <?php 
                        // Filtros dinámicos basados en filterOptions
                        if (isset($reportData['filterOptions']) && is_array($reportData['filterOptions'])): 
                            foreach ($reportData['filterOptions'] as $filterKey => $filterOptions): 
                                if (is_array($filterOptions) && !empty($filterOptions)):
                        ?>
                        <div class="filter-group">
                            <label><?php echo ucfirst(str_replace('_', ' ', $filterKey)); ?>:</label>
                            <select name="<?php echo htmlspecialchars($filterKey); ?>" class="filter-select">
                                <option value="">Todos</option>
                                <?php foreach ($filterOptions as $opt): 
                                    $optValue = is_object($opt) ? (string)$opt : $opt;
                                    $currentValue = isset($reportData['currentFilters'][$filterKey]) ? $reportData['currentFilters'][$filterKey] : '';
                                ?>
                                    <option value="<?php echo htmlspecialchars($optValue); ?>" <?php echo ($currentValue == $optValue) ? 'selected' : ''; ?>><?php echo htmlspecialchars($optValue); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php 
                                endif;
                            endforeach;
                        endif; 
                        ?>
                        <div class="filter-actions">
                            <?php
                            // Construir URL de exportación con los mismos filtros actuales
                            // El botón siempre debe estar visible cuando hay un reporte
                            $exportParams = ['report' => $currentReport];
                            if (isset($reportData) && isset($reportData['currentFilters'])) {
                                foreach ($reportData['currentFilters'] as $key => $value) {
                                    if (!empty($value)) {
                                        $exportParams[$key] = $value;
                                    }
                                }
                            }
                            $exportUrl = 'export_excel.php?' . http_build_query($exportParams);
                            ?>
                            <a href="<?php echo htmlspecialchars($exportUrl); ?>" class="filter-btn export-btn" style="text-decoration: none; display: inline-block;">Exportar</a>
                            <button type="submit" class="filter-btn apply-btn">Aplicar</button>
                            <a href="?report=<?php echo $currentReport; ?>" class="filter-btn clear-btn" style="text-decoration: none; display: inline-block;">Limpiar</a>
                        </div>
                    </form>
                    
                    <!-- Agrupación (Tabla Dinámica) -->
                    <div class="grouping-container" style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 8px;">
                        <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #333;">Agrupar (Tabla Dinámica)</h3>
                        <form method="get" action="" id="groupingForm" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;" onsubmit="handleGroupingSubmit(event)">
                            <input type="hidden" name="report" value="<?php echo $currentReport; ?>">
                            <?php 
                            // Mantener filtros actuales en el formulario de agrupación
                            if (isset($reportData['currentFilters'])): 
                                foreach ($reportData['currentFilters'] as $filterKey => $filterValue): 
                                    if (!empty($filterValue)):
                            ?>
                                <input type="hidden" name="<?php echo htmlspecialchars($filterKey); ?>" value="<?php echo htmlspecialchars($filterValue); ?>">
                            <?php 
                                    endif;
                                endforeach;
                            endif; 
                            
                            // Columnas disponibles para agrupación
                            $columns = isset($reportData['columns']) ? $reportData['columns'] : [];
                            $currentGroupBy = isset($_GET['group_by']) ? explode(',', $_GET['group_by']) : [];
                            $currentAggregations = isset($_GET['aggregations']) ? json_decode($_GET['aggregations'], true) : [];
                            ?>
                            
                            <div class="group-field" style="flex: 1; min-width: 200px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Agrupar por:</label>
                                <select name="group_by[]" id="groupBySelect" multiple class="filter-select" style="min-height: 100px; width: 100%;" size="5">
                                    <?php foreach ($columns as $col): ?>
                                        <option value="<?php echo htmlspecialchars($col); ?>" <?php echo in_array($col, $currentGroupBy) ? 'selected' : ''; ?>>
                                            <?php echo ucfirst(str_replace('_', ' ', $col)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small style="color: #666; font-size: 12px;">Mantén Ctrl (Cmd en Mac) para seleccionar múltiples columnas</small>
                            </div>
                            
                            <div class="aggregations-field" style="flex: 2; min-width: 300px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Valores a Calcular:</label>
                                <div id="aggregationsContainer">
                                    <?php if (!empty($currentAggregations)): ?>
                                        <?php foreach ($currentAggregations as $agg): ?>
                                            <div class="aggregation-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                                <select name="agg_function[]" class="filter-select" style="flex: 0 0 120px;">
                                                    <option value="COUNT" <?php echo ($agg['function'] === 'COUNT') ? 'selected' : ''; ?>>Contar</option>
                                                    <option value="SUM" <?php echo ($agg['function'] === 'SUM') ? 'selected' : ''; ?>>Sumar</option>
                                                    <option value="AVG" <?php echo ($agg['function'] === 'AVG') ? 'selected' : ''; ?>>Promedio</option>
                                                    <option value="MIN" <?php echo ($agg['function'] === 'MIN') ? 'selected' : ''; ?>>Mínimo</option>
                                                    <option value="MAX" <?php echo ($agg['function'] === 'MAX') ? 'selected' : ''; ?>>Máximo</option>
                                                </select>
                                                <select name="agg_column[]" class="filter-select" style="flex: 1;">
                                                    <option value="">Seleccione columna...</option>
                                                    <?php foreach ($columns as $col): ?>
                                                        <option value="<?php echo htmlspecialchars($col); ?>" <?php echo ($agg['column'] === $col) ? 'selected' : ''; ?>>
                                                            <?php echo ucfirst(str_replace('_', ' ', $col)); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="button" onclick="removeAggregation(this)" style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">✕</button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="aggregation-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                            <select name="agg_function[]" class="filter-select" style="flex: 0 0 120px;">
                                                <option value="COUNT">Contar</option>
                                                <option value="SUM">Sumar</option>
                                                <option value="AVG">Promedio</option>
                                                <option value="MIN">Mínimo</option>
                                                <option value="MAX">Máximo</option>
                                            </select>
                                            <select name="agg_column[]" class="filter-select" style="flex: 1;">
                                                <option value="">Seleccione columna...</option>
                                                <?php foreach ($columns as $col): ?>
                                                    <option value="<?php echo htmlspecialchars($col); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $col)); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="button" onclick="removeAggregation(this)" style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">✕</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" onclick="addAggregation()" style="padding: 8px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">+ Agregar Función</button>
                            </div>
                            
                            <div class="group-actions" style="flex: 0 0 auto;">
                                <button type="submit" class="filter-btn apply-btn">Aplicar Agrupación</button>
                                <button type="button" onclick="clearGrouping()" class="filter-btn clear-btn" style="margin-top: 10px;">Limpiar Agrupación</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Título -->
                    <div class="report-header">
                        <h2 class="report-title"><?php echo htmlspecialchars($reportData['reportName']); ?></h2>
                    </div>
                    
                    <!-- Breadcrumbs -->
                    <nav class="breadcrumbs">
                        <span class="breadcrumb-item"><?php echo htmlspecialchars($reportData['folderName']); ?></span>
                        <span class="breadcrumb-separator">></span>
                        <span class="breadcrumb-item active"><?php echo htmlspecialchars($reportData['reportName']); ?></span>
                    </nav>
                    
                    <!-- Tabla -->
                    <div class="table-container">
                        <?php if (isset($reportData['error'])): ?>
                            <div style="padding: 20px; color: #d32f2f; background-color: #ffebee; border-radius: 4px; margin: 20px 0;">
                                <strong>Error:</strong> <?php echo htmlspecialchars($reportData['error']); ?>
                                <br><small>Verifique la configuración de conexión en config.php y que el servidor SQL Server esté accesible.</small>
                            </div>
                        <?php else: ?>
                        <table id="reportTable" class="display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <?php 
                                    // Encabezados dinámicos basados en las columnas de los datos
                                    $columns = isset($reportData['columns']) ? $reportData['columns'] : (isset($reportData['data'][0]) ? array_keys($reportData['data'][0]) : []);
                                    foreach ($columns as $column): 
                                    ?>
                                        <th><?php echo ucfirst(str_replace('_', ' ', $column)); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($isDatabaseReport): ?>
                                    <!-- Para reportes SQL Server, los datos se cargan vía AJAX con paginación -->
                                    <tr>
                                        <td colspan="<?php echo count($columns); ?>" style="text-align: center; padding: 20px;">
                                            Cargando datos...
                                        </td>
                                    </tr>
                                <?php elseif (!empty($reportData['data'])): ?>
                                    <?php foreach ($reportData['data'] as $row): ?>
                                        <tr>
                                            <?php foreach ($columns as $column): ?>
                                                <td><?php 
                                                    $value = isset($row[$column]) ? $row[$column] : '';
                                                    // Si es numérico, mostrar sin htmlspecialchars
                                                    if (is_numeric($value)) {
                                                        echo $value;
                                                    } else {
                                                        echo htmlspecialchars($value);
                                                    }
                                                ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?php echo count($columns); ?>" style="text-align: center; padding: 20px;">
                                            No hay datos disponibles
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($executionTime !== null): ?>
                    <!-- Tiempo de ejecución -->
                    <div class="execution-time" style="margin-top: 16px; padding: 12px; text-align: right; font-size: 12px; color: #666;">
                        Tiempo de ejecución: <strong><?php echo number_format($executionTime, 2, '.', ','); ?> ms</strong>
                    </div>
                    <?php endif; ?>
                    
                    <script>
                        // Inicializar DataTable solo cuando hay datos
                        $(document).ready(function() {
                            var isDatabaseReport = <?php echo $isDatabaseReport ? 'true' : 'false'; ?>;
                            var reportId = <?php echo $currentReport; ?>;
                            
                            var dtConfig = {
                                dom: 'rtip',
                                pageLength: 10,
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
                                scrollX: true,
                                searching: false
                            };
                            
                            // Si es reporte SQL Server o MySQL, usar server-side processing
                            if (isDatabaseReport) {
                                dtConfig.serverSide = true;
                                dtConfig.processing = true;
                                dtConfig.ajax = {
                                    url: 'get_report_data_ajax.php',
                                    type: 'GET',
                                    data: function(d) {
                                        // Agregar report ID
                                        d.report = reportId;
                                        
                                        // Agregar filtros del formulario
                                        <?php if (isset($reportData['filterOptions'])): ?>
                                        <?php foreach ($reportData['filterOptions'] as $filterKey => $options): ?>
                                        var <?php echo $filterKey; ?>Filter = $('select[name="<?php echo $filterKey; ?>"]').val();
                                        if (<?php echo $filterKey; ?>Filter) {
                                            d.<?php echo $filterKey; ?> = <?php echo $filterKey; ?>Filter;
                                        }
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                        
                                        // Agregar parámetros de agrupación
                                        var urlParams = new URLSearchParams(window.location.search);
                                        var groupBy = urlParams.getAll('group_by[]');
                                        if (groupBy.length > 0) {
                                            d['group_by'] = groupBy;
                                        }
                                        
                                        var aggFunctions = urlParams.getAll('agg_function[]');
                                        var aggColumns = urlParams.getAll('agg_column[]');
                                        if (aggFunctions.length > 0 && aggColumns.length > 0) {
                                            d['agg_function'] = aggFunctions;
                                            d['agg_column'] = aggColumns;
                                        }
                                    },
                                    dataSrc: function(json) {
                                        // Actualizar encabezados si hay agrupación y se devuelven nuevas columnas
                                        if (json.columns && json.columns.length > 0) {
                                            var table = $('#reportTable').DataTable();
                                            var columns = table.columns();
                                            
                                            // Si el número de columnas cambió, destruir y recrear la tabla
                                            if (columns.count() !== json.columns.length) {
                                                table.destroy();
                                                // Recrear la tabla con nuevos encabezados
                                                $('#reportTable thead tr').html(json.columns.map(col => '<th>' + col + '</th>').join(''));
                                                // Reinicializar DataTable
                                                $('#reportTable').DataTable(dtConfig);
                                                return json.data;
                                            }
                                        }
                                        return json.data;
                                    }
                                };
                            }
                            
                            $('#reportTable').DataTable(dtConfig);
                        });
                    </script>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Selecciona un reporte del menú para visualizar los datos</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Funciones para manejar agrupación
        function addAggregation() {
            const container = document.getElementById('aggregationsContainer');
            const columns = <?php echo json_encode($columns); ?>;
            const row = document.createElement('div');
            row.className = 'aggregation-row';
            row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px; align-items: center;';
            row.innerHTML = `
                <select name="agg_function[]" class="filter-select" style="flex: 0 0 120px;">
                    <option value="COUNT">Contar</option>
                    <option value="SUM">Sumar</option>
                    <option value="AVG">Promedio</option>
                    <option value="MIN">Mínimo</option>
                    <option value="MAX">Máximo</option>
                </select>
                <select name="agg_column[]" class="filter-select" style="flex: 1;">
                    <option value="">Seleccione columna...</option>
                    ${columns.map(col => `<option value="${col}">${col.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</option>`).join('')}
                </select>
                <button type="button" onclick="removeAggregation(this)" style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">✕</button>
            `;
            container.appendChild(row);
        }
        
        function removeAggregation(button) {
            button.closest('.aggregation-row').remove();
        }
        
        function clearGrouping() {
            document.getElementById('groupBySelect').selectedIndex = -1;
            const container = document.getElementById('aggregationsContainer');
            container.innerHTML = '';
            addAggregation(); // Agregar una fila vacía
            
            // Recargar sin parámetros de agrupación
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('group_by');
            urlParams.delete('group_by[]');
            urlParams.delete('agg_function');
            urlParams.delete('agg_function[]');
            urlParams.delete('agg_column');
            urlParams.delete('agg_column[]');
            window.location.search = urlParams.toString();
        }
        
        function handleGroupingSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const urlParams = new URLSearchParams();
            
            // Agregar report
            urlParams.append('report', formData.get('report'));
            
            // Agregar filtros existentes
            <?php if (isset($reportData['currentFilters'])): ?>
            <?php foreach ($reportData['currentFilters'] as $filterKey => $filterValue): ?>
                <?php if (!empty($filterValue)): ?>
                    urlParams.append('<?php echo htmlspecialchars($filterKey); ?>', '<?php echo htmlspecialchars($filterValue); ?>');
                <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
            
            // Agregar group_by
            const groupBy = formData.getAll('group_by[]');
            groupBy.forEach(val => {
                if (val) urlParams.append('group_by[]', val);
            });
            
            // Agregar agregaciones
            const aggFunctions = formData.getAll('agg_function[]');
            const aggColumns = formData.getAll('agg_column[]');
            for (let i = 0; i < aggFunctions.length; i++) {
                if (aggColumns[i]) {
                    urlParams.append('agg_function[]', aggFunctions[i]);
                    urlParams.append('agg_column[]', aggColumns[i]);
                }
            }
            
            // Recargar página con parámetros
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>
