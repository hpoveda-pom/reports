<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'get_report_data.php';

$reportId = isset($_GET['report']) ? intval($_GET['report']) : 1;

// Obtener configuración del reporte
$reportConfig = getReportConfig($reportId);

if (!$reportConfig) {
    echo json_encode([
        'title' => 'Reporte no encontrado',
        'data' => [],
        'filterOptions' => []
    ]);
    exit;
}

// Si no hay configuración, usar MySQL por defecto
$source = ($reportConfig && isset($reportConfig['source'])) ? $reportConfig['source'] : 'mysql';

try {
    if ($source === 'sqlserver') {
        // Validar configuración
        if (empty($reportConfig['table'])) {
            throw new Exception("Configuración incompleta del reporte. Verifica que la tabla esté configurada.");
        }
        
        // Obtener datos desde SQL Server usando conexión centralizada
        require_once __DIR__ . '/connections.php';
        $sqlServerConfig = getSqlServerConfig();
        $conn = connectSqlServer();
        
        // Detectar columnas de la tabla
        $tableColumns = getTableColumns($conn, $sqlServerConfig['database'], $reportConfig['table']);
        
        if (empty($tableColumns)) {
            sqlsrv_close($conn);
            throw new Exception("No se pudieron obtener las columnas de la tabla: " . $reportConfig['table'] . ". Verifica que la tabla exista en la base de datos " . $sqlServerConfig['database']);
        }
        
        $columnNames = array_column($tableColumns, 'name');
        
        // Obtener filtros dinámicos de los parámetros GET
        $filters = [];
        foreach ($columnNames as $column) {
            $filterValue = isset($_GET[$column]) ? trim($_GET[$column]) : '';
            $filters[$column] = $filterValue;
        }
        
        // Obtener valores únicos para filtros (solo de columnas que no sean numéricas o de fecha)
        $uniqueOptions = [];
        foreach ($tableColumns as $col) {
            $colName = $col['name'];
            $dataType = strtolower($col['type']);
            
            if (stripos($dataType, 'int') === false &&
                stripos($dataType, 'float') === false &&
                stripos($dataType, 'decimal') === false &&
                stripos($dataType, 'double') === false &&
                stripos($dataType, 'date') === false &&
                stripos($dataType, 'time') === false &&
                stripos($dataType, 'year') === false &&
                stripos($colName, 'id') === false) {
                
                $uniqueValues = getUniqueValues($conn, $reportConfig['table'], $colName);
                if (!empty($uniqueValues)) {
                    $uniqueOptions[$colName] = $uniqueValues;
                }
            }
        }
        
        sqlsrv_close($conn);
        
        require_once __DIR__ . '/get_report_names.php';
        $reportNames = get_report_names();
        $title = isset($reportNames[$reportId]['name']) ? $reportNames[$reportId]['name'] : 'Reporte ' . $reportId;
        
        echo json_encode([
            'title' => $title,
            'data' => [], // Datos vacíos - se cargarán vía AJAX
            'filterOptions' => $uniqueOptions,
            'columns' => $columnNames,
            'currentFilters' => $filters
        ], JSON_UNESCAPED_UNICODE);
        
    } elseif ($source === 'mysql') {
        // Validar configuración
        if (empty($reportConfig['table'])) {
            throw new Exception("Configuración incompleta del reporte. Verifica que la tabla esté configurada.");
        }
        
        // Obtener datos desde MySQL usando conexión centralizada
        require_once __DIR__ . '/connections.php';
        $mysqlConfig = getMySQLConfig();
        $conn = connectMySQL();
        
        // Detectar columnas de la tabla
        $tableColumns = getMySQLTableColumns($conn, $mysqlConfig['database'], $reportConfig['table']);
        
        if (empty($tableColumns)) {
            $conn = null;
            throw new Exception("No se pudieron obtener las columnas de la tabla: " . $reportConfig['table'] . ". Verifica que la tabla exista en la base de datos " . $mysqlConfig['database']);
        }
        
        $columnNames = array_column($tableColumns, 'name');
        
        // Obtener filtros dinámicos de los parámetros GET
        $filters = [];
        $whereConditions = [];
        $params = [];
        
        foreach ($columnNames as $column) {
            $filterValue = isset($_GET[$column]) ? trim($_GET[$column]) : '';
            if (!empty($filterValue)) {
                $filters[$column] = $filterValue;
                $whereConditions[] = "`$column` = ?";
                $params[] = $filterValue;
            } else {
                $filters[$column] = '';
            }
        }
        
        // NO cargar todos los datos aquí - solo metadatos
        // Los datos se cargarán vía AJAX con paginación del lado del servidor
        
        // Obtener valores únicos para filtros
        $uniqueOptions = [];
        foreach ($tableColumns as $col) {
            $colName = $col['name'];
            $dataType = strtolower($col['type']);
            
            // Omitir tipos numéricos, fechas y IDs para filtros
            if (stripos($dataType, 'int') === false && 
                stripos($dataType, 'float') === false && 
                stripos($dataType, 'decimal') === false &&
                stripos($dataType, 'double') === false &&
                stripos($dataType, 'date') === false &&
                stripos($dataType, 'time') === false &&
                stripos($dataType, 'year') === false &&
                stripos($colName, 'id') === false) {
                
                $uniqueValues = getMySQLUniqueValues($conn, $reportConfig['table'], $colName);
                if (!empty($uniqueValues)) {
                    $uniqueOptions[$colName] = $uniqueValues;
                }
            }
        }
        
        $conn = null;
        
        // Obtener nombre del reporte
        require_once __DIR__ . '/get_report_names.php';
        $reportNames = get_report_names();
        $title = isset($reportNames[$reportId]) ? $reportNames[$reportId]['name'] : 'Reporte ' . $reportId;
        
        // Retornar solo metadatos - los datos se cargarán vía AJAX con paginación
        echo json_encode([
            'title' => $title,
            'data' => [], // Datos vacíos - se cargarán vía AJAX
            'filterOptions' => $uniqueOptions,
            'columns' => $columnNames,
            'currentFilters' => $filters
        ]);
        
    } else {
        throw new Exception("Fuente de datos no soportada: " . $source);
    }
    
} catch (Exception $e) {
    // Log del error para debugging (opcional, puedes comentar esto en producción)
    error_log("Error en data.php para reporte $reportId: " . $e->getMessage());
    
    echo json_encode([
        'title' => 'Error',
        'data' => [],
        'filterOptions' => [],
        'error' => $e->getMessage(),
        'reportId' => $reportId
    ], JSON_UNESCAPED_UNICODE);
}
?>
