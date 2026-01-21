<?php
require_once 'config.php';

function get_report_data() {
// Obtener parámetros GET
$reportId = isset($_GET['report']) ? intval($_GET['report']) : 0;

// Obtener nombres de reportes
require_once __DIR__ . '/get_report_names.php';
$reportNames = get_report_names();

if ($reportId === 0 || !isset($reportNames[$reportId])) {
    return;
}

$reportName = $reportNames[$reportId]['name'];
$folderName = $reportNames[$reportId]['folder'];

// Obtener configuración del reporte
$reportConfig = getReportConfig($reportId);

// Si no hay configuración, usar MySQL por defecto
$source = ($reportConfig && isset($reportConfig['source'])) ? $reportConfig['source'] : 'mysql';

if ($source === 'sqlserver') {
    return get_data_from_sqlserver($reportId, $reportName, $folderName, $reportConfig);
} elseif ($source === 'mysql') {
    return get_data_from_mysql($reportId, $reportName, $folderName, $reportConfig);
} else {
    // Por defecto, intentar MySQL
    if ($reportConfig) {
        return get_data_from_mysql($reportId, $reportName, $folderName, $reportConfig);
    }
    return null;
}
}

/**
 * Obtener solo metadatos (columnas y filtros) sin datos para reportes SQL Server y MySQL
 * Los datos se cargarán vía AJAX con paginación
 */
function get_report_data_metadata($reportId) {
    require_once 'config.php';
    $reportConfig = getReportConfig($reportId);
    
    if (!$reportConfig || !in_array($reportConfig['source'], ['sqlserver', 'mysql'])) {
        return null;
    }
    
    $source = $reportConfig['source'];
    
    // Si es MySQL, usar la función específica de MySQL
    if ($source === 'mysql') {
        return get_mysql_report_metadata($reportId);
    }
    
    // Si es SQL Server, continuar con la lógica existente
    require_once __DIR__ . '/get_report_names.php';
    $reportNames = get_report_names();
    
    $reportName = $reportNames[$reportId]['name'];
    $folderName = $reportNames[$reportId]['folder'];
    
    try {
        // Conectar a SQL Server usando conexión centralizada
        require_once __DIR__ . '/connections.php';
        $sqlServerConfig = getSqlServerConfig();
        $conn = connectSqlServer();
        $tableColumns = getTableColumns($conn, $sqlServerConfig['database'], $reportConfig['table']);
        
        if (empty($tableColumns)) {
            sqlsrv_close($conn);
            throw new Exception("No se pudieron obtener las columnas de la tabla: " . $reportConfig['table'] . ". Verifica que la tabla exista en la base de datos " . $sqlServerConfig['database']);
        }
        
        $columnNames = array_column($tableColumns, 'name');
        
        // Obtener filtros actuales
        $filters = [];
        foreach ($columnNames as $column) {
            $filterValue = isset($_GET[$column]) ? trim($_GET[$column]) : '';
            $filters[$column] = $filterValue;
        }
        
        // Obtener valores únicos para filtros
        $uniqueOptions = [];
        foreach ($tableColumns as $col) {
            $colName = $col['name'];
            $dataType = strtolower($col['type']);
            
            if (stripos($dataType, 'int') === false && 
                stripos($dataType, 'float') === false && 
                stripos($dataType, 'decimal') === false &&
                stripos($dataType, 'date') === false &&
                stripos($dataType, 'time') === false &&
                stripos($colName, 'id') === false) {
                
                $uniqueValues = getUniqueValues($conn, $reportConfig['table'], $colName);
                if (!empty($uniqueValues)) {
                    $uniqueOptions[$colName] = $uniqueValues;
                }
            }
        }
        
        sqlsrv_close($conn);
        
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => [], // Datos vacíos, se cargarán vía AJAX
            'filterOptions' => $uniqueOptions,
            'currentFilters' => $filters,
            'columns' => $columnNames
        ];
        
    } catch (Exception $e) {
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => [],
            'filterOptions' => [],
            'currentFilters' => [],
            'columns' => [],
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Obtener datos desde SQL Server
 */
function get_data_from_sqlserver($reportId, $reportName, $folderName, $config) {
    try {
        // Conectar a SQL Server usando conexión centralizada
        require_once __DIR__ . '/connections.php';
        $sqlServerConfig = getSqlServerConfig();
        $conn = connectSqlServer();
        
        // Detectar columnas de la tabla si no están especificadas
        $tableColumns = getTableColumns($conn, $sqlServerConfig['database'], $config['table']);
        
        if (empty($tableColumns)) {
            sqlsrv_close($conn);
            throw new Exception("No se pudieron obtener las columnas de la tabla: " . $config['table'] . ". Verifica que la tabla exista en la base de datos " . $sqlServerConfig['database']);
        }
        
        $columnNames = array_column($tableColumns, 'name');
        
        // Obtener filtros dinámicos de los parámetros GET
        $filters = [];
        $whereConditions = [];
        $params = [];
        $paramIndex = 0;
        
        foreach ($columnNames as $column) {
            $filterValue = isset($_GET[$column]) ? trim($_GET[$column]) : '';
            if (!empty($filterValue)) {
                $filters[$column] = $filterValue;
                // Escapar nombre de columna con corchetes
                $whereConditions[] = "[$column] = ?";
                $params[] = $filterValue;
                $paramIndex++;
            } else {
                $filters[$column] = '';
            }
        }
        
        // Construir query con filtros
        $query = $config['query'];
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Ejecutar consulta
        $allData = executeQuery($conn, $query, $params);
        
        // Obtener valores únicos para filtros (solo de columnas que no sean numéricas o de fecha)
        $uniqueOptions = [];
        foreach ($tableColumns as $col) {
            $colName = $col['name'];
            $dataType = strtolower($col['type']);
            
            // Omitir tipos numéricos, fechas y IDs para filtros
            if (stripos($dataType, 'int') === false && 
                stripos($dataType, 'float') === false && 
                stripos($dataType, 'decimal') === false &&
                stripos($dataType, 'date') === false &&
                stripos($dataType, 'time') === false &&
                stripos($colName, 'id') === false) {
                
                $uniqueValues = getUniqueValues($conn, $config['table'], $colName);
                if (!empty($uniqueValues)) {
                    $uniqueOptions[$colName] = $uniqueValues;
                }
            }
        }
        
        sqlsrv_close($conn);
        
        // Retornar datos
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => $allData,
            'filterOptions' => $uniqueOptions,
            'currentFilters' => $filters,
            'columns' => $columnNames
        ];
        
    } catch (Exception $e) {
        // En caso de error, retornar estructura vacía con mensaje de error
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => [],
            'filterOptions' => [],
            'currentFilters' => [],
            'columns' => [],
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Obtener datos desde MySQL
 */
function get_data_from_mysql($reportId, $reportName, $folderName, $config) {
    try {
        // Conectar a MySQL usando conexión centralizada
        require_once __DIR__ . '/connections.php';
        $mysqlConfig = getMySQLConfig();
        $conn = connectMySQL();
        
        // Detectar columnas de la tabla si no están especificadas
        $tableColumns = getMySQLTableColumns($conn, $mysqlConfig['database'], $config['table']);
        
        if (empty($tableColumns)) {
            $conn = null;
            throw new Exception("No se pudieron obtener las columnas de la tabla: " . $config['table'] . ". Verifica que la tabla exista en la base de datos " . $mysqlConfig['database']);
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
                // Escapar nombre de columna con backticks
                $whereConditions[] = "`$column` = ?";
                $params[] = $filterValue;
            } else {
                $filters[$column] = '';
            }
        }
        
        // Construir query con filtros
        $query = $config['query'];
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Ejecutar consulta
        $allData = executeMySQLQuery($conn, $query, $params);
        
        // Obtener valores únicos para filtros (solo de columnas que no sean numéricas o de fecha)
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
                
                $uniqueValues = getMySQLUniqueValues($conn, $config['table'], $colName);
                if (!empty($uniqueValues)) {
                    $uniqueOptions[$colName] = $uniqueValues;
                }
            }
        }
        
        $conn = null;
        
        // Retornar datos
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => $allData,
            'filterOptions' => $uniqueOptions,
            'currentFilters' => $filters,
            'columns' => $columnNames
        ];
        
    } catch (Exception $e) {
        // En caso de error, retornar estructura vacía con mensaje de error
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => [],
            'filterOptions' => [],
            'currentFilters' => [],
            'columns' => [],
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Obtener solo metadatos (columnas y filtros) sin datos para reportes MySQL
 * Los datos se cargarán vía AJAX con paginación
 */
function get_mysql_report_metadata($reportId) {
    require_once 'config.php';
    $reportConfig = getReportConfig($reportId);
    
    if (!$reportConfig || $reportConfig['source'] !== 'mysql') {
        return null;
    }
    
    require_once __DIR__ . '/get_report_names.php';
    $reportNames = get_report_names();
    
    $reportName = $reportNames[$reportId]['name'];
    $folderName = $reportNames[$reportId]['folder'];
    
    try {
        // Conectar a MySQL usando conexión centralizada
        require_once __DIR__ . '/connections.php';
        $mysqlConfig = getMySQLConfig();
        $conn = connectMySQL();
        $tableColumns = getMySQLTableColumns($conn, $mysqlConfig['database'], $reportConfig['table']);
        
        if (empty($tableColumns)) {
            $conn = null;
            throw new Exception("No se pudieron obtener las columnas de la tabla: " . $reportConfig['table'] . ". Verifica que la tabla exista en la base de datos " . $mysqlConfig['database']);
        }
        
        $columnNames = array_column($tableColumns, 'name');
        
        // Obtener filtros actuales
        $filters = [];
        foreach ($columnNames as $column) {
            $filterValue = isset($_GET[$column]) ? trim($_GET[$column]) : '';
            $filters[$column] = $filterValue;
        }
        
        // Obtener valores únicos para filtros
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
                
                $uniqueValues = getMySQLUniqueValues($conn, $reportConfig['table'], $colName);
                if (!empty($uniqueValues)) {
                    $uniqueOptions[$colName] = $uniqueValues;
                }
            }
        }
        
        $conn = null;
        
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => [],
            'filterOptions' => $uniqueOptions,
            'currentFilters' => $filters,
            'columns' => $columnNames
        ];
        
    } catch (Exception $e) {
        return [
            'reportName' => $reportName,
            'folderName' => $folderName,
            'data' => [],
            'filterOptions' => [],
            'currentFilters' => [],
            'columns' => [],
            'error' => $e->getMessage()
        ];
    }
}

?>
