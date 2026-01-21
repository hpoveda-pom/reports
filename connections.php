<?php
/**
 * Configuración centralizada de conexiones a bases de datos
 * 
 * Soporta:
 * - MySQL: conexión estándar con usuario/contraseña
 * - SQL Server: Windows Authentication (sin usuario/contraseña)
 * - Snowflake: conexión con credenciales (requiere driver de Snowflake para PHP)
 * 
 * Uso:
 * - MySQL: connectMySQL() sin parámetros usa configuración por defecto
 * - SQL Server: connectSqlServer() sin parámetros usa Windows Authentication
 * - Snowflake: connectSnowflake() sin parámetros usa configuración por defecto
 */

// Configuración de conexión a MySQL
$mysqlConfig = [
    'host' => 'localhost',
    'database' => 'pom_reportes',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Configuración de conexión a Snowflake
$snowflakeConfig = [
    'account' => 'fkwugeu-qic97823',
    'user' => 'HPOVEDAPOMCR',
    'password' => 'ik5niBj5FiXN4px',
    'role' => 'ACCOUNTADMIN',
    'warehouse' => 'COMPUTE_WH',
    'database' => 'POM_TEST01',
    'schema' => 'RAW'
];

// Configuración de conexión a SQL Server (Windows Authentication)
// Nota: Si SQL Server tiene una instancia con nombre, usa: 'localhost\INSTANCENAME'
// Ejemplos: 'localhost\SQLEXPRESS', 'localhost\MSSQLSERVER', '.\\SQLEXPRESS', etc.
$sqlServerConfig = [
    'server' => 'localhost', // Cambiar según tu servidor (ej: 'localhost\SQLEXPRESS' o '.\\SQLEXPRESS')
    'database' => 'master', // Base de datos por defecto
    'connectionInfo' => [
        'TrustServerCertificate' => true,
        'CharacterSet' => 'UTF-8'
    ]
];

/**
 * Obtener configuración de conexión MySQL
 */
function getMySQLConfig() {
    global $mysqlConfig;
    return $mysqlConfig;
}

/**
 * Conectar a MySQL usando PDO
 */
function connectMySQL($host = null, $database = null, $username = null, $password = null, $charset = 'utf8mb4') {
    global $mysqlConfig;
    
    // Usar valores por defecto si no se proporcionan
    $host = $host ?? $mysqlConfig['host'];
    $database = $database ?? $mysqlConfig['database'];
    $username = $username ?? $mysqlConfig['username'];
    $password = $password ?? $mysqlConfig['password'];
    $charset = $charset ?? $mysqlConfig['charset'];
    
    try {
        $dsn = "mysql:host=$host;dbname=$database;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $conn = new PDO($dsn, $username, $password, $options);
        return $conn;
    } catch (PDOException $e) {
        $errorMsg = "Error de conexión a MySQL";
        $errorCode = $e->getCode();
        
        // Mensajes más descriptivos según el código de error
        if ($errorCode == 1045) {
            $errorMsg .= ": Usuario o contraseña incorrectos";
        } elseif ($errorCode == 1049) {
            $errorMsg .= ": La base de datos '$database' no existe";
        } elseif ($errorCode == 2002) {
            $errorMsg .= ": No se puede conectar al servidor MySQL en '$host'. Verifica que MySQL esté ejecutándose.";
        } else {
            $errorMsg .= ": " . $e->getMessage();
        }
        
        throw new Exception($errorMsg);
    }
}

/**
 * Ejecutar consulta MySQL y obtener resultados
 */
function executeMySQLQuery($conn, $query, $params = []) {
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al ejecutar consulta MySQL: " . $e->getMessage());
    }
}

/**
 * Obtener columnas de una tabla MySQL
 */
function getMySQLTableColumns($conn, $database = null, $table) {
    global $mysqlConfig;
    
    // Usar configuración centralizada si no se proporciona database
    if ($database === null) {
        $database = $mysqlConfig['database'];
    }
    
    try {
        $query = "SELECT COLUMN_NAME, DATA_TYPE 
                  FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                  ORDER BY ORDINAL_POSITION";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$database, $table]);
        $columns = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = [
                'name' => $row['COLUMN_NAME'],
                'type' => $row['DATA_TYPE']
            ];
        }
        
        return $columns;
    } catch (PDOException $e) {
        throw new Exception("Error al obtener columnas: " . $e->getMessage());
    }
}

/**
 * Obtener valores únicos de una columna MySQL para filtros
 */
function getMySQLUniqueValues($conn, $table, $column) {
    try {
        $query = "SELECT DISTINCT `$column` FROM `$table` WHERE `$column` IS NOT NULL ORDER BY `$column`";
        $stmt = $conn->query($query);
        $values = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $value = reset($row);
            if ($value !== null) {
                $values[] = $value;
            }
        }
        
        return $values;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Obtener configuración de conexión SQL Server
 */
function getSqlServerConfig() {
    global $sqlServerConfig;
    return $sqlServerConfig;
}

/**
 * Conectar a SQL Server usando Windows Authentication
 */
function connectSqlServer($server = null, $database = null, $connectionInfo = []) {
    global $sqlServerConfig;
    
    // Usar valores por defecto si no se proporcionan
    $server = $server ?? $sqlServerConfig['server'];
    $database = $database ?? $sqlServerConfig['database'];
    $connectionInfo = array_merge($sqlServerConfig['connectionInfo'], $connectionInfo);
    
    // Asegurar que se use Windows Authentication (no incluir UID/PWD)
    // Agregar la base de datos a connectionInfo
    $connectionInfo['Database'] = $database;
    
    $conn = sqlsrv_connect($server, $connectionInfo);
    
    if ($conn === false) {
        $errors = sqlsrv_errors();
        $errorMsg = "Error de conexión a SQL Server en '$server':\n";
        foreach ($errors as $error) {
            $errorMsg .= "- " . $error['message'] . "\n";
        }
        $errorMsg .= "\nSugerencias:\n";
        $errorMsg .= "1. Verifica que SQL Server esté ejecutándose\n";
        $errorMsg .= "2. Si tienes una instancia con nombre, usa: 'localhost\\INSTANCENAME' (ej: 'localhost\\SQLEXPRESS')\n";
        $errorMsg .= "3. Verifica que SQL Server permita conexiones remotas\n";
        $errorMsg .= "4. Asegúrate de que el servicio SQL Server Browser esté ejecutándose\n";
        $errorMsg .= "5. Verifica la configuración en connections.php (línea 37-44)";
        throw new Exception($errorMsg);
    }
    
    return $conn;
}

/**
 * Obtener configuración de conexión Snowflake
 */
function getSnowflakeConfig() {
    global $snowflakeConfig;
    return $snowflakeConfig;
}

/**
 * Conectar a Snowflake
 * Nota: Requiere el driver de Snowflake para PHP instalado
 */
function connectSnowflake($account = null, $user = null, $password = null, $role = null, $warehouse = null, $database = null, $schema = null) {
    global $snowflakeConfig;
    
    // Usar valores por defecto si no se proporcionan
    $account = $account ?? $snowflakeConfig['account'];
    $user = $user ?? $snowflakeConfig['user'];
    $password = $password ?? $snowflakeConfig['password'];
    $role = $role ?? $snowflakeConfig['role'];
    $warehouse = $warehouse ?? $snowflakeConfig['warehouse'];
    $database = $database ?? $snowflakeConfig['database'];
    $schema = $schema ?? $snowflakeConfig['schema'];
    
    try {
        // Construir DSN para Snowflake
        $dsn = sprintf(
            "snowflake:account=%s;warehouse=%s;database=%s;schema=%s;role=%s",
            $account,
            $warehouse,
            $database,
            $schema,
            $role
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $conn = new PDO($dsn, $user, $password, $options);
        return $conn;
    } catch (PDOException $e) {
        $errorMsg = "Error de conexión a Snowflake: " . $e->getMessage();
        throw new Exception($errorMsg);
    }
}

/**
 * Ejecutar consulta Snowflake y obtener resultados
 */
function executeSnowflakeQuery($conn, $query, $params = []) {
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error al ejecutar consulta Snowflake: " . $e->getMessage());
    }
}

/**
 * Obtener columnas de una tabla Snowflake
 */
function getSnowflakeTableColumns($conn, $database, $schema, $table) {
    try {
        $query = "SELECT COLUMN_NAME, DATA_TYPE 
                  FROM INFORMATION_SCHEMA.COLUMNS 
                  WHERE TABLE_CATALOG = ? AND TABLE_SCHEMA = ? AND TABLE_NAME = ?
                  ORDER BY ORDINAL_POSITION";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$database, $schema, $table]);
        $columns = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = [
                'name' => $row['COLUMN_NAME'],
                'type' => $row['DATA_TYPE']
            ];
        }
        
        return $columns;
    } catch (PDOException $e) {
        throw new Exception("Error al obtener columnas Snowflake: " . $e->getMessage());
    }
}

/**
 * Obtener valores únicos de una columna Snowflake para filtros
 */
function getSnowflakeUniqueValues($conn, $database, $schema, $table, $column) {
    try {
        $query = "SELECT DISTINCT \"$column\" FROM \"$database\".\"$schema\".\"$table\" WHERE \"$column\" IS NOT NULL ORDER BY \"$column\"";
        $stmt = $conn->query($query);
        $values = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $value = reset($row);
            if ($value !== null) {
                $values[] = $value;
            }
        }
        
        return $values;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Ejecutar consulta y obtener resultados SQL Server
 */
function executeQuery($conn, $query, $params = []) {
    $stmt = sqlsrv_query($conn, $query, $params);
    
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $errorMsg = "Error al ejecutar consulta: ";
        foreach ($errors as $error) {
            $errorMsg .= $error['message'] . " ";
        }
        throw new Exception($errorMsg);
    }
    
    $results = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Convertir tipos de datos SQL Server a formatos PHP legibles
        $processedRow = [];
        foreach ($row as $key => $value) {
            if ($value instanceof DateTime) {
                $processedRow[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_resource($value)) {
                $processedRow[$key] = stream_get_contents($value);
            } else {
                $processedRow[$key] = $value;
            }
        }
        $results[] = $processedRow;
    }
    
    sqlsrv_free_stmt($stmt);
    return $results;
}

/**
 * Obtener columnas de una tabla SQL Server
 */
function getTableColumns($conn, $database, $table) {
    $query = "SELECT COLUMN_NAME, DATA_TYPE 
              FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_CATALOG = ? AND TABLE_NAME = ?
              ORDER BY ORDINAL_POSITION";
    
    $params = [$database, $table];
    $stmt = sqlsrv_query($conn, $query, $params);
    
    if ($stmt === false) {
        return [];
    }
    
    $columns = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $columns[] = [
            'name' => $row['COLUMN_NAME'],
            'type' => $row['DATA_TYPE']
        ];
    }
    
    sqlsrv_free_stmt($stmt);
    return $columns;
}

/**
 * Obtener valores únicos de una columna SQL Server para filtros
 */
function getUniqueValues($conn, $table, $column) {
    $query = "SELECT DISTINCT [$column] FROM [$table] WHERE [$column] IS NOT NULL ORDER BY [$column]";
    $stmt = sqlsrv_query($conn, $query);
    
    if ($stmt === false) {
        return [];
    }
    
    $values = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $value = reset($row); // Obtener el primer valor del array
        if ($value !== null) {
            $values[] = $value;
        }
    }
    
    sqlsrv_free_stmt($stmt);
    return $values;
}
?>
