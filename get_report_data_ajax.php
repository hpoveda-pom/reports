<?php
/**
 * Endpoint AJAX para DataTables server-side processing
 * Maneja paginación, ordenamiento y filtrado del lado del servidor
 */
require_once 'config.php';
require_once 'get_report_data.php';

// Obtener parámetros de DataTables
$reportId = isset($_GET['report']) ? intval($_GET['report']) : 0;
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$orderColumn = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 0;
$orderDir = isset($_GET['order'][0]['dir']) ? strtoupper($_GET['order'][0]['dir']) : 'ASC';

if ($orderDir !== 'ASC' && $orderDir !== 'DESC') {
    $orderDir = 'ASC';
}

// Obtener parámetros de agrupación
$groupByColumns = [];
if (isset($_GET['group_by']) && is_array($_GET['group_by'])) {
    $groupByColumns = array_filter($_GET['group_by']); // Filtrar valores vacíos
}

$aggregations = [];
if (isset($_GET['agg_function']) && isset($_GET['agg_column'])) {
    $functions = is_array($_GET['agg_function']) ? $_GET['agg_function'] : [$_GET['agg_function']];
    $columns = is_array($_GET['agg_column']) ? $_GET['agg_column'] : [$_GET['agg_column']];
    
    for ($i = 0; $i < count($functions); $i++) {
        if (!empty($columns[$i])) {
            $aggregations[] = [
                'function' => strtoupper($functions[$i]),
                'column' => $columns[$i]
            ];
        }
    }
}

$isGrouped = !empty($groupByColumns) || !empty($aggregations);

// Obtener configuración del reporte
$reportConfig = getReportConfig($reportId);

if (!$reportConfig || !in_array($reportConfig['source'], ['sqlserver', 'mysql'])) {
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Reporte no configurado o fuente de datos no soportada (solo SQL Server y MySQL)'
    ]);
    exit;
}

$source = $reportConfig['source'];

try {
    if ($source === 'sqlserver') {
        // Conectar a SQL Server usando conexión centralizada
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
        $whereConditions = [];
        $params = [];
        
        foreach ($columnNames as $column) {
            $filterValue = isset($_GET[$column]) ? trim($_GET[$column]) : '';
            if (!empty($filterValue)) {
                $filters[$column] = $filterValue;
                // Escapar nombre de columna con corchetes
                $whereConditions[] = "[$column] = ?";
                $params[] = $filterValue;
            } else {
                $filters[$column] = '';
            }
        }
        
        // Construir WHERE clause
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Construir SELECT con agrupación si aplica
        if ($isGrouped) {
            // Construir lista de columnas para SELECT
            $selectColumns = [];
            
            // Agregar columnas de agrupación
            foreach ($groupByColumns as $groupCol) {
                if (in_array($groupCol, $columnNames)) {
                    $selectColumns[] = "[$groupCol]";
                }
            }
            
            // Agregar funciones de agregación
            foreach ($aggregations as $agg) {
                $aggFunc = $agg['function'];
                $aggCol = $agg['column'];
                if (in_array($aggCol, $columnNames)) {
                    $alias = $aggFunc . '_' . str_replace(' ', '_', $aggCol);
                    $selectColumns[] = "$aggFunc([$aggCol]) AS [$alias]";
                }
            }
            
            if (empty($selectColumns)) {
                throw new Exception("Debe especificar al menos una columna para agrupar o una función de agregación");
            }
            
            $selectClause = implode(", ", $selectColumns);
            $tableName = $reportConfig['table'];
            
            // Construir GROUP BY
            $groupByClause = " GROUP BY " . implode(", ", array_map(function($col) {
                return "[$col]";
            }, $groupByColumns));
            
            // Query base con agrupación
            $baseGroupedQuery = "SELECT $selectClause FROM [$tableName] $whereClause $groupByClause";
            
            // Contar total sin filtros de agrupación
            $countQueryAll = "SELECT COUNT(*) as total FROM [{$reportConfig['table']}]";
            $countResultAll = executeQuery($conn, $countQueryAll, []);
            $recordsTotal = isset($countResultAll[0]['total']) ? intval($countResultAll[0]['total']) : 0;
            
            // Contar total con filtros (usando subquery)
            $countQueryFiltered = "SELECT COUNT(*) as total FROM ($baseGroupedQuery) as grouped_data";
            $countResultFiltered = executeQuery($conn, $countQueryFiltered, $params);
            $recordsFiltered = isset($countResultFiltered[0]['total']) ? intval($countResultFiltered[0]['total']) : 0;
            
            // Construir ORDER BY (usar primera columna de agrupación o primera agregación)
            $orderByColumn = !empty($groupByColumns) ? $groupByColumns[0] : 
                            (!empty($aggregations) ? $aggregations[0]['function'] . '_' . str_replace(' ', '_', $aggregations[0]['column']) : $columnNames[0]);
            $orderBy = " ORDER BY [$orderByColumn] $orderDir";
            
            // Construir query paginada
            $pagedQuery = "SELECT * FROM ($baseGroupedQuery) as grouped $orderBy 
                           OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
            
            // Ejecutar consulta paginada
            $data = executeQuery($conn, $pagedQuery, $params);
            
            // Construir lista de columnas para formateo (columnas de agrupación + agregaciones)
            $displayColumns = $groupByColumns;
            foreach ($aggregations as $agg) {
                $displayColumns[] = $agg['function'] . '_' . str_replace(' ', '_', $agg['column']);
            }
            
            // Formatear datos para DataTables
            $formattedData = [];
            foreach ($data as $row) {
                $formattedRow = [];
                foreach ($displayColumns as $column) {
                    $formattedRow[] = isset($row[$column]) ? $row[$column] : '';
                }
                $formattedData[] = $formattedRow;
            }
            
            // Actualizar columnNames para que DataTables muestre las columnas correctas
            // Formatear nombres de columnas para mostrar
            $formattedColumnNames = [];
            foreach ($displayColumns as $col) {
                // Si es una función de agregación, formatear el nombre
                if (strpos($col, '_') !== false) {
                    $parts = explode('_', $col, 2);
                    $func = $parts[0];
                    $colName = str_replace('_', ' ', $parts[1]);
                    $funcNames = ['COUNT' => 'Contar', 'SUM' => 'Suma', 'AVG' => 'Promedio', 'MIN' => 'Mínimo', 'MAX' => 'Máximo'];
                    $funcName = isset($funcNames[$func]) ? $funcNames[$func] : $func;
                    $formattedColumnNames[] = ucwords($funcName . ' ' . $colName);
                } else {
                    $formattedColumnNames[] = ucwords(str_replace('_', ' ', $col));
                }
            }
            $columnNames = $formattedColumnNames;
            
        } else {
            // Query normal sin agrupación
            // Obtener total de registros sin filtros
            $countQueryAll = "SELECT COUNT(*) as total FROM [{$reportConfig['table']}]";
            $countResultAll = executeQuery($conn, $countQueryAll, []);
            $recordsTotal = isset($countResultAll[0]['total']) ? intval($countResultAll[0]['total']) : 0;
            
            // Obtener total de registros con filtros
            $countQueryFiltered = "SELECT COUNT(*) as total FROM [{$reportConfig['table']}] $whereClause";
            $countResultFiltered = executeQuery($conn, $countQueryFiltered, $params);
            $recordsFiltered = isset($countResultFiltered[0]['total']) ? intval($countResultFiltered[0]['total']) : 0;
            
            // Construir ORDER BY
            $orderByColumn = isset($columnNames[$orderColumn]) ? $columnNames[$orderColumn] : $columnNames[0];
            $orderBy = " ORDER BY [$orderByColumn] $orderDir";
            
            // Construir query paginada usando OFFSET/FETCH (SQL Server 2012+)
            $tableName = $reportConfig['table'];
            $pagedQuery = "SELECT * FROM [$tableName] $whereClause $orderBy 
                           OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
            
            // Ejecutar consulta paginada
            $data = executeQuery($conn, $pagedQuery, $params);
            
            // Formatear datos para DataTables (array de arrays en lugar de asociativo)
            $formattedData = [];
            foreach ($data as $row) {
                $formattedRow = [];
                foreach ($columnNames as $column) {
                    $formattedRow[] = isset($row[$column]) ? $row[$column] : '';
                }
                $formattedData[] = $formattedRow;
            }
        }
        
        sqlsrv_close($conn);
        
    } elseif ($source === 'mysql') {
        // Conectar a MySQL usando conexión centralizada
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
                // Escapar nombre de columna con backticks
                $whereConditions[] = "`$column` = ?";
                $params[] = $filterValue;
            } else {
                $filters[$column] = '';
            }
        }
        
        // Construir WHERE clause para filtros
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = " WHERE " . implode(" AND ", $whereConditions);
        }
        
        // Usar la consulta base del reporte si está configurada
        $baseQuery = isset($reportConfig['query']) ? $reportConfig['query'] : "SELECT * FROM `{$reportConfig['table']}`";
        $queryUpper = strtoupper(trim($baseQuery));
        $hasWhereInBase = strpos($queryUpper, 'WHERE') !== false;
        
        // Construir SELECT con agrupación si aplica
        if ($isGrouped) {
            // Construir lista de columnas para SELECT
            $selectColumns = [];
            
            // Agregar columnas de agrupación
            foreach ($groupByColumns as $groupCol) {
                if (in_array($groupCol, $columnNames)) {
                    $selectColumns[] = "`$groupCol`";
                }
            }
            
            // Agregar funciones de agregación
            foreach ($aggregations as $agg) {
                $aggFunc = $agg['function'];
                $aggCol = $agg['column'];
                if (in_array($aggCol, $columnNames)) {
                    $alias = $aggFunc . '_' . str_replace(' ', '_', $aggCol);
                    $selectColumns[] = "$aggFunc(`$aggCol`) AS `$alias`";
                }
            }
            
            if (empty($selectColumns)) {
                throw new Exception("Debe especificar al menos una columna para agrupar o una función de agregación");
            }
            
            $selectClause = implode(", ", $selectColumns);
            $tableName = $reportConfig['table'];
            
            // Construir GROUP BY
            $groupByClause = " GROUP BY " . implode(", ", array_map(function($col) {
                return "`$col`";
            }, $groupByColumns));
            
            // Query base con agrupación
            $baseGroupedQuery = "SELECT $selectClause FROM `$tableName` $whereClause $groupByClause";
            
            // Contar total sin filtros de agrupación
            $countQueryAll = "SELECT COUNT(*) as total FROM `{$reportConfig['table']}`";
            $countResultAll = executeMySQLQuery($conn, $countQueryAll, []);
            $recordsTotal = isset($countResultAll[0]['total']) ? intval($countResultAll[0]['total']) : 0;
            
            // Contar total con filtros (usando subquery)
            $countQueryFiltered = "SELECT COUNT(*) as total FROM ($baseGroupedQuery) as grouped_data";
            $countResultFiltered = executeMySQLQuery($conn, $countQueryFiltered, $params);
            $recordsFiltered = isset($countResultFiltered[0]['total']) ? intval($countResultFiltered[0]['total']) : 0;
            
            // Construir ORDER BY (usar primera columna de agrupación o primera agregación)
            $orderByColumn = !empty($groupByColumns) ? $groupByColumns[0] : 
                            (!empty($aggregations) ? $aggregations[0]['function'] . '_' . str_replace(' ', '_', $aggregations[0]['column']) : $columnNames[0]);
            $orderBy = " ORDER BY `$orderByColumn` $orderDir";
            
            // Construir query paginada
            $pagedQuery = "SELECT * FROM ($baseGroupedQuery) as grouped $orderBy LIMIT $length OFFSET $start";
            
            // Ejecutar consulta paginada
            $data = executeMySQLQuery($conn, $pagedQuery, $params);
            
            // Construir lista de columnas para formateo (columnas de agrupación + agregaciones)
            $displayColumns = $groupByColumns;
            foreach ($aggregations as $agg) {
                $displayColumns[] = $agg['function'] . '_' . str_replace(' ', '_', $agg['column']);
            }
            
            // Formatear datos para DataTables
            $formattedData = [];
            foreach ($data as $row) {
                $formattedRow = [];
                foreach ($displayColumns as $column) {
                    $formattedRow[] = isset($row[$column]) ? $row[$column] : '';
                }
                $formattedData[] = $formattedRow;
            }
            
            // Actualizar columnNames para que DataTables muestre las columnas correctas
            // Formatear nombres de columnas para mostrar
            $formattedColumnNames = [];
            foreach ($displayColumns as $col) {
                // Si es una función de agregación, formatear el nombre
                if (strpos($col, '_') !== false) {
                    $parts = explode('_', $col, 2);
                    $func = $parts[0];
                    $colName = str_replace('_', ' ', $parts[1]);
                    $funcNames = ['COUNT' => 'Contar', 'SUM' => 'Suma', 'AVG' => 'Promedio', 'MIN' => 'Mínimo', 'MAX' => 'Máximo'];
                    $funcName = isset($funcNames[$func]) ? $funcNames[$func] : $func;
                    $formattedColumnNames[] = ucwords($funcName . ' ' . $colName);
                } else {
                    $formattedColumnNames[] = ucwords(str_replace('_', ' ', $col));
                }
            }
            $columnNames = $formattedColumnNames;
            
        } else {
            // Query normal sin agrupación
            // Construir query para contar total sin filtros
            if ($hasWhereInBase) {
                $countQueryAll = "SELECT COUNT(*) as total FROM ($baseQuery) as base_query";
            } else {
                $countQueryAll = "SELECT COUNT(*) as total FROM `{$reportConfig['table']}`";
            }
            
            $countResultAll = executeMySQLQuery($conn, $countQueryAll, []);
            $recordsTotal = isset($countResultAll[0]['total']) ? intval($countResultAll[0]['total']) : 0;
            
            // Construir query para contar con filtros
            if ($hasWhereInBase && !empty($whereClause)) {
                $countQueryFiltered = "SELECT COUNT(*) as total FROM ($baseQuery" . (empty($whereClause) ? '' : ' AND ' . substr($whereClause, 7)) . ") as filtered_query";
            } elseif (!empty($whereClause)) {
                $countQueryFiltered = "SELECT COUNT(*) as total FROM ($baseQuery $whereClause) as filtered_query";
            } else {
                $countQueryFiltered = $countQueryAll;
            }
            
            $countResultFiltered = executeMySQLQuery($conn, $countQueryFiltered, $params);
            $recordsFiltered = isset($countResultFiltered[0]['total']) ? intval($countResultFiltered[0]['total']) : 0;
            
            // Construir ORDER BY
            $orderByColumn = isset($columnNames[$orderColumn]) ? $columnNames[$orderColumn] : $columnNames[0];
            $orderBy = " ORDER BY `$orderByColumn` $orderDir";
            
            // Construir query completa con filtros
            $fullQuery = $baseQuery;
            if (!empty($whereConditions)) {
                if ($hasWhereInBase) {
                    $fullQuery .= " AND " . implode(" AND ", $whereConditions);
                } else {
                    $fullQuery .= " WHERE " . implode(" AND ", $whereConditions);
                }
            }
            
            // Construir query paginada usando LIMIT/OFFSET (MySQL)
            $pagedQuery = "($fullQuery) $orderBy LIMIT $length OFFSET $start";
            
            // Ejecutar consulta paginada
            $data = executeMySQLQuery($conn, $pagedQuery, $params);
            
            // Formatear datos para DataTables (array de arrays en lugar de asociativo)
            $formattedData = [];
            foreach ($data as $row) {
                $formattedRow = [];
                foreach ($columnNames as $column) {
                    $formattedRow[] = isset($row[$column]) ? $row[$column] : '';
                }
                $formattedData[] = $formattedRow;
            }
        }
        
        $conn = null;
    }
    
    // Retornar respuesta JSON para DataTables
    $response = [
        'draw' => $draw,
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $formattedData
    ];
    
    // Solo incluir columnas si hay agrupación (para actualizar encabezados)
    if ($isGrouped && isset($formattedColumnNames)) {
        $response['columns'] = $formattedColumnNames;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // En caso de error, retornar estructura vacía con mensaje de error
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
